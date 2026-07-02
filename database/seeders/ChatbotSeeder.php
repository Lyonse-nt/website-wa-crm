<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatbotSetting;
use App\Models\ChatbotMenu;
use App\Models\ChatbotKeyword;

class ChatbotSeeder extends Seeder
{
    public function run(): void
    {
        // Settings - gunakan updateOrCreate untuk avoid duplicate
        ChatbotSetting::updateOrCreate(
            ['setting_key' => 'chatbot_enabled'],
            [
                'setting_value' => 'true',
                'is_active' => true
            ]
        );

        ChatbotSetting::updateOrCreate(
            ['setting_key' => 'reply_all_messages'],
            [
                'setting_value' => 'true',
                'is_active' => true
            ]
        );

        ChatbotSetting::updateOrCreate(
            ['setting_key' => 'welcome_message'],
            [
                'setting_value' => "👋 Halo! Selamat datang di Mini WhatsApp CRM\n\nKami siap membantu Anda! Silakan pilih:\n\n1️⃣ Informasi Produk\n2️⃣ Cara Pemesanan\n3️⃣ Hubungi Admin\n\nKetik angka atau langsung tanyakan kebutuhan Anda 😊",
                'is_active' => true
            ]
        );

        ChatbotSetting::updateOrCreate(
            ['setting_key' => 'default_reply'],
            [
                'setting_value' => "Terima kasih atas pesan Anda! 🙏\n\nMaaf, saya belum mengerti maksud Anda.\nSilakan ketik angka menu atau tanyakan:\n- Informasi produk\n- Cara pemesanan\n- Hubungi admin\n\nAdmin kami juga akan segera membalas 😊",
                'is_active' => true
            ]
        );

        // Menus - hanya create jika belum ada
        if (ChatbotMenu::count() === 0) {
            ChatbotMenu::create([
                'menu_number' => 1,
                'menu_label' => '1️⃣ Informasi Produk',
                'reply_message' => "📦 Informasi Produk\n\nKami menyediakan berbagai produk berkualitas:\n- Produk A\n- Produk B\n- Produk C\n\nUntuk detail lengkap, silakan kunjungi website kami atau hubungi admin.",
                'is_active' => true,
                'order' => 1
            ]);

            ChatbotMenu::create([
                'menu_number' => 2,
                'menu_label' => '2️⃣ Cara Pemesanan',
                'reply_message' => "📝 Cara Pemesanan\n\n1. Pilih produk yang diinginkan\n2. Kirim pesan ke admin dengan format:\n   PESAN [Nama Produk] [Jumlah]\n3. Admin akan mengkonfirmasi pesanan\n4. Lakukan pembayaran\n5. Pesanan akan dikirim\n\nMudah bukan? 😊",
                'is_active' => true,
                'order' => 2
            ]);

            ChatbotMenu::create([
                'menu_number' => 3,
                'menu_label' => '3️⃣ Hubungi Admin',
                'reply_message' => "👨‍💼 Hubungi Admin\n\nJika ingin bicara langsung dengan admin, silakan tunggu sebentar.\nAdmin kami akan segera merespon pesan Anda.\n\nJam operasional:\nSenin - Jumat: 09:00 - 17:00\nSabtu: 09:00 - 12:00",
                'is_active' => true,
                'order' => 3
            ]);
        }

        // Keywords - hanya create jika belum ada
        if (ChatbotKeyword::count() === 0) {
            ChatbotKeyword::create([
                'keywords' => 'harga,price,berapa,biaya',
                'reply_message' => "💰 Informasi Harga\n\nUntuk informasi harga lengkap, silakan:\n1. Kunjungi website kami\n2. Ketik KATALOG untuk melihat daftar harga\n3. Hubungi admin untuk harga spesial\n\nAdmin kami siap membantu! 😊",
                'priority' => 'high',
                'is_active' => true
            ]);

            ChatbotKeyword::create([
                'keywords' => 'order,pesan,beli,buat pesanan',
                'reply_message' => "🛒 Cara Order\n\nMau pesan? Ketik angka 2 atau langsung kirim:\nPESAN [Nama Produk] [Jumlah]\n\nContoh:\nPESAN Produk A 2\n\nAdmin akan segera proses! 😊",
                'priority' => 'high',
                'is_active' => true
            ]);

            ChatbotKeyword::create([
                'keywords' => 'lokasi,alamat,dimana,maps',
                'reply_message' => "📍 Lokasi Kami\n\nAlamat:\nJl. Contoh No. 123\nKota, Provinsi 12345\n\nJam operasional:\nSenin - Jumat: 09:00 - 17:00\nSabtu: 09:00 - 12:00\nMinggu: Tutup",
                'priority' => 'medium',
                'is_active' => true
            ]);

            ChatbotKeyword::create([
                'keywords' => 'admin,cs,customer service,hubungi',
                'reply_message' => "👨‍💼 Menghubungkan ke Admin...\n\nSilakan tunggu sebentar, admin kami akan segera merespon pesan Anda.\n\nTerima kasih atas kesabaran Anda! 🙏",
                'priority' => 'medium',
                'is_active' => true
            ]);
        }
    }
}
