@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Chatbot</h1>
        <p class="text-gray-600 mt-1">Kelola auto-reply dan pengaturan chatbot WhatsApp</p>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Settings Umum -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Pengaturan Umum</h2>
        </div>
        <form action="{{ route('chatbot.settings.update') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="chatbot_enabled" value="1" {{ $chatbotEnabled ? 'checked' : '' }} class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Aktifkan Chatbot</span>
                    </label>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="reply_all_messages" value="1" {{ $replyAllMessages ? 'checked' : '' }} class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Balas Semua Pesan</span>
                    </label>
                    <p class="text-xs text-gray-500 ml-6 mt-1">Jika dinonaktifkan, hanya pesan pertama yang mendapat welcome message</p>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- Welcome Message -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Pesan Sambutan (Welcome Message)</h2>
            <p class="text-sm text-gray-600 mt-1">Pesan ini akan dikirim otomatis untuk pesan pertama dari customer baru</p>
        </div>
        <form action="{{ route('chatbot.settings.update') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="chatbot_enabled" value="{{ $chatbotEnabled ? '1' : '0' }}">
            <input type="hidden" name="reply_all_messages" value="{{ $replyAllMessages ? '1' : '0' }}">
            <textarea name="welcome_message" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Contoh:&#10;👋 Halo! Selamat datang di [Nama Bisnis]&#10;&#10;Silakan pilih menu:&#10;1️⃣ Informasi Produk&#10;2️⃣ Harga&#10;3️⃣ Hubungi Admin">{{ $welcomeMessage }}</textarea>
            <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Simpan Welcome Message
            </button>
        </form>
    </div>

    <!-- Menu Bot -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Menu Bot</h2>
            <p class="text-sm text-gray-600 mt-1">Customer bisa pilih menu dengan ketik angka (1, 2, 3, dll)</p>
        </div>
        <div class="p-6">
            @if($menus->count() > 0)
            <div class="space-y-3 mb-6">
                @foreach($menus as $menu)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded">Menu {{ $menu->menu_number }}</span>
                                <span class="text-sm font-medium text-gray-800">{{ $menu->menu_label }}</span>
                                @if($menu->is_active)
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">Aktif</span>
                                @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Nonaktif</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $menu->reply_message }}</p>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button type="button" onclick='editMenu(@json($menu->id), @json($menu->menu_number), @json($menu->menu_label), @json($menu->reply_message), @json($menu->is_active))' class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <form action="{{ route('chatbot.menu.delete', $menu) }}" method="POST" onsubmit="return confirm('Hapus menu ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <button onclick="showAddMenuModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                + Tambah Menu Baru
            </button>
        </div>
    </div>

    <!-- Keyword Auto-Reply -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Keyword Auto-Reply</h2>
            <p class="text-sm text-gray-600 mt-1">Bot akan otomatis balas jika pesan mengandung keyword tertentu</p>
        </div>
        <div class="p-6">
            @if($keywords->count() > 0)
            <div class="space-y-3 mb-6">
                @foreach($keywords as $keyword)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded
                                    {{ $keyword->priority === 'high' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $keyword->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $keyword->priority === 'low' ? 'bg-gray-100 text-gray-700' : '' }}
                                ">{{ ucfirst($keyword->priority) }}</span>
                                <span class="text-sm font-medium text-gray-800">{{ $keyword->keywords }}</span>
                                @if($keyword->is_active)
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">Aktif</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $keyword->reply_message }}</p>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button type="button" onclick='editKeyword(@json($keyword->id), @json($keyword->keywords), @json($keyword->reply_message), @json($keyword->priority), @json($keyword->is_active))' class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <form action="{{ route('chatbot.keyword.delete', $keyword) }}" method="POST" onsubmit="return confirm('Hapus keyword ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <button onclick="showAddKeywordModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                + Tambah Keyword Baru
            </button>
        </div>
    </div>

    <!-- Default Reply -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Pesan Default</h2>
            <p class="text-sm text-gray-600 mt-1">Pesan ini akan dikirim jika bot tidak mengerti pesan customer</p>
        </div>
        <form action="{{ route('chatbot.settings.update') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="chatbot_enabled" value="{{ $chatbotEnabled ? '1' : '0' }}">
            <input type="hidden" name="reply_all_messages" value="{{ $replyAllMessages ? '1' : '0' }}">
            <input type="hidden" name="welcome_message" value="{{ $welcomeMessage }}">
            <textarea name="default_reply" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Contoh:&#10;Maaf, saya belum mengerti maksud Anda.&#10;Silakan ketik angka menu atau hubungi admin kami.">{{ $defaultReply }}</textarea>
            <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Simpan Default Reply
            </button>
        </form>
    </div>
</div>

<!-- Modal Add/Edit Menu -->
<div id="menuModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
        <h3 id="menuModalTitle" class="text-lg font-semibold mb-4">Tambah Menu</h3>
        <form id="menuForm" method="POST">
            @csrf
            <input type="hidden" id="menuMethod" name="_method" value="POST">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Menu</label>
                    <input type="number" name="menu_number" id="menu_number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label Menu</label>
                    <input type="text" name="menu_label" id="menu_label" required placeholder="Contoh: 1️⃣ Info Produk" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pesan Balasan</label>
                    <textarea name="reply_message" id="menu_reply" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                <div id="menuActiveDiv" class="hidden">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="menu_active" value="1" checked class="w-4 h-4 text-green-600 rounded">
                        <span class="ml-2 text-sm font-medium text-gray-700">Aktifkan menu</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Simpan</button>
                <button type="button" onclick="closeMenuModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add/Edit Keyword -->
<div id="keywordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
        <h3 id="keywordModalTitle" class="text-lg font-semibold mb-4">Tambah Keyword</h3>
        <form id="keywordForm" method="POST">
            @csrf
            <input type="hidden" id="keywordMethod" name="_method" value="POST">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keywords (pisahkan dengan koma)</label>
                    <input type="text" name="keywords" id="keywords" required placeholder="Contoh: harga, price, berapa" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">Gunakan koma untuk memisahkan multiple keywords</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pesan Balasan</label>
                    <textarea name="reply_message" id="keyword_reply" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                    <select name="priority" id="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="high">High</option>
                        <option value="medium" selected>Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div id="keywordActiveDiv" class="hidden">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="keyword_active" value="1" checked class="w-4 h-4 text-green-600 rounded">
                        <span class="ml-2 text-sm font-medium text-gray-700">Aktifkan keyword</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Simpan</button>
                <button type="button" onclick="closeKeywordModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddMenuModal() {
    document.getElementById('menuModalTitle').textContent = 'Tambah Menu';
    document.getElementById('menuForm').action = '{{ route("chatbot.menu.store") }}';
    document.getElementById('menuMethod').value = 'POST';
    document.getElementById('menu_number').value = '';
    document.getElementById('menu_label').value = '';
    document.getElementById('menu_reply').value = '';
    document.getElementById('menuActiveDiv').classList.add('hidden');
    document.getElementById('menuModal').classList.remove('hidden');
}

