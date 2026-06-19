# Deploy LMS ke GitHub dan Railway

Panduan ini untuk project `C:\xampp\htdocs\LMS`.

## 1. Persiapan Lokal

Pastikan file berikut sudah ada:

- `railway.toml`
- `nixpacks.toml`
- `scripts/railway-start.sh`
- `.env.railway.example`

Jangan commit `.env`, `vendor/`, atau `.composer-cache/`.

## 2. Push ke GitHub

```powershell
cd C:\xampp\htdocs\LMS
git status
git add .
git commit -m "Prepare LMS for Railway deployment"
git branch -M main
git remote add origin https://github.com/Aldoy01/LMS-APP.git
git push -u origin main
```

Jika remote sudah ada:

```powershell
git remote set-url origin https://github.com/Aldoy01/LMS-APP.git
git push -u origin main
```

## 3. Buat Project Railway

1. Buka Railway.
2. New Project.
3. Deploy from GitHub repo.
4. Pilih repo LMS.
5. Tambahkan service PostgreSQL di project yang sama.

Railway/Nixpacks akan membaca `railway.toml` dan memakai `scripts/railway-start.sh` sebagai start command.

Catatan: karena project ini juga punya `Dockerfile`, Railway dapat memilih Docker build. Dockerfile sudah diarahkan agar start command tetap memakai `scripts/railway-start.sh`, sehingga aplikasi listen ke `$PORT` Railway.

## 4. Environment Variables Railway

Tambahkan variable berikut di service aplikasi:

```env
APP_NAME="Trama Verse LMS"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:ISI_DENGAN_APP_KEY
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
LOG_CHANNEL=stderr
LOG_LEVEL=error
DB_CONNECTION=pgsql
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
FILESYSTEM_DISK=local
MAIL_MAILER=smtp
MAIL_HOST=smtp.provider-email-anda.com
MAIL_PORT=587
MAIL_USERNAME=USERNAME_SMTP
MAIL_PASSWORD=PASSWORD_SMTP
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@domain-anda.com
MAIL_FROM_NAME="${APP_NAME}"
```

Tambahkan juga variable PostgreSQL dari Railway PostgreSQL service:

```env
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}
```

Nama `Postgres` bisa berbeda sesuai nama service database di Railway. Sesuaikan dengan variable reference yang muncul di Railway.

Konfigurasi SMTP wajib diisi agar password akun dan notifikasi aktivasi kelas benar-benar terkirim. Jika `MAIL_MAILER=log`, email hanya ditulis ke log Railway dan tidak masuk ke inbox peserta.

## 5. Membuat APP_KEY

Di lokal:

```powershell
docker compose exec app php artisan key:generate --show
```

Copy hasilnya ke variable `APP_KEY` di Railway.

## 6. Deploy

Setelah variables lengkap:

1. Trigger deploy dari Railway.
2. Railway menjalankan composer install.
3. Saat start, script akan menjalankan:
   - `php artisan migrate --force` jika variable database lengkap
   - `php artisan db:seed --force` untuk akun demo dan data awal
   - cache config, route, dan view
   - `php artisan serve` memakai `$PORT` dari Railway

Jika service crash, cek tab **Deploy Logs** untuk error build dan tab **Runtime Logs** untuk error start aplikasi. Runtime crash paling sering terjadi karena `APP_KEY` kosong atau variable PostgreSQL belum sesuai service Railway.

Jangan jalankan `php artisan storage:link` di Railway runtime jika muncul error `symlink(): Permission denied`; script project ini sudah tidak menjalankannya.

## 7. Volume untuk Menyimpan Materi

Filesystem bawaan deployment Railway bersifat sementara. Agar video, PDF, avatar, dan gambar yang di-upload tidak hilang saat redeploy, pasang Railway Volume pada service aplikasi:

1. Buka project Railway.
2. Pada project canvas, klik kanan lalu pilih **New Volume**. Volume juga dapat dibuat melalui `Ctrl/Cmd + K`.
3. Hubungkan volume ke service aplikasi LMS, bukan ke service PostgreSQL.
4. Isi **Mount Path** dengan:

   ```text
   /app/storage
   ```

5. Deploy ulang service aplikasi.
6. Upload satu materi percobaan, lakukan redeploy, lalu pastikan materi masih dapat dibuka.

Script `scripts/railway-start.sh` otomatis membuat folder Laravel yang diperlukan ketika volume masih kosong.

Materi yang sudah hilang sebelum volume dipasang tidak dapat dipulihkan otomatis dan perlu di-upload satu kali lagi.

## 8. Akun Demo

Seeder akan berjalan hanya jika Anda menjalankan seed manual. Untuk production, sebaiknya buat admin manual atau jalankan:

```bash
php artisan db:seed --force
```

Akun demo dari seeder:

- `admin@tramatekid.test` / `password`
- `peserta@example.test` / `password`

Ganti password setelah production aktif.

## 9. URL Penting

- Landing LMS: `https://domain-railway`
- Login peserta: `/login`
- Dashboard peserta: `/peserta/dashboard`
- Login admin: `/admin/login`
- Manajemen course: `/admin/courses`

## 10. Catatan

Dokumentasi Nixpacks untuk PHP/Laravel menyarankan document root Laravel diarahkan ke folder `public`; project ini mengatur `NIXPACKS_PHP_ROOT_DIR=/app/public` lewat `nixpacks.toml`.
