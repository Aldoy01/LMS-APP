# Cyber Security Playbook LMS

Aplikasi LMS berbasis Laravel dan PostgreSQL yang dibuat dari dokumen proses pengembangan dan implementasi LMS Cyber Security Playbook untuk TRAMATEKID / PT. Tera Multi Teknologi.

## Modul MVP

- Dashboard course, peserta, revenue, live Q&A, pertanyaan, case review, dan CRM pipeline.
- Database PostgreSQL untuk role, course, module, lesson, material, order, payment, enrollment, progress, live session, question, case review, lead, activity, coupon, dan certificate.
- Seeder data demo untuk admin, mentor, peserta, sales/CS, course, transaksi, progress, live session, case review, dan lead.
- Docker Compose dengan Laravel app container, PostgreSQL, dan scheduler untuk mode lokal.
- GitHub Actions CI untuk migration dan test.

## Menjalankan Dengan Docker

```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Buka `http://localhost:8081`.

## Menjalankan Di XAMPP

Project berada di `C:\xampp\htdocs\LMS`. Setelah dependency Composer tersedia, aplikasi bisa dibuka dari:

- `http://localhost/LMS`
- `http://localhost/LMS/public` jika rewrite Apache belum aktif

Panel admin MVP:

- `http://localhost/LMS/admin/courses`

Panduan detail ada di `docs/xampp-setup.md`.

## Deploy GitHub + Railway

Panduan deploy production tersedia di `docs/deploy-github-railway.md`.

## Menjalankan Lokal

Pastikan PHP 8.0+, Composer, extension `pdo_pgsql`, dan PostgreSQL tersedia.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Catatan Lingkungan Windows Ini

Composer di mesin ini gagal mengunduh dependency karena masalah CA certificate Packagist (`curl error 60`). Source aplikasi sudah dibuat, tetapi folder `vendor/` belum tersedia sampai konfigurasi sertifikat Composer/PHP dibenahi atau install dilakukan lewat Docker/lingkungan lain.

## Akun Demo Seeder

- `admin@tramatekid.test` / `password`
- `mentor@tramatekid.test` / `password`
- `peserta@example.test` / `password`
- `sales@tramatekid.test` / `password`