function editMenu(id, number, label, reply, active) {
    document.getElementById('menuModalTitle').textContent = 'Edit Menu';
    document.getElementById('menuForm').action = '/chatbot/menu/' + id;
    document.getElementById('menuMethod').value = 'PUT';
    document.getElementById('menu_number').value = number;
    document.getElementById('menu_label').value = label;
    document.getElementById('menu_reply').value = reply;
    document.getElementById('menu_active').checked = active;
    document.getElementById('menuActiveDiv').classList.remove('hidden');
    document.getElementById('menuModal').classList.remove('hidden');
}

function closeMenuModal() {
    document.getElementById('menuModal').classList.add('hidden');
}

function showAddKeywordModal() {
    document.getElementById('keywordModalTitle').textContent = 'Tambah Keyword';
    document.getElementById('keywordForm').action = '{{ route("chatbot.keyword.store") }}';
    document.getElementById('keywordMethod').value = 'POST';
    document.getElementById('keywords').value = '';
    document.getElementById('keyword_reply').value = '';
    document.getElementById('priority').value = 'medium';
    document.getElementById('keywordActiveDiv').classList.add('hidden');
    document.getElementById('keywordModal').classList.remove('hidden');
}

function editKeyword(id, keywords, reply, priority, active) {
    document.getElementById('keywordModalTitle').textContent = 'Edit Keyword';
    document.getElementById('keywordForm').action = '/chatbot/keyword/' + id;
    document.getElementById('keywordMethod').value = 'PUT';
    document.getElementById('keywords').value = keywords;
    document.getElementById('keyword_reply').value = reply;
    document.getElementById('priority').value = priority;
    document.getElementById('keyword_active').checked = active;
    document.getElementById('keywordActiveDiv').classList.remove('hidden');
    document.getElementById('keywordModal').classList.remove('hidden');
}

function closeKeywordModal() {
    document.getElementById('keywordModal').classList.add('hidden');
}
</script>
@endsection
