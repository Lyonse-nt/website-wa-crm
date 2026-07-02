# Mini WhatsApp CRM

Aplikasi CRM sederhana berbasis Laravel 12 untuk mengelola komunikasi WhatsApp Business menggunakan WhatsApp Cloud API.

## 🎯 Tujuan Project

Project ini dibuat sebagai media pembelajaran untuk memahami:
- ✅ WhatsApp Business Cloud API
- ✅ Webhook mechanism
- ✅ Service Layer pattern
- ✅ Laravel HTTP Client
- ✅ Eloquent Relationships
- ✅ Real-time messaging flow

## 🚀 Fitur

- **Dashboard** - Monitoring statistik percakapan
- **CRUD Kontak** - Kelola kontak WhatsApp
- **Kirim Pesan** - Kirim pesan ke nomor WhatsApp
- **Riwayat Chat** - Tampilan chat seperti WhatsApp Web
- **Webhook** - Terima pesan masuk dari customer
- **Auto Reply** - Respon otomatis berdasarkan keyword
- **Log API** - Monitoring request/response API
- **Log Webhook** - Monitoring webhook payload
- **Pengaturan** - Konfigurasi WhatsApp API

## 🛠️ Tech Stack

- **Laravel 12** - Backend framework
- **Blade** - Template engine
- **Tailwind CSS** - Styling
- **SQLite** - Database
- **WhatsApp Cloud API** - Messaging service
- **ngrok** - Local development tunnel

## 📋 Persyaratan

- PHP 8.2+
- Composer
- Node.js & NPM
- WhatsApp Business Account
- Meta Developer Account
- ngrok (untuk development)

## 🔧 Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd webapiwhatsapp
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Database sudah menggunakan SQLite secara default. File `database/database.sqlite` sudah tersedia.

```bash
php artisan migrate
```

### 5. Konfigurasi WhatsApp API

Edit file `.env` dan isi kredensial WhatsApp API Anda:

```env
WHATSAPP_TOKEN=your_access_token_from_meta
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_random_secure_token
WHATSAPP_VERSION=v18.0
```

#### Cara Mendapatkan Kredensial:

