@extends('layouts.app', ['header' => 'Detail Kontak'])

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('kontak.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">{{ $kontak->nama }}</h3>
                <p class="text-gray-600 mt-1">{{ $kontak->nomor_whatsapp }}</p>
                <p class="text-sm text-gray-500 mt-2">Terdaftar {{ $kontak->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('kontak.edit', $kontak) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Edit
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-900">Riwayat Percakapan</h4>
        </div>
        <div class="p-6">
            @forelse($kontak->percakapans as $percakapan)
                <div class="mb-6 last:mb-0">
                    <div class="text-sm text-gray-500 mb-3">
                        {{ $percakapan->waktu_pesan_terakhir?->format('d M Y H:i') }}
                    </div>
                    <div class="space-y-3">
                        @foreach($percakapan->pesans as $pesan)
                            <div class="flex {{ $pesan->arah_pesan === 'keluar' ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-md {{ $pesan->arah_pesan === 'keluar' ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg px-4 py-2">
                                    <p class="text-sm text-gray-900">{{ $pesan->isi_pesan }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $pesan->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    Belum ada percakapan dengan kontak ini
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
