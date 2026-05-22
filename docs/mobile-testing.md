# Mobile Friendly Testing

Gunakan checklist ini setelah deploy Railway dan setelah perubahan besar pada layout LMS.

## URL yang dicek

- Public home: `/`
- Login peserta: `/login`
- Login admin: `/admin/login`
- Dashboard peserta: `/home`
- Detail course: `/courses/{slug}`
- Halaman lesson: `/courses/{slug}/lessons/{id}`
- Preview material: `/materials/{id}`

## Android

1. Buka Chrome Android.
2. Cek home tanpa zoom: hero, card, dan menu harus muat di layar.
3. Scroll menu header horizontal; item menu tidak boleh bertumpuk.
4. Login peserta memakai email dan password.
5. Buka dashboard peserta; metric, kategori, daftar modul, dan bantuan harus satu kolom rapi.
6. Buka halaman lesson; teks, tools list, dan tombol Next/Previous harus mudah diklik.
7. Buka PDF; file terbuka di tab browser atau halaman fallback tampil jika file belum tersedia.
8. Putar video upload atau embed; kontrol video harus terlihat dan bisa dipakai.

## iPhone atau Safari

1. Buka Safari iPhone.
2. Pastikan input login tidak memaksa zoom berlebihan.
3. Header tetap rapi dan menu bisa digeser horizontal.
4. Dashboard dan card modul tidak melebar keluar layar.
5. Tombol CTA minimal 44px dan nyaman diklik.
6. PDF terbuka dari link material.
7. Video bisa diputar dan iframe embed tidak keluar layar.

## Catatan Performa Mobile

- Hindari upload video besar langsung ke LMS jika jaringan peserta terbatas; gunakan embed YouTube/Google Drive untuk video panjang.
- PDF disarankan dikompresi sebelum upload.
- Jika Railway memakai storage lokal tanpa volume persistent, upload ulang file setelah redeploy atau gunakan Railway Volume.
