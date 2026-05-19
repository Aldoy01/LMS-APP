# Setup XAMPP Lokal

Project berada di `C:\xampp\htdocs\LMS`.

## 1. Dependency

Jalankan dari terminal:

```powershell
cd C:\xampp\htdocs\LMS
composer install
```

Jika Composer masih gagal dengan `curl error 60`, perbarui CA certificate PHP/Composer. Di mesin ini Composer sudah dicoba memakai:

- `C:\xampp\apache\bin\curl-ca-bundle.crt`
- `C:\Users\aldia\lksp\backend\venv\Lib\site-packages\pip\_vendor\certifi\cacert.pem`

Namun koneksi HTTPS Packagist masih ditolak oleh certificate chain lokal.

## 2. Environment

Untuk XAMPP/non-Docker:

```powershell
Copy-Item .env.xampp .env
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan PostgreSQL lokal.

Untuk Docker:

```powershell
Copy-Item .env.example .env
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

Stack Docker lokal memakai `php artisan serve` di container app pada port `8081`, agar tidak bergantung pada image Nginx/Redis lokal.

## 3. Database PostgreSQL

Buat database:

```sql
CREATE DATABASE lms_db;
```

Lalu jalankan:

```powershell
php artisan migrate --seed
```

## 4. Akses Browser

- Dashboard: `http://localhost/LMS`
- Admin course: `http://localhost/LMS/admin/courses`
- Alternatif jika rewrite belum aktif: `http://localhost/LMS/public`
- Docker: `http://localhost:8081`
