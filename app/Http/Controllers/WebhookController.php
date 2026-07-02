<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use App\Models\Percakapan;
use App\Models\Pesan;
use App\Models\LogWebhook;
use App\Models\ChatbotSetting;
use App\Models\ChatbotMenu;
use App\Models\ChatbotKeyword;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WebhookController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = config('services.whatsapp.webhook_verify_token');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    public function handle(Request $request)
    {
        $payload = $request->all();
        LogWebhook::create(['payload' => $payload]);

        $provider = config('services.whatsapp.provider', 'fonnte');

        if ($provider === 'fonnte') {
            return $this->handleFonnteWebhook($payload);
        }

        // Meta/Facebook webhook format
        if (!isset($payload['entry'][0]['changes'][0]['value']['messages'][0])) {
            return response()->json(['status' => 'ok']);
        }

        $message = $payload['entry'][0]['changes'][0]['value']['messages'][0];
        $from = $message['from'];
        $messageBody = $message['text']['body'] ?? '';
        $messageId = $message['id'];

        $kontak = Kontak::firstOrCreate(
            ['nomor_whatsapp' => $from],
            ['nama' => $from]
        );

        $percakapan = Percakapan::firstOrCreate(
            ['kontak_id' => $kontak->id]
        );

        Pesan::create([
            'percakapan_id' => $percakapan->id,
            'arah_pesan' => 'masuk',
            'jenis_pesan' => 'text',
            'isi_pesan' => $messageBody,
            'whatsapp_message_id' => $messageId,
            'status' => 'delivered',
            'raw_response' => $message
        ]);

        $percakapan->update([
            'pesan_terakhir' => $messageBody,
            'waktu_pesan_terakhir' => now()
        ]);

        $this->autoReply($from, $messageBody);

        return response()->json(['status' => 'ok']);
    }

    private function handleFonnteWebhook(array $payload)
    {
        // Log payload untuk debugging
        Log::info('Fonnte Webhook Received:', $payload);

        // Fonnte webhook format: device, sender, message, etc.
        if (!isset($payload['message'])) {
            return response()->json(['status' => 'ok']);
        }

        $from = $payload['sender'] ?? $payload['phone'] ?? '';
        $messageBody = $payload['message'] ?? '';
        $messageId = $payload['messageId'] ?? uniqid();
        $isGroup = isset($payload['isGroup']) && $payload['isGroup'] == true;

        // Skip pesan dari bot sendiri
        if (isset($payload['fromMe']) && $payload['fromMe'] == true) {
            Log::info('Skipped: Message from self', ['from' => $from]);
            return response()->json(['status' => 'ok']);
        }

        // IMPORTANT: Cek apakah pesan ini sudah pernah diproses (deduplikasi)
        $existingMessage = Pesan::where('whatsapp_message_id', $messageId)->first();
        if ($existingMessage) {
            Log::info('Skipped: Duplicate message (already processed)', ['messageId' => $messageId, 'from' => $from]);
            return response()->json(['status' => 'ok']);
        }

        // Untuk pesan grup, hanya proses kalau ada mention atau pesan khusus
        if ($isGroup || str_contains($from, '@g.us')) {
            Log::info('Group message detected', ['from' => $from, 'message' => $messageBody]);
            
            // Hanya balas kalau ada mention (@) atau keyword khusus
            // Atau bisa skip sepenuhnya kalau tidak mau bot aktif di grup
            // Untuk sekarang, kita skip auto-reply di grup tapi tetap log pesan
            
            // Simpan pesan grup ke database (optional, bisa di-comment kalau tidak perlu)
            $kontak = Kontak::firstOrCreate(
                ['nomor_whatsapp' => $from],
                ['nama' => $payload['pushname'] ?? 'Group: ' . $from]
            );

            $percakapan = Percakapan::firstOrCreate(
                ['kontak_id' => $kontak->id]
            );

            Pesan::create([
                'percakapan_id' => $percakapan->id,
                'arah_pesan' => 'masuk',
                'jenis_pesan' => 'text',
                'isi_pesan' => $messageBody,
                'whatsapp_message_id' => $messageId,
                'status' => 'delivered',
                'raw_response' => $payload
            ]);

            $percakapan->update([
                'pesan_terakhir' => $messageBody,
                'waktu_pesan_terakhir' => now()
            ]);

            // TESTING MODE: Balas semua pesan grup
            Log::info('Attempting auto-reply to group', ['from' => $from, 'message' => substr($messageBody, 0, 50)]);
            $this->autoReply($from, $messageBody, true);
            
            return response()->json(['status' => 'ok']);
        }

        Log::info('Processing individual message', ['from' => $from, 'message' => $messageBody]);

        $kontak = Kontak::firstOrCreate(
            ['nomor_whatsapp' => $from],
            ['nama' => $payload['pushname'] ?? $from]
        );

        $percakapan = Percakapan::firstOrCreate(
            ['kontak_id' => $kontak->id]
        );

        Pesan::create([
            'percakapan_id' => $percakapan->id,
            'arah_pesan' => 'masuk',
            'jenis_pesan' => 'text',
            'isi_pesan' => $messageBody,
            'whatsapp_message_id' => $messageId,
            'status' => 'delivered',
            'raw_response' => $payload
        ]);

        $percakapan->update([
            'pesan_terakhir' => $messageBody,
            'waktu_pesan_terakhir' => now()
        ]);

        $this->autoReply($from, $messageBody, false); // false = not group

        return response()->json(['status' => 'ok']);
    }

    private function autoReply(string $from, string $message, bool $isGroup = false)
    {
        // Rate limiting: Cegah spam reply dalam 3 detik
        $cacheKey = "autoreply:{$from}:" . md5($message);
        if (Cache::has($cacheKey)) {
            Log::info('Rate limited: Auto-reply blocked (duplicate in last 3 seconds)', [
                'from' => $from,
                'message' => substr($message, 0, 50)
            ]);
            return;
        }
        
        // Set cache untuk 3 detik
        Cache::put($cacheKey, true, 3);

        // Cek apakah chatbot aktif
        $chatbotEnabled = ChatbotSetting::getSetting('chatbot_enabled', 'true') === 'true';
        if (!$chatbotEnabled) {
            Log::info('Chatbot disabled, skipping auto-reply');
            return;
        }

        // Untuk pesan grup, hanya balas kalau ada mention atau keyword tertentu
        if ($isGroup) {
            Log::info('Processing group message for auto-reply', ['from' => $from, 'message' => substr($message, 0, 50)]);
            
            $messageLower = strtolower(trim($message));
            
            // TESTING: Balas semua pesan di grup (hapus filter)
            // Cek keyword-based reply untuk grup
            $keyword = ChatbotKeyword::findByMessage($message);
            if ($keyword) {
                Log::info('Found keyword match for group', ['keyword' => $keyword->keywords]);
                $this->whatsappService->sendText($from, $keyword->reply_message);
                return;
            }
            
            // Cek apakah pesan adalah nomor menu
            if (is_numeric($messageLower)) {
                $menu = ChatbotMenu::findByNumber((int)$messageLower);
                if ($menu) {
                    Log::info('Found menu match for group', ['menu_number' => $messageLower]);
                    $this->whatsappService->sendText($from, $menu->reply_message);
                    return;
                }
            }
            
            // TESTING: Kirim default reply untuk semua pesan grup yang tidak cocok
            $defaultReply = ChatbotSetting::getSetting('default_reply');
            if ($defaultReply) {
                Log::info('Sending default reply to group');
                $this->whatsappService->sendText($from, "Pesan grup diterima: " . substr($message, 0, 30));
                return;
            }
            
            Log::info('No reply sent to group (no matching keyword/menu/default)');
            return;
        }

        // Logic untuk chat personal (bukan grup)
        $replyAllMessages = ChatbotSetting::getSetting('reply_all_messages', 'true') === 'true';
        
        // Cek apakah pesan pertama dari nomor ini
        $isFirstMessage = Pesan::whereHas('percakapan.kontak', function ($query) use ($from) {
            $query->where('nomor_whatsapp', $from);
        })->count() === 1;

        Log::info('Auto-reply check', [
            'from' => $from,
            'isFirstMessage' => $isFirstMessage,
            'replyAllMessages' => $replyAllMessages
        ]);

        // Kirim welcome message untuk pesan pertama
        if ($isFirstMessage) {
            $welcomeMessage = ChatbotSetting::getSetting('welcome_message');
            if ($welcomeMessage) {
                Log::info('Sending welcome message', ['from' => $from]);
                $this->whatsappService->sendText($from, $welcomeMessage);
                return; // PENTING: Return setelah kirim welcome message
            }
        }

        // Jika tidak reply semua pesan dan bukan pesan pertama, skip
        if (!$replyAllMessages && !$isFirstMessage) {
            Log::info('Skip auto-reply: not first message and reply_all_messages is false');
            return;
        }

        $messageLower = strtolower(trim($message));

        // Cek apakah pesan adalah nomor menu (1, 2, 3, dll)
        if (is_numeric($messageLower)) {
            $menu = ChatbotMenu::findByNumber((int)$messageLower);
            if ($menu) {
                Log::info('Sending menu reply', ['from' => $from, 'menu_number' => $messageLower]);
                $this->whatsappService->sendText($from, $menu->reply_message);
                return; // PENTING: Return setelah kirim menu
            }
        }

        // Cek keyword-based reply
        $keyword = ChatbotKeyword::findByMessage($message);
        if ($keyword) {
            Log::info('Sending keyword reply', ['from' => $from, 'keyword' => $keyword->keywords]);
            $this->whatsappService->sendText($from, $keyword->reply_message);
            return; // PENTING: Return setelah kirim keyword reply
        }

        // Default reply
        $defaultReply = ChatbotSetting::getSetting('default_reply');
        if ($defaultReply) {
            Log::info('Sending default reply', ['from' => $from]);
            $this->whatsappService->sendText($from, $defaultReply);
        }
    }
}
