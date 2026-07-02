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
        // Fonnte webhook format: device, sender, message, etc.
        if (!isset($payload['message'])) {
            return response()->json(['status' => 'ok']);
        }

        $from = $payload['sender'] ?? $payload['phone'] ?? '';
        $messageBody = $payload['message'] ?? '';
        $messageId = $payload['messageId'] ?? uniqid();

        // Skip pesan dari bot sendiri
        if (isset($payload['fromMe']) && $payload['fromMe'] == true) {
            return response()->json(['status' => 'ok']);
        }

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

        $this->autoReply($from, $messageBody);

        return response()->json(['status' => 'ok']);
    }

    private function autoReply(string $from, string $message)
    {
        // Cek apakah chatbot aktif
        $chatbotEnabled = ChatbotSetting::getSetting('chatbot_enabled', 'true') === 'true';
        if (!$chatbotEnabled) {
            return;
        }

        $replyAllMessages = ChatbotSetting::getSetting('reply_all_messages', 'true') === 'true';
        
        // Cek apakah pesan pertama dari nomor ini
        $isFirstMessage = Pesan::whereHas('percakapan.kontak', function ($query) use ($from) {
            $query->where('nomor_whatsapp', $from);
        })->count() === 1;

        // Kirim welcome message untuk pesan pertama
        if ($isFirstMessage) {
            $welcomeMessage = ChatbotSetting::getSetting('welcome_message');
            if ($welcomeMessage) {
                $this->whatsappService->sendText($from, $welcomeMessage);
                return;
            }
        }

        // Jika tidak reply semua pesan dan bukan pesan pertama, skip
        if (!$replyAllMessages && !$isFirstMessage) {
            return;
        }

        $messageLower = strtolower(trim($message));

        // Cek apakah pesan adalah nomor menu (1, 2, 3, dll)
        if (is_numeric($messageLower)) {
            $menu = ChatbotMenu::findByNumber((int)$messageLower);
            if ($menu) {
                $this->whatsappService->sendText($from, $menu->reply_message);
                return;
            }
        }

        // Cek keyword-based reply
        $keyword = ChatbotKeyword::findByMessage($message);
        if ($keyword) {
            $this->whatsappService->sendText($from, $keyword->reply_message);
            return;
        }

        // Default reply
        $defaultReply = ChatbotSetting::getSetting('default_reply');
        if ($defaultReply) {
            $this->whatsappService->sendText($from, $defaultReply);
        }
    }
}
