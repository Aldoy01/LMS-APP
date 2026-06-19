@component('mail::message')
# Pembayaran berhasil diverifikasi

Halo **{{ $order->user->name }}**,

Pembayaran untuk paket **{{ $order->course->title }}** dengan invoice **{{ $order->invoice_number }}** sudah dikonfirmasi. Akun dan akses kelas Anda sekarang aktif.

@component('mail::panel')
Email login: **{{ $order->user->email }}**

@if($temporaryPassword)
Password sementara: **{{ $temporaryPassword }}**
@else
Gunakan password akun Anda yang sudah terdaftar.
@endif
@endcomponent

@component('mail::button', ['url' => route('login')])
Login dan Buka Kelas
@endcomponent

@if($temporaryPassword)
Demi keamanan, segera ganti password sementara melalui halaman profil setelah login.
@endif

Jika Anda tidak merasa melakukan pembelian ini, silakan hubungi admin Trama Verse.

Salam,<br>
{{ config('app.name') }}
@endcomponent
