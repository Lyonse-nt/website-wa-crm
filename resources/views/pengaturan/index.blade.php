@extends('layouts.app', ['header' => 'Pengaturan'])

@section('content')
<div class="max-w-4xl">
    <div class="space-y-6">
        <!-- WhatsApp API Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Konfigurasi WhatsApp API</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number ID</label>
                    <div class="flex items-center gap-3">
                        <input type="text" value="{{ $settings['phone_number_id'] ?? 'Belum dikonfigurasi' }}" 
                            class="flex-1 px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700" readonly>
                        <button onclick="copyToClipboard('{{ $settings['phone_number_id'] }}')" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            Copy
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">ID nomor telepon WhatsApp Business</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Webhook URL</label>
                    <div class="flex items-center gap-3">
                        <input type="text" value="{{ $settings['webhook_url'] }}" 
                            class="flex-1 px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700" readonly>
                        <button onclick="copyToClipboard('{{ $settings['webhook_url'] }}')" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            Copy
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">URL untuk menerima webhook dari Meta</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Verify Token</label>
                    <div class="flex items-center gap-3">
                        <input type="text" value="{{ $settings['verify_token'] ?? 'Belum dikonfigurasi' }}" 
                            class="flex-1 px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700" readonly>
                        <button onclick="copyToClipboard('{{ $settings['verify_token'] }}')" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            Copy
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Token untuk verifikasi webhook</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Version</label>
                    <input type="text" value="{{ $settings['api_version'] }}" 
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700" readonly>
                    <p class="mt-1 text-xs text-gray-500">Versi Graph API yang digunakan</p>
                </div>
            </div>
        </div>

        <!-- Setup Guide -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h4 class="font-semibold text-blue-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Panduan Setup Webhook
            </h4>
            <ol class="text-sm text-blue-900 space-y-3 list-decimal list-inside">
                <li>Buka <a href="https://developers.facebook.com" target="_blank" class="underline font-medium">Meta Developer Console</a></li>
                <li>Pilih aplikasi WhatsApp Business Anda</li>
                <li>Buka menu "Configuration" di sidebar WhatsApp</li>
                <li>Klik "Edit" pada Webhook section</li>
                <li>Masukkan <strong>Callback URL</strong> dan <strong>Verify Token</strong> dari atas</li>
                <li>Subscribe ke events: <code class="bg-blue-100 px-1 rounded">messages</code></li>
                <li>Save dan test webhook</li>
            </ol>
        </div>

        <!-- ngrok Guide -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h4 class="font-semibold text-yellow-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                Development dengan ngrok
            </h4>
            <p class="text-sm text-yellow-900 mb-3">Untuk development lokal, gunakan ngrok untuk expose webhook:</p>
            <div class="bg-yellow-100 rounded-lg p-4 space-y-2">
                <code class="text-sm text-yellow-900 block">$ ngrok http 8000</code>
                <p class="text-xs text-yellow-800 mt-2">Kemudian gunakan URL https://xxxxx.ngrok-free.app/webhook sebagai Callback URL</p>
            </div>
        </div>

        <!-- Environment Variables -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Environment Variables (.env)</h3>
            </div>
            <div class="p-6">
                <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-green-400 font-mono">WHATSAPP_TOKEN=your_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_random_token
WHATSAPP_VERSION=v18.0</pre>
                </div>
                <p class="mt-3 text-xs text-gray-600">Edit file <code class="bg-gray-100 px-2 py-1 rounded">.env</code> dan isi nilai-nilai di atas</p>
            </div>
        </div>

        <!-- Links -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Link Berguna</h3>
            <div class="space-y-2 text-sm">
                <a href="https://developers.facebook.com/docs/whatsapp/cloud-api" target="_blank" 
                    class="flex items-center gap-2 text-green-600 hover:text-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    WhatsApp Cloud API Documentation
                </a>
                <a href="https://developers.facebook.com" target="_blank" 
                    class="flex items-center gap-2 text-green-600 hover:text-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Meta Developer Console
                </a>
                <a href="https://ngrok.com" target="_blank" 
                    class="flex items-center gap-2 text-green-600 hover:text-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    ngrok - Secure tunnels to localhost
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied to clipboard!');
    });
}
</script>
@endsection