1. Buka [Meta Developer Console](https://developers.facebook.com)
2. Buat atau pilih aplikasi WhatsApp Business Anda
3. **Access Token**: Menu "WhatsApp" → "API Setup" → Copy token
4. **Phone Number ID**: Menu "WhatsApp" → "API Setup" → Phone Number ID
5. **Verify Token**: Buat string random sendiri (akan digunakan saat setup webhook)

### 6. Build Assets

```bash
npm run build
```

### 7. Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 🌐 Setup Webhook (Development)

Untuk development lokal, gunakan ngrok untuk expose webhook ke internet:

### 1. Install ngrok

Download dari [ngrok.com](https://ngrok.com)

### 2. Jalankan ngrok

```bash
ngrok http 8000
```

Anda akan mendapat URL seperti: `https://abc123.ngrok-free.app`

### 3. Setup Webhook di Meta

1. Buka [Meta Developer Console](https://developers.facebook.com)
2. Pilih aplikasi WhatsApp Anda
3. Menu "WhatsApp" → "Configuration"
4. Klik "Edit" pada Webhook section
5. Isi:
   - **Callback URL**: `https://abc123.ngrok-free.app/webhook`
   - **Verify Token**: Sama dengan `WHATSAPP_WEBHOOK_VERIFY_TOKEN` di `.env`
6. Subscribe events: `messages`
7. Save

### 4. Test Webhook

Kirim pesan WhatsApp ke nomor bisnis Anda. Pesan akan muncul di menu "Percakapan" dan "Log Webhook".

## 📚 Struktur Database

### Tabel `kontaks`
- id
- nama
- nomor_whatsapp
- created_at, updated_at

### Tabel `percakapans`
- id
- kontak_id (FK)
- pesan_terakhir
- waktu_pesan_terakhir
- created_at, updated_at

### Tabel `pesans`
- id
- percakapan_id (FK)
- arah_pesan (masuk/keluar)
- jenis_pesan (text/image/document)
- isi_pesan
- whatsapp_message_id
- status (pending/sent/delivered/read/failed)
- raw_response (JSON)
- created_at, updated_at

### Tabel `log_apis`
- id
- endpoint
- request (JSON)
- response (JSON)
- status_http
- created_at, updated_at

### Tabel `log_webhooks`
- id
- payload (JSON)
- created_at, updated_at

## 🔄 Flow Sistem

### Outbound (Kirim Pesan)
```
Admin → Controller → WhatsAppService → Meta API → Customer
                ↓
            Database (Log)
```

### Inbound (Terima Pesan)
```
Customer → WhatsApp → Meta → Webhook → Laravel → Database → Dashboard
```

## 🤖 Auto Reply

Aplikasi sudah dilengkapi auto reply untuk keyword berikut:

- **halo** → "Halo juga 👋\nAda yang bisa kami bantu?"
- **menu** → "📋 Menu:\n1. Informasi\n2. Bantuan\n3. Kontak Admin"
- **info** → "ℹ️ Ini adalah sistem CRM WhatsApp otomatis..."

Anda bisa menambahkan keyword lainnya di `WebhookController@autoReply()`

## 📱 Endpoint API

### Webhook Verification (GET)
```
GET /webhook?hub.mode=subscribe&hub.verify_token=xxx&hub.challenge=123
```

### Webhook Handler (POST)
```
POST /webhook
```

## 🎨 Desain UI

Aplikasi menggunakan design system modern dengan:
- **Warna Primary**: Hijau WhatsApp (#25D366)
- **Layout**: Sidebar + Main Content
- **Style**: Clean, Minimalis, Responsive
- **Icons**: Heroicons

Inspirasi desain dari:
- WhatsApp Web
- Linear
- Notion
- Vercel Dashboard

## 📖 Cara Menggunakan

### 1. Tambah Kontak
- Menu "Kontak" → "Tambah Kontak"
- Isi nama dan nomor WhatsApp
- Simpan

### 2. Kirim Pesan
- Menu "Kirim Pesan"
- Pilih kontak atau ketik nomor manual
- Tulis pesan
- Kirim

### 3. Lihat Riwayat Chat
- Menu "Percakapan"
- Pilih kontak dari sidebar
- Lihat chat history
- Balas langsung dari form di bawah

### 4. Monitoring
- **Log API**: Lihat semua request/response ke WhatsApp API
- **Log Webhook**: Lihat semua payload dari Meta

## 🔍 Troubleshooting

### Pesan Gagal Terkirim
- Cek `WHATSAPP_TOKEN` di `.env`
- Cek `WHATSAPP_PHONE_NUMBER_ID` di `.env`
- Pastikan token masih valid
- Cek Log API untuk detail error

### Webhook Tidak Menerima Pesan
- Pastikan ngrok masih running
- Cek URL webhook di Meta Developer Console
- Cek `WHATSAPP_WEBHOOK_VERIFY_TOKEN` sama dengan yang di Meta
- Subscribe ke event `messages` di Meta

### Bot Tidak Balas di Grup WhatsApp
⚠️ **WhatsApp Business Cloud API tidak support grup**. Ini adalah limitasi dari Meta/WhatsApp:
- Bot hanya bisa terima dan balas pesan **personal/private chat**
- Bot tidak bisa baca pesan di grup
- Bot tidak bisa join grup
- Ini berlaku untuk semua bot yang pakai WhatsApp Cloud API (gratis)

**Solusi:** Arahkan user untuk chat personal dengan bot, bukan di grup.

### Tombol Edit Chatbot Tidak Berfungsi
Sudah diperbaiki dengan menggunakan `@json()` directive untuk proper escaping.

### Database Error
```bash
php artisan migrate:fresh
```

## 📝 Development Notes

### Service Layer Pattern

Semua logic WhatsApp API ada di `WhatsAppService.php`:
- `sendText()` - Kirim text message
- `sendImage()` - Kirim gambar
- `sendDocument()` - Kirim dokumen

### Auto Reply Logic

Edit `WebhookController@autoReply()` untuk menambah keyword:

```php
$replies = [
    'halo' => 'Response untuk halo',
    'keyword' => 'Response untuk keyword',
];
```

### Menambah Fitur Template Message

WhatsApp Cloud API mendukung template message. Tambahkan method di `WhatsAppService`:

```php
public function sendTemplate($to, $templateName, $parameters)
{
    // Implementation
}
```

## 🌐 Deploy ke Railway

Untuk deployment production ke Railway:

1. **Baca dokumentasi lengkap**: [RAILWAY_SETUP.md](RAILWAY_SETUP.md)
2. **Gunakan PostgreSQL** (bukan SQLite) agar data tidak hilang
3. Setup environment variables di Railway dashboard
4. Connect GitHub repo untuk auto-deploy

⚠️ **PENTING**: Railway menggunakan ephemeral filesystem. Jika menggunakan SQLite, data akan **hilang setiap deploy**. Gunakan PostgreSQL yang disediakan Railway (gratis).

## 🚀 Next Steps

Setelah paham dengan project ini, kamu bisa kembangkan:

1. **Chatbot AI** - Integrasi dengan OpenAI/Claude
2. **Broadcast Message** - Kirim pesan massal
3. **Template Management** - CRUD template pesan
4. **Analytics** - Statistik percakapan lebih detail
5. **Multi User** - Authentication & role management
6. **Media Support** - Upload image/document
7. **Contact Import** - Import dari CSV/Excel
8. **Schedule Message** - Kirim pesan terjadwal

## 📄 License

Project ini dibuat untuk tujuan pembelajaran.

## 🤝 Contributing

Silakan fork, improve, dan submit PR!

## 📞 Support

Jika ada pertanyaan atau kendala, buka issue di repository ini.

---

**Happy Coding!** 🎉

Built with ❤️ using Laravel 12 & WhatsApp Cloud API
