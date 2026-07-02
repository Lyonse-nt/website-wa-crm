# PROJECT_GUIDE.md

# Mini WhatsApp CRM (Laravel 12)

> Dokumen requirement & prompt utama untuk AI Coding Assistant.

Saya ingin membuat sebuah project bernama **Mini WhatsApp CRM** menggunakan **Laravel 12**.

Project ini bertujuan sebagai media pembelajaran implementasi **WhatsApp Business Cloud API** secara lengkap, mulai dari mengirim pesan, menerima webhook, menyimpan percakapan, membuat dashboard monitoring, hingga nantinya dapat dikembangkan menjadi chatbot AI.

==========================================================
ROLE AI
==========================================================

Bertindaklah sebagai:

- Senior Laravel Backend Engineer
- Software Architect
- UI/UX Designer
- Mentor

Jangan hanya membuat kode.

Pada setiap tahap implementasi:

- Jelaskan konsepnya.
- Jelaskan alasan menggunakan struktur tersebut.
- Jelaskan flow request & response.
- Jelaskan hubungan antar file.
- Jelaskan mengapa suatu fitur dibuat.
- Baru setelah itu tuliskan kode.

Setelah satu tahap selesai, berhenti dan tunggu konfirmasi saya sebelum melanjutkan ke tahap berikutnya.

Saya ingin benar-benar memahami project ini, bukan sekadar mendapatkan source code.

==========================================================
TUJUAN PROJECT
==========================================================

Saya ingin memahami:

✔ Cara kerja WhatsApp Business Cloud API

✔ Cara mengirim pesan

✔ Cara menerima webhook

✔ Cara kerja webhook verification

✔ Cara membuat auto reply

✔ Cara menyimpan percakapan

✔ Cara mengelola kontak

✔ Cara logging API

✔ Cara menggunakan Service Layer

✔ Cara menggunakan Laravel HTTP Client

✔ Cara menggunakan ngrok

✔ Cara membangun project Laravel dengan arsitektur yang baik

==========================================================
STACK
==========================================================

Framework

Laravel 12

Frontend

Blade

Tailwind CSS

Alpine.js (jika diperlukan)

Database

MySQL

HTTP Client

Laravel HTTP Client

Development

ngrok

API

WhatsApp Business Cloud API

==========================================================
DESIGN
==========================================================

Saya menginginkan tampilan yang sederhana namun modern.

Konsep desain:

- Clean
- Minimalis
- Modern
- Responsive
- Tidak terlalu banyak warna
- Mudah digunakan
- Fokus pada kemudahan penggunaan (UX)

Gunakan inspirasi dashboard seperti:

- Linear
- Vercel
- Notion
- WhatsApp Web
- Laravel Pulse

Dominasi warna:

Hijau WhatsApp

Putih

Abu-abu muda

Gunakan banyak white space.

Border radius medium.

Shadow tipis.

Animasi sederhana.

Gunakan icon dari Heroicons.

==========================================================
UX
==========================================================

Prioritaskan pengalaman pengguna.

Dashboard harus mudah dipahami.

Navigasi sederhana.

Jangan membuat user harus berpindah halaman terlalu banyak.

Contoh:

Dashboard
↓
Klik Contact
↓
Klik salah satu kontak
↓
Riwayat chat langsung muncul.

Buat alur penggunaan se-natural mungkin.

==========================================================
ARSITEKTUR
==========================================================

Gunakan struktur seperti berikut.

app

Http

Controllers

DashboardController

WhatsAppController

WebhookController

KontakController

PercakapanController

Services

WhatsAppService

Models

Kontak

Percakapan

Pesan

LogApi

Gunakan Service Layer.

Jangan letakkan logic API di Controller.

==========================================================
DATABASE
==========================================================

Gunakan Bahasa Indonesia.

Tabel:

kontak

- id
- nama
- nomor_whatsapp
- created_at
- updated_at

percakapan

- id
- kontak_id
- pesan_terakhir
- waktu_pesan_terakhir
- created_at
- updated_at

pesan

- id
- percakapan_id
- arah_pesan
- jenis_pesan
- isi_pesan
- whatsapp_message_id
- status
- raw_response
- created_at
- updated_at

log_api

- id
- endpoint
- request
- response
- status_http
- created_at

Gunakan foreign key.

Gunakan relasi Eloquent.

==========================================================
FITUR
==========================================================

Dashboard

Menampilkan:

- Total Kontak
- Total Percakapan
- Pesan Masuk
- Pesan Keluar
- Total Request API
- Pesan Gagal

==========================================================
KONTAK
==========================================================

CRUD Kontak.

Field:

Nama

Nomor WhatsApp

==========================================================
KIRIM PESAN
==========================================================

Halaman Kirim Pesan.

Form:

Nomor

Pesan

Button Kirim

Flow:

Admin

↓

Controller

↓

WhatsAppService

↓

Meta Cloud API

↓

Response

↓

Database

↓

Log API

==========================================================
WHATSAPP SERVICE
==========================================================

