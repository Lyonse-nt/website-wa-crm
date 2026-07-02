@extends('layouts.app', ['header' => 'Log Webhook'])

@section('content')
<div class="space-y-6">
    <div>
        <h3 class="text-lg font-medium text-gray-900">Log Webhook</h3>
        <p class="text-sm text-gray-600 mt-1">Semua payload yang diterima dari Meta WhatsApp</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @forelse($logWebhooks as $log)
            <div class="border-b border-gray-200 p-6 hover:bg-gray-50">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                            WEBHOOK
                        </span>
                        <span class="text-sm text-gray-600">{{ $log->created_at->format('d M Y H:i:s') }}</span>
                    </div>
                    <span class="text-xs text-gray-500">ID: {{ $log->id }}</span>
                </div>

                <div>
                    <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Payload</h4>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-yellow-400 font-mono">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>

                @if(isset($log->payload['entry'][0]['changes'][0]['value']['messages'][0]))
                    @php
                        $message = $log->payload['entry'][0]['changes'][0]['value']['messages'][0];
                    @endphp
                    <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                        <h5 class="text-xs font-semibold text-green-900 mb-2">Pesan Terdeteksi</h5>
                        <div class="text-sm text-green-800 space-y-1">
                            <p><span class="font-medium">From:</span> {{ $message['from'] ?? 'N/A' }}</p>
                            <p><span class="font-medium">Type:</span> {{ $message['type'] ?? 'N/A' }}</p>
                            @if(isset($message['text']['body']))
                                <p><span class="font-medium">Message:</span> {{ $message['text']['body'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="px-6 py-12 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p>Belum ada webhook diterima</p>
                <p class="text-xs mt-2">Pastikan webhook sudah dikonfigurasi di Meta Developer Console</p>
            </div>
        @endforelse
    </div>

    @if($logWebhooks->hasPages())
        <div class="mt-6">
            {{ $logWebhooks->links() }}
        </div>
    @endif
</div>
@endsection
