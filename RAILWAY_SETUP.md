# Railway Deployment Setup

## Masalah: Database Reset Setiap Deploy

Jika menggunakan SQLite, data akan **hilang setiap kali Railway deploy ulang** karena filesystem Railway bersifat **ephemeral** (tidak persistent).

**Solusi:** Gunakan PostgreSQL (gratis di Railway).

---

## Setup PostgreSQL di Railway

### 1. Tambah PostgreSQL Database

1. Buka Railway Dashboard
2. Pilih project kamu
3. Klik tombol **"New"** → **"Database"** → **"Add PostgreSQL"**
4. Railway akan otomatis generate database dan environment variables

### 2. Link Database ke Service

1. Klik service aplikasi Laravel kamu
2. Tab **"Variables"**
3. PostgreSQL variables sudah otomatis tersedia dengan format:
   - `DATABASE_URL`
   - `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD`

### 3. Set Laravel Database Variables

Di Railway service variables, tambahkan/update:

```bash
DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}
```

**Note:** Railway akan otomatis replace `${{Postgres.xxx}}` dengan nilai dari PostgreSQL service.

### 4. Environment Variables Lain yang Diperlukan

```bash
APP_NAME="Mini WhatsApp CRM"
APP_ENV=production
APP_KEY=base64:xxx  # Generate dengan: php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app

# WhatsApp Business API
WHATSAPP_API_URL=https://graph.facebook.com/v21.0
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_ACCESS_TOKEN=your_access_token
WHATSAPP_VERIFY_TOKEN=your_verify_token
WHATSAPP_BUSINESS_ACCOUNT_ID=your_business_account_id
```

### 5. Deploy

1. **Commit changes** ke Git:
   ```bash
   git add .
   git commit -m "Fix: Use PostgreSQL instead of SQLite for Railway"
   git push
   ```

2. Railway akan otomatis **re-deploy** dengan config baru

3. Database akan persistent dan **tidak reset** lagi setiap deploy

### 6. Jalankan Seeder (First Time Only)

Setelah deploy pertama kali, jalankan seeder sekali saja untuk setup chatbot:

1. Buka Railway Dashboard
2. Klik service aplikasi kamu
3. Tab **"Settings"** → Scroll ke **"Deploy"**
4. Atau gunakan Railway CLI:
   ```bash
   railway run php artisan db:seed --class=ChatbotSeeder
   ```

**Note:** Seeder tidak perlu dijalankan setiap deploy, cuma sekali saat setup awal.

---

## Verifikasi

Setelah deploy, cek di Railway logs:

```
php artisan migrate --force
Migration table created successfully.
Migrating: xxx_create_users_table
Migrated:  xxx_create_users_table
...
```

Database sekarang persistent dan data tidak akan hilang.

---

## Alternatif: Pakai MySQL

Jika mau pakai MySQL, jalankan script:

```bash
chmod +x railway-env-setup.sh
./railway-env-setup.sh
```

Tapi Railway **tidak provide MySQL gratis**, jadi harus connect ke external MySQL (seperti PlanetScale, etc).

**Rekomendasi:** Pakai PostgreSQL karena **gratis** dan **terintegrasi** dengan Railway.