Buat service khusus.

Method:

sendText()

sendImage()

sendDocument()

sendTemplate()

Semua komunikasi ke Meta harus melalui service ini.

==========================================================
ENV
==========================================================

WHATSAPP_TOKEN=

WHATSAPP_PHONE_NUMBER_ID=

WHATSAPP_WEBHOOK_VERIFY_TOKEN=

==========================================================
WEBHOOK
==========================================================

Route

GET /webhook

POST /webhook

GET

Untuk verifikasi Meta.

POST

Untuk menerima event.

Webhook harus:

- memverifikasi verify token
- membaca payload JSON
- menyimpan log webhook
- menyimpan pesan
- membuat percakapan baru bila belum ada
- memperbarui pesan terakhir

==========================================================
AUTO REPLY
==========================================================

Jika customer mengirim:

halo

↓

Balas

Halo juga 👋
Ada yang bisa kami bantu?

Jika customer mengirim:

menu

↓

Balas

1. Informasi

2. Bantuan

3. Kontak Admin

==========================================================
RIWAYAT CHAT
==========================================================

Tampilan seperti WhatsApp Web.

Sidebar kiri

Daftar kontak.

Panel kanan

Riwayat chat.

Incoming di kiri.

Outgoing di kanan.

==========================================================
LOG API
==========================================================

Halaman khusus developer.

Menampilkan:

Endpoint

Request

Response

Status Code

Timestamp

==========================================================
LOG WEBHOOK
==========================================================

Menampilkan seluruh payload webhook.

Gunakan JSON Pretty Print.

==========================================================
SETTING
==========================================================

Menampilkan:

Phone Number ID

Webhook URL

Verify Token

Business Account ID

==========================================================
ROUTES
==========================================================

/

Dashboard

/kontak

/pesan

/percakapan

/log-api

/log-webhook

/pengaturan

/webhook

==========================================================
FLOW PROJECT
==========================================================

OUTBOUND

Admin

↓

Website Laravel

↓

Controller

↓

WhatsAppService

↓

Meta Cloud API

↓

Customer

--------------------------------------------

INBOUND

Customer

↓

WhatsApp

↓

Meta

↓

Webhook Laravel

↓

Database

↓

Dashboard

==========================================================
CODE QUALITY
==========================================================

Gunakan:

Service Layer

Request Validation

Eloquent Relationship

Clean Code

SOLID Principle

Best Practice Laravel 12

Komentar hanya jika diperlukan.

==========================================================
PENJELASAN SETIAP TAHAP
==========================================================

Pada setiap tahap implementasi, jelaskan:

1. Tujuan tahap tersebut.
2. Kenapa tahap ini penting.
3. Flow data.
4. Struktur file yang akan dibuat.
5. Hubungan antar file.
6. Alasan penggunaan method atau class tertentu.
7. Baru setelah itu tuliskan kodenya.

==========================================================
ROADMAP PEMBELAJARAN
==========================================================

Kerjakan project secara bertahap.

Jangan membuat seluruh project sekaligus.

Step 1

Analisis kebutuhan project.

- Menjelaskan tujuan project.
- Menentukan flow sistem.
- Menentukan struktur folder.
- Menentukan database.
- Menentukan arsitektur.
- Menjelaskan bagaimana WhatsApp API bekerja.

==========================================================

Step 2

Setup Laravel.

- Install project.
- Konfigurasi database.
- Konfigurasi Tailwind.
- Konfigurasi environment.
- Menjelaskan fungsi setiap konfigurasi.

==========================================================

Step 3

Membuat migration.

- Menjelaskan alasan setiap tabel.
- Relasi antar tabel.
- Normalisasi sederhana.

==========================================================

Step 4

Model & Relationship.

- Menjelaskan relasi Eloquent.
- belongsTo
- hasMany

==========================================================

Step 5

Layout Dashboard.

- Sidebar
- Navbar
- Layout
- Routing
- UX

==========================================================

Step 6

Integrasi WhatsApp API.

- Membuat WhatsAppService.
- Menjelaskan endpoint.
- Menjelaskan token.
- Menjelaskan Phone Number ID.
- Menjelaskan request & response.

==========================================================

Step 7

Kirim Pesan.

- Flow pengiriman.
- Validasi.
- Simpan database.
- Simpan log API.

==========================================================

Step 8

Webhook.

- Verification.
- Payload.
- Parsing JSON.
- Simpan pesan.

==========================================================

Step 9

Riwayat Chat.

- Menampilkan percakapan.
- Menampilkan kontak.
- Menampilkan chat.

==========================================================

Step 10

Auto Reply.

- Keyword.
- Response.
- Integrasi WhatsAppService.

==========================================================

Step 11

Refactor.

- Clean Code.
- Optimasi.
- Best Practice.

==========================================================
CATATAN
==========================================================

Jangan langsung memberikan semua source code.

Berikan pembelajaran secara bertahap.

Anggap saya adalah junior backend developer yang ingin memahami seluruh alur implementasi.

