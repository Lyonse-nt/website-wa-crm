<?php

namespace Database\Seeders;

use App\Models\Kontak;
use App\Models\Percakapan;
use App\Models\Pesan;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample contacts
        $kontak1 = Kontak::create([
            'nama' => 'Budi Santoso',
            'nomor_whatsapp' => '628123456789'
        ]);

        $kontak2 = Kontak::create([
            'nama' => 'Siti Nurhaliza',
            'nomor_whatsapp' => '628987654321'
        ]);

        $kontak3 = Kontak::create([
            'nama' => 'Ahmad Rizki',
            'nomor_whatsapp' => '628555666777'
        ]);

        // Create conversations and messages for contact 1
        $percakapan1 = Percakapan::create([
            'kontak_id' => $kontak1->id,
            'pesan_terakhir' => 'Terima kasih atas infonya!',
            'waktu_pesan_terakhir' => now()->subMinutes(5)
        ]);

        Pesan::create([
            'percakapan_id' => $percakapan1->id,
            'arah_pesan' => 'masuk',
            'jenis_pesan' => 'text',
            'isi_pesan' => 'Halo, saya mau tanya tentang produk Anda',
            'status' => 'delivered',
            'created_at' => now()->subMinutes(10)
        ]);

        Pesan::create([
            'percakapan_id' => $percakapan1->id,
            'arah_pesan' => 'keluar',
            'jenis_pesan' => 'text',
            'isi_pesan' => 'Halo! Tentu, silakan. Ada yang bisa kami bantu?',
            'status' => 'sent',
            'created_at' => now()->subMinutes(9)
        ]);

        Pesan::create([
            'percakapan_id' => $percakapan1->id,
            'arah_pesan' => 'masuk',
            'jenis_pesan' => 'text',
            'isi_pesan' => 'Berapa harga untuk paket premium?',
            'status' => 'delivered',
            'created_at' => now()->subMinutes(8)
        ]);

        Pesan::create([
            'percakapan_id' => $percakapan1->id,
            'arah_pesan' => 'keluar',
            'jenis_pesan' => 'text',
            'isi_pesan' => 'Untuk paket premium, harganya Rp 500.000/bulan dengan fitur lengkap.',
            'status' => 'sent',
            'created_at' => now()->subMinutes(6)
        ]);

        Pesan::create([
            'percakapan_id' => $percakapan1->id,
            'arah_pesan' => 'masuk',
            'jenis_pesan' => 'text',
            'isi_pesan' => 'Terima kasih atas infonya!',
            'status' => 'delivered',
            'created_at' => now()->subMinutes(5)
        ]);

        // Create conversations and messages for contact 2
        $percakapan2 = Percakapan::create([
            'kontak_id' => $kontak2->id,
            'pesan_terakhir' => 'Baik, saya akan coba dulu',
            'waktu_pesan_terakhir' => now()->subHours(2)
        ]);

        Pesan::create([
            'percakapan_id' => $percakapan2->id,
            'arah_pesan' => 'masuk',
            'jenis_pesan' => 'text',
            'isi_pesan' => 'Apakah ada trial gratis?',
            'status' => 'delivered',
            'created_at' => now()->subHours(3)
        ]);

        Pesan::create([
            'percakapan_id' => $percakapan2->id,
            'arah_pesan' => 'keluar',
            'jenis_pesan' => 'text',
            'isi_pesan' => 'Ya, kami menyediakan trial 14 hari gratis tanpa kartu kredit.',
            'status' => 'sent',
            'created_at' => now()->subHours(2)->subMinutes(30)
        ]);

        Pesan::create([
            'percakapan_id' => $percakapan2->id,
            'arah_pesan' => 'masuk',
            'jenis_pesan' => 'text',
            'isi_pesan' => 'Baik, saya akan coba dulu',
            'status' => 'delivered',
            'created_at' => now()->subHours(2)
        ]);

        // Create conversation for contact 3
        $percakapan3 = Percakapan::create([
            'kontak_id' => $kontak3->id,
            'pesan_terakhir' => 'Halo',
            'waktu_pesan_terakhir' => now()->subDay()
        ]);

        Pesan::create([
            'percakapan_id' => $percakapan3->id,
            'arah_pesan' => 'masuk',
            'jenis_pesan' => 'text',
            'isi_pesan' => 'Halo',
            'status' => 'delivered',
            'created_at' => now()->subDay()
        ]);

        $this->command->info('✅ Demo data created successfully!');
        $this->command->info('📊 Created 3 contacts with sample conversations');
    }
}
