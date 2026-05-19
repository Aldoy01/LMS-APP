# Deployment Checklist LMS

## Production

1. Set environment `.env` production dan jangan commit secret.
2. Pastikan PostgreSQL tidak public dan hanya bisa diakses oleh aplikasi.
3. Aktifkan HTTPS, WAF, rate limiting, dan backup terenkripsi.
4. Jalankan:

```bash
php artisan down
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
php artisan up
```

## Backup

```bash
pg_dump -h postgres -U lms_user -d lms_db > backup_lms_$(date +%F).sql
```

## Akun Demo Seeder

- `admin@tramatekid.test` / `password`
- `mentor@tramatekid.test` / `password`
- `peserta@example.test` / `password`
- `sales@tramatekid.test` / `password`
