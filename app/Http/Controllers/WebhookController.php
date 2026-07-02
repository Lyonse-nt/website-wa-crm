<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use App\Models\Percakapan;
use App\Models\Pesan;
use App\Models\LogWebhook;
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
        $message = strtolower(trim($message));

        $replies = [
            'halo' => "Halo juga 👋\nAda yang bisa kami bantu?",
            'menu' => "📋 Menu:\n1. Informasi\n2. Bantuan\n3. Kontak Admin",
            'info' => "ℹ️ Ini adalah sistem CRM WhatsApp otomatis.\nKami akan segera merespon pesan Anda.",
        ];

        if (isset($replies[$message])) {
            $this->whatsappService->sendText($from, $replies[$message]);
        }
    }
}
