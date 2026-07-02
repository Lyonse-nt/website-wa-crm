@extends('layouts.app', ['header' => 'Percakapan'])

@section('content')
<div class="h-[calc(100vh-200px)] flex bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <!-- Sidebar Percakapan -->
    <div class="w-80 border-r border-gray-200 flex flex-col">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">Percakapan</h3>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            @forelse($percakapans as $percakapan)
                <a href="{{ route('percakapan.show', $percakapan) }}" 
                    class="block px-4 py-3 border-b border-gray-100 hover:bg-gray-50 {{ $selectedPercakapan && $selectedPercakapan->id === $percakapan->id ? 'bg-green-50' : '' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $percakapan->kontak->nama }}
                            </p>
                            <p class="text-xs text-gray-600 truncate mt-1">
                                {{ $percakapan->kontak->nomor_whatsapp }}
                            </p>
                            <p class="text-xs text-gray-500 truncate mt-1">
                                {{ Str::limit($percakapan->pesan_terakhir, 40) }}
                            </p>
                        </div>
                        <div class="ml-2 flex-shrink-0">
                            <p class="text-xs text-gray-400">
                                {{ $percakapan->waktu_pesan_terakhir?->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-4 py-8 text-center text-gray-500 text-sm">
                    Belum ada percakapan
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
        @if($selectedPercakapan)
            <!-- Chat Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $selectedPercakapan->kontak->nama }}</h3>
                        <p class="text-xs text-gray-600 mt-1">{{ $selectedPercakapan->kontak->nomor_whatsapp }}</p>
                    </div>
                    <a href="{{ route('kontak.show', $selectedPercakapan->kontak) }}" 
                        class="text-sm text-green-600 hover:text-green-700">
                        Lihat Detail
                    </a>
                </div>
            </div>

            <!-- Messages -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
                @forelse($selectedPercakapan->pesans as $pesan)
                    <div class="flex {{ $pesan->arah_pesan === 'keluar' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-md">
                            <div class="rounded-lg px-4 py-2 {{ $pesan->arah_pesan === 'keluar' ? 'bg-green-500 text-white' : 'bg-white border border-gray-200 text-gray-900' }}">
                                <p class="text-sm whitespace-pre-wrap break-words">{{ $pesan->isi_pesan }}</p>
                            </div>
                            <div class="flex items-center gap-2 mt-1 {{ $pesan->arah_pesan === 'keluar' ? 'justify-end' : 'justify-start' }}">
                                <p class="text-xs text-gray-500">
                                    {{ $pesan->created_at->format('H:i') }}
                                </p>
                                @if($pesan->arah_pesan === 'keluar')
                                    <span class="text-xs">
                                        @if($pesan->status === 'sent')
                                            <span class="text-green-600">✓✓</span>
                                        @elseif($pesan->status === 'failed')
                                            <span class="text-red-600">✗</span>
                                        @else
                                            <span class="text-gray-400">✓</span>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500">Belum ada pesan</p>
                    </div>
                @endforelse
            </div>

            <!-- Input Area -->
            <div class="px-6 py-4 border-t border-gray-200 bg-white">
                <form action="{{ route('pesan.send') }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="hidden" name="nomor_whatsapp" value="{{ $selectedPercakapan->kontak->nomor_whatsapp }}">
                    <textarea name="pesan" rows="1" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        placeholder="Ketik pesan..." required></textarea>
                    <button type="submit" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim
                    </button>
                </form>
            </div>
        @else
            <!-- Empty State -->
            <div class="flex-1 flex items-center justify-center text-gray-500">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-lg font-medium">Pilih percakapan</p>
                    <p class="text-sm mt-1">Pilih percakapan dari sidebar untuk melihat chat</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