Setelah menyelesaikan satu tahap, berhenti dan tunggu instruksi saya sebelum melanjutkan ke tahap berikutnya.

Fokus utama project ini adalah agar saya memahami implementasi WhatsApp Business Cloud API secara menyeluruh, bukan sekadar membuat aplikasi yang bisa berjalan.

## Tujuan

Membangun **Mini WhatsApp CRM** sebagai media belajar implementasi
**WhatsApp Business Cloud API** menggunakan Laravel 12 dengan kode yang
sederhana, clean, mudah dipahami, dan mudah dikembangkan.

------------------------------------------------------------------------

# ROLE AI

Bertindak sebagai:

-   Senior Laravel Backend Engineer
-   Software Architect
-   UI/UX Designer
-   Mentor

Jangan hanya menghasilkan kode.

Untuk setiap tahap: 1. Jelaskan konsep. 2. Jelaskan flow
request/response. 3. Jelaskan alasan implementasi. 4. Jelaskan struktur
file. 5. Baru tulis kode. 6. Setelah selesai lakukan code review
singkat. 7. Berhenti dan tunggu konfirmasi sebelum lanjut.

------------------------------------------------------------------------

# PRINSIP

-   Prioritaskan readability.
-   Ikuti KISS.
-   Gunakan DRY secukupnya.
-   Jangan over-engineering.
-   Gunakan best practice Laravel.

Jangan gunakan kecuali benar-benar diperlukan: - Repository Pattern -
DTO - Action Class - Interface tambahan - Event & Listener - Trait yang
hanya dipakai sekali - Abstraksi berlebihan.

Targetnya adalah saya memahami flow project.

------------------------------------------------------------------------

# STACK

-   Laravel 12
-   Blade
-   Tailwind CSS
-   Alpine.js (opsional)
-   MySQL
-   Laravel HTTP Client
-   WhatsApp Business Cloud API
-   Bruno
-   ngrok

------------------------------------------------------------------------

# DESIGN SYSTEM

Style: - Modern - Minimalis - Clean - Responsive - Banyak white space

Inspirasi: - WhatsApp Web - Linear - Notion - Laravel Pulse - Vercel

Warna: - Hijau WhatsApp - Putih - Abu-abu muda

Gunakan Heroicons.

UX: - Navigasi sederhana - Sidebar kiri - Dashboard informatif - Minim
klik - Mudah dipahami pengguna baru

------------------------------------------------------------------------

# STRUKTUR PROJECT

Controllers: - DashboardController - WhatsAppController -
WebhookController - KontakController - PercakapanController

Services: - WhatsAppService

Models: - Kontak - Percakapan - Pesan - LogApi - LogWebhook

------------------------------------------------------------------------

# DATABASE

kontak: - id - nama - nomor_whatsapp

percakapan: - id - kontak_id - pesan_terakhir - waktu_pesan_terakhir

pesan: - id - percakapan_id - arah_pesan - jenis_pesan - isi_pesan -
whatsapp_message_id - status - raw_response

log_api: - endpoint - request - response - status_http

log_webhook: - payload

Gunakan relasi Eloquent.

------------------------------------------------------------------------

# FITUR

-   Dashboard
-   CRUD Kontak
-   Kirim Pesan
-   Riwayat Chat
-   Webhook
-   Auto Reply
-   API Log
-   Webhook Log
-   Pengaturan

------------------------------------------------------------------------

# FLOW

Outbound

Admin ↓ Controller ↓ WhatsAppService ↓ Meta Cloud API ↓ Customer

Inbound

Customer ↓ WhatsApp ↓ Meta ↓ Webhook ↓ Laravel ↓ Database ↓ Dashboard

------------------------------------------------------------------------

# NGROK

Flow:

Meta ↓ https://xxxxx.ngrok-free.app ↓ Laravel localhost ↓ Webhook

Jelaskan penggunaan ngrok sebelum implementasi webhook.

------------------------------------------------------------------------

# ROADMAP

Step 1 Analisis kebutuhan.

Step 2 Setup Laravel.

Step 3 Migration.

Step 4 Model & Relationship.

Step 5 Layout Dashboard.

Step 6 WhatsApp Service.

Step 7 Kirim Pesan.

Step 8 Webhook.

Step 9 Riwayat Chat.

Step 10 Auto Reply.

Step 11 Refactor.

Untuk setiap step: - Tujuan - Diagram ASCII - Flow - Struktur file -
Alasan - Kode - Testing - Code Review

------------------------------------------------------------------------

# DIAGRAM

Selalu buat diagram ASCII sebelum coding.

Contoh:

Admin │ ▼ Controller │ ▼ Service │ ▼ Meta API │ ▼ Customer

------------------------------------------------------------------------

# TARGET

Setelah project selesai saya harus mampu: - Memahami WhatsApp Business
Cloud API. - Menjelaskan flow project. - Menambah fitur sendiri. -
Mengembangkan menjadi chatbot AI.

Jangan langsung membuat seluruh project sekaligus.

