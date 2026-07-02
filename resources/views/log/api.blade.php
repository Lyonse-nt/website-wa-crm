@extends('layouts.app', ['header' => 'Log API'])

@section('content')
<div class="space-y-6">
    <div>
        <h3 class="text-lg font-medium text-gray-900">Log API WhatsApp</h3>
        <p class="text-sm text-gray-600 mt-1">Monitoring semua request & response ke WhatsApp API</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            @forelse($logApis as $log)
                <div class="border-b border-gray-200 p-6 hover:bg-gray-50">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $log->status_http >= 200 && $log->status_http < 300 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $log->status_http ?? 'N/A' }}
                                </span>
                                <span class="text-sm font-mono text-gray-600">POST</span>
                                <span class="text-sm text-gray-500">{{ $log->created_at->format('d M Y H:i:s') }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-900 mb-1">{{ $log->endpoint }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Request -->
                        <div>
                            <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Request</h4>
                            <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                                <pre class="text-xs text-green-400 font-mono">{{ json_encode($log->request, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                        </div>

                        <!-- Response -->
                        <div>
                            <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Response</h4>
                            <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                                <pre class="text-xs text-blue-400 font-mono">{{ json_encode($log->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500">
                    Belum ada log API
                </div>
            @endforelse
        </div>
    </div>

    @if($logApis->hasPages())
        <div class="mt-6">
            {{ $logApis->links() }}
        </div>
    @endif
</div>
@endsection
