# 📘 Setup Guide - Mini WhatsApp CRM

Panduan lengkap untuk setup WhatsApp Business Cloud API dari nol.

## 🎯 Prerequisites

- Meta (Facebook) Account
- Nomor telepon yang belum terdaftar di WhatsApp Business
- Dokumen bisnis (opsional, untuk verifikasi)

---

## 📝 STEP 1: Buat Meta Developer App

### 1.1 Buka Meta Developer Console

Buka [https://developers.facebook.com](https://developers.facebook.com)

### 1.2 Login atau Daftar

Gunakan akun Facebook Anda atau buat akun baru.

### 1.3 Buat Aplikasi Baru

1. Klik "My Apps" di kanan atas
2. Klik "Create App"
3. Pilih tipe: **Business**
4. Isi form:
   - **App Name**: Mini WhatsApp CRM
   - **App Contact Email**: email@yourdomain.com
   - **Business Account**: Pilih atau buat baru
5. Klik "Create App"

---

## 📱 STEP 2: Setup WhatsApp Product

### 2.1 Tambah WhatsApp ke App

1. Di dashboard app, cari "WhatsApp" di product list
2. Klik "Set Up"
3. Ikuti wizard setup

### 2.2 Pilih Business Account

- Jika belum punya, buat WhatsApp Business Account baru
- Jika sudah punya, pilih yang existing

### 2.3 Tambah Nomor Telepon

1. Menu "WhatsApp" → "Getting Started"
2. Bagian "Phone Number" → klik "Add phone number"
3. Pilih metode verifikasi:
   - **Test Number** (untuk development, gratis, terbatas 5 nomor)
   - **Own Number** (production, butuh verifikasi bisnis)

**Untuk Development, gunakan Test Number:**
- Meta akan provide nomor test
- Kamu bisa kirim pesan ke max 5 nomor tujuan yang kamu daftarkan

**Cara Tambah Nomor Tujuan Test:**
1. Menu "WhatsApp" → "API Setup"
2. Scroll ke "To" → "Manage phone number list"
3. Tambahkan nomor WhatsApp yang akan kamu gunakan untuk testing
4. Verifikasi nomor via OTP

---

## 🔑 STEP 3: Dapatkan Credentials

### 3.1 Access Token

1. Menu "WhatsApp" → "API Setup"
2. Bagian "Temporary access token" → Copy token
3. Paste ke `.env`:

```env
WHATSAPP_TOKEN=EAAxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

⚠️ **Penting**: Token ini temporary (24 jam). Untuk production, buat System User Token yang permanent.

### 3.2 Phone Number ID

1. Masih di halaman "API Setup"
2. Bagian "Phone Number ID" → Copy ID
3. Paste ke `.env`:

```env
WHATSAPP_PHONE_NUMBER_ID=123456789012345
```

### 3.3 WhatsApp Business Account ID (Optional)

1. Menu "WhatsApp" → "API Setup"
2. Bagian "WhatsApp Business Account ID" → Copy
3. Simpan untuk referensi

---

## 🔗 STEP 4: Setup Webhook

### 4.1 Generate Verify Token

Buat random string untuk verify token:

```bash
openssl rand -base64 32
```

Atau gunakan generator online: [https://randomkeygen.com](https://randomkeygen.com)

Simpan di `.env`:

```env
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_random_secure_string_here
```

### 4.2 Setup ngrok (Development)

**Install ngrok:**
- Download dari [https://ngrok.com/download](https://ngrok.com/download)
- Extract dan install

**Jalankan Laravel:**
```bash
php artisan serve
```

**Di terminal lain, jalankan ngrok:**
```bash
ngrok http 8000
```

**Copy URL yang muncul:**
```
Forwarding: https://abc123def.ngrok-free.app -> http://localhost:8000
```

### 4.3 Configure Webhook di Meta

1. Menu "WhatsApp" → "Configuration"
2. Bagian "Webhook" → klik "Edit"
3. Isi form:
   - **Callback URL**: `https://abc123def.ngrok-free.app/webhook`
   - **Verify Token**: Token yang kamu buat di step 4.1
4. Klik "Verify and Save"

✅ Jika berhasil, akan muncul pesan success.

### 4.4 Subscribe ke Webhook Fields

Masih di halaman Configuration:

1. Scroll ke "Webhook fields"
2. Klik "Manage"
3. Subscribe ke field:
   - ☑️ **messages** (wajib)
   - ☑️ **message_status** (opsional, untuk status delivered/read)
4. Save

---

## 🧪 STEP 5: Testing

### 5.1 Test Kirim Pesan (Outbound)

1. Buka aplikasi: `http://localhost:8000`
2. Menu "Kirim Pesan"
3. Masukkan nomor yang sudah didaftarkan sebagai test number
4. Tulis pesan dan kirim
5. Cek WhatsApp di nomor tujuan

✅ Jika berhasil, kamu akan terima pesan di WhatsApp.

### 5.2 Test Terima Pesan (Inbound)

1. Dari WhatsApp, balas pesan ke nomor bisnis
2. Cek menu "Log Webhook" di aplikasi
3. Cek menu "Percakapan"

✅ Jika berhasil:
- Webhook log akan muncul
- Pesan akan masuk ke database
- Muncul di halaman percakapan

### 5.3 Test Auto Reply

Kirim pesan dari WhatsApp:
- Ketik "halo" → Auto reply
- Ketik "menu" → Auto reply dengan menu

---

## 🔄 STEP 6: Permanent Access Token (Production)

Token temporary hanya bertahan 24 jam. Untuk production, buat System User Token.

### 6.1 Buat System User

1. Buka [Meta Business Suite](https://business.facebook.com)
2. Settings → Users → System Users
3. Klik "Add" → Beri nama (misal: "WhatsApp API")
4. Role: **Admin**
5. Save

### 6.2 Generate Token

1. Klik pada System User yang baru dibuat
2. Klik "Generate New Token"
3. Select App: Pilih app kamu
4. Select permissions:
   - `whatsapp_business_messaging`
   - `whatsapp_business_management`
5. Generate Token
6. **Copy dan simpan token** (tidak bisa dilihat lagi!)

### 6.3 Update .env

```env
WHATSAPP_TOKEN=EAAxxxxxxxxxxxxx_permanent_token
```

---

## 🌐 STEP 7: Production Deployment

### 7.1 Setup Domain

Untuk production, kamu butuh domain sendiri (bukan ngrok).

**Options:**
1. VPS (DigitalOcean, AWS, etc)
2. Shared Hosting dengan Laravel support
3. Laravel Forge / Ploi
4. Cloud Platform (Heroku, Railway, etc)

### 7.2 SSL Certificate

WhatsApp webhook **harus** HTTPS. Gunakan:
- Let's Encrypt (gratis)
- Cloudflare SSL
- SSL dari hosting provider

### 7.3 Update Webhook URL

Ganti webhook URL di Meta:
```
https://yourdomain.com/webhook
```

### 7.4 Environment Variables

Set semua env variables di production:
```env
APP_ENV=production
APP_DEBUG=false
WHATSAPP_TOKEN=your_permanent_token
WHATSAPP_PHONE_NUMBER_ID=your_phone_id
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_verify_token
```

---

## ⚠️ Troubleshooting

### Error: Token Invalid

**Solusi:**
- Generate token baru
- Pastikan permissions sudah benar
- Untuk production, gunakan System User Token

### Error: Webhook Verification Failed

**Solusi:**
- Cek `WHATSAPP_WEBHOOK_VERIFY_TOKEN` sama dengan yang di Meta
- Pastikan route `/webhook` (GET) berfungsi
- Test manual: `https://your-url/webhook?hub.mode=subscribe&hub.verify_token=your_token&hub.challenge=test`

### Error: Pesan Tidak Terkirim

**Solusi:**
- Cek nomor tujuan sudah didaftarkan di test number list
- Cek format nomor (harus 628xxx, bukan 08xxx)
- Cek Log API untuk detail error

### Error: Webhook Tidak Menerima Pesan

**Solusi:**
- Pastikan ngrok masih running (untuk dev)
- Cek webhook subscription di Meta
- Cek Log Webhook di aplikasi
- Test dengan curl:
  ```bash
  curl -X POST https://your-url/webhook \
    -H "Content-Type: application/json" \
    -d '{"test": true}'
  ```

### Error: Rate Limit

WhatsApp API punya rate limit:
- Development: 250 pesan/hari
- Production (dengan verifikasi): 1000+ pesan/hari

**Solusi:**
- Request rate limit increase di Meta
- Complete business verification

---

## 📚 Resources

- [WhatsApp Cloud API Docs](https://developers.facebook.com/docs/whatsapp/cloud-api)
- [Meta Business Help Center](https://www.facebook.com/business/help)
- [WhatsApp Business Platform](https://business.whatsapp.com)
- [ngrok Documentation](https://ngrok.com/docs)

---

## 🎓 Next Steps

Setelah setup berhasil:

1. ✅ Test semua fitur
2. ✅ Customize auto reply
3. ✅ Add more contacts
4. ✅ Explore WhatsApp template messages
5. ✅ Build chatbot AI integration
6. ✅ Deploy to production

---

## 💡 Tips

**Development:**
- Gunakan ngrok untuk testing lokal
- Save webhook logs untuk debugging
- Test dengan berbagai tipe pesan

**Production:**
- Gunakan System User Token (permanent)
- Setup monitoring & alerts
- Backup database regularly
- Implement rate limiting
- Add error handling & retry logic

**Security:**
- Jangan commit `.env` ke git
- Rotate tokens securely
- Validate webhook signatures
- Use HTTPS only

---

**Selamat! WhatsApp CRM kamu sudah siap digunakan!** 🎉

Jika ada pertanyaan, check documentation atau open issue di repository.
