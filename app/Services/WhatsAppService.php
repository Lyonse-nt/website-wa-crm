<?php

namespace App\Services;

use App\Models\LogApi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private $token;
    private $phoneNumberId;
    private $version;
    private $baseUrl;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->version = config('services.whatsapp.version', 'v18.0');
        $this->baseUrl = "https://graph.facebook.com/{$this->version}/{$this->phoneNumberId}";
    }

    public function sendText(string $to, string $message)
    {
        $endpoint = "{$this->baseUrl}/messages";
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $message
            ]
        ];

        return $this->sendRequest($endpoint, $payload);
    }

    public function sendImage(string $to, string $imageUrl, ?string $caption = null)
    {
        $endpoint = "{$this->baseUrl}/messages";
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'image',
            'image' => [
                'link' => $imageUrl,
                'caption' => $caption
            ]
        ];

        return $this->sendRequest($endpoint, $payload);
    }

    public function sendDocument(string $to, string $documentUrl, ?string $filename = null)
    {
        $endpoint = "{$this->baseUrl}/messages";
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'document',
            'document' => [
                'link' => $documentUrl,
                'filename' => $filename
            ]
        ];

        return $this->sendRequest($endpoint, $payload);
    }

    private function sendRequest(string $endpoint, array $payload)
    {
        try {
            $response = Http::withToken($this->token)
                ->post($endpoint, $payload);

            $this->logApi($endpoint, $payload, $response->json(), $response->status());

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            $this->logApi($endpoint, $payload, ['error' => $e->getMessage()], 500);
            
            Log::error('WhatsApp API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'data' => ['error' => $e->getMessage()],
                'status' => 500
            ];
        }
    }

    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    private function logApi(string $endpoint, array $request, array $response, int $status): void
    {
        LogApi::create([
            'endpoint' => $endpoint,
            'request' => $request,
            'response' => $response,
            'status_http' => $status
        ]);
    }
}
