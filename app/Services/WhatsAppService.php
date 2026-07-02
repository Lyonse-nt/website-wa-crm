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
    private $provider;

    public function __construct()
    {
        $this->provider = config('services.whatsapp.provider', 'fonnte');
        $this->token = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->version = config('services.whatsapp.version', 'v18.0');
        
        if ($this->provider === 'fonnte') {
            $this->baseUrl = 'https://api.fonnte.com';
        } else {
            $this->baseUrl = "https://graph.facebook.com/{$this->version}/{$this->phoneNumberId}";
        }
    }

    public function sendText(string $to, string $message)
    {
        if ($this->provider === 'fonnte') {
            return $this->sendFonnteMessage($to, $message);
        }
        
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
    
    private function sendFonnteMessage(string $to, string $message, ?string $imageUrl = null, ?string $fileUrl = null)
    {
        $endpoint = "{$this->baseUrl}/send";
        
        $payload = [
            'target' => $this->formatPhoneNumber($to),
            'message' => $message,
            'countryCode' => '62',
        ];
        
        if ($imageUrl) {
            $payload['url'] = $imageUrl;
        }
        
        if ($fileUrl) {
            $payload['file'] = $fileUrl;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($endpoint, $payload);

            $this->logApi($endpoint, $payload, $response->json(), $response->status());

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            $this->logApi($endpoint, $payload, ['error' => $e->getMessage()], 500);
            
            Log::error('Fonnte API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'data' => ['error' => $e->getMessage()],
                'status' => 500
            ];
        }
    }

    public function sendImage(string $to, string $imageUrl, ?string $caption = null)
    {
        if ($this->provider === 'fonnte') {
            return $this->sendFonnteMessage($to, $caption ?? '', $imageUrl);
        }
        
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
