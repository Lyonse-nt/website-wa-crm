@extends('layouts.app', ['header' => 'Kirim Pesan'])

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Kirim Pesan WhatsApp</h3>
            <p class="text-sm text-gray-600 mt-1">Kirim pesan ke nomor WhatsApp menggunakan API</p>
        </div>

        <form action="{{ route('pesan.send') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="nomor_whatsapp" class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp</label>
                <select name="nomor_whatsapp" id="nomor_whatsapp" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nomor_whatsapp') border-red-500 @enderror">
                    <option value="">Pilih kontak atau ketik manual</option>
                    @foreach($kontaks as $kontak)
                        <option value="{{ $kontak->nomor_whatsapp }}">{{ $kontak->nama }} - {{ $kontak->nomor_whatsapp }}</option>
                    @endforeach
                </select>
                <input type="text" name="nomor_manual" id="nomor_manual" placeholder="Atau ketik nomor manual: 08123456789" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent mt-2">
                @error('nomor_whatsapp')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Format: 08xxx atau 628xxx</p>
            </div>

            <div>
                <label for="pesan" class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                <textarea name="pesan" id="pesan" rows="6" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('pesan') border-red-500 @enderror" 
                    placeholder="Tulis pesan Anda di sini..." required>{{ old('pesan') }}</textarea>
                @error('pesan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Pastikan konfigurasi API sudah benar:</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-700">
                            <li>WHATSAPP_TOKEN sudah diisi di file .env</li>
                            <li>WHATSAPP_PHONE_NUMBER_ID sudah diisi</li>
                            <li>Nomor tujuan sudah terdaftar di WhatsApp</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim Pesan
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6">
        <h4 class="text-sm font-semibold text-gray-900 mb-3">Tips Penggunaan</h4>
        <ul class="text-sm text-gray-600 space-y-2">
            <li class="flex gap-2">
                <span class="text-green-600">✓</span>
                <span>Pilih dari daftar kontak atau ketik nomor manual</span>
            </li>
            <li class="flex gap-2">
                <span class="text-green-600">✓</span>
                <span>Nomor otomatis diformat ke format internasional (62xxx)</span>
            </li>
            <li class="flex gap-2">
                <span class="text-green-600">✓</span>
                <span>Cek Log API untuk melihat hasil pengiriman</span>
            </li>
        </ul>
    </div>
</div>

<script>
    document.getElementById('nomor_manual').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('nomor_whatsapp').value = '';
        }
    });

    document.getElementById('nomor_whatsapp').addEventListener('change', function() {
        if (this.value) {
            document.getElementById('nomor_manual').value = '';
        }
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        const select = document.getElementById('nomor_whatsapp');
        const manual = document.getElementById('nomor_manual');
        
        if (manual.value) {
            select.name = 'nomor_whatsapp_temp';
            manual.name = 'nomor_whatsapp';
        }
    });
</script>
@endsection
