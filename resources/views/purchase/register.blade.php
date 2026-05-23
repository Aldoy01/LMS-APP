@extends('layouts.lms', ['title' => 'Register TECHVERSE Learning'])

@section('content')
    <style>
        .register-hero {
            min-height: 360px;
            grid-template-columns: minmax(0, 1fr);
            align-items: center;
            padding: clamp(34px, 6vw, 72px) clamp(20px, 5vw, 80px);
            background:
                radial-gradient(circle at 78% 24%, rgba(0, 212, 255, .2), transparent 18rem),
                linear-gradient(135deg, #ffffff 0%, #eef6ff 100%);
            box-shadow: none;
        }
        .register-hero h1 {
            max-width: 820px;
            color: #1f1f28;
            font-size: clamp(34px, 4.6vw, 58px);
            line-height: 1.16;
        }
        .register-hero p {
            max-width: 760px;
            color: #252533;
            font-size: clamp(18px, 2vw, 24px);
            line-height: 1.5;
        }
        .register-steps {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 24px;
        }
        .package-card {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }
        .package-card .button {
            margin-top: auto;
        }
        @media (max-width: 820px) {
            .register-steps {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="hero register-hero">
        <div>
            <span class="eyebrow">Daftar Peserta</span>
            <h1>Pilih paket kelas dan mulai proses registrasi.</h1>
            <p>
                Setelah memilih paket, isi data peserta, buat password akun, lalu lanjutkan pembayaran.
                Admin akan memverifikasi pembayaran dan mengaktifkan akses kelas Anda.
            </p>
        </div>
    </section>

    <main class="main">
        <section class="grid register-steps">
            <div class="card">
                <span class="eyebrow">Langkah 1</span>
                <h3>Pilih Paket</h3>
                <p>Pilih course cyber security yang sesuai kebutuhan belajar Anda.</p>
            </div>
            <div class="card">
                <span class="eyebrow">Langkah 2</span>
                <h3>Isi Registrasi</h3>
                <p>Masukkan nama, email, nomor WhatsApp, dan password akun peserta.</p>
            </div>
            <div class="card">
                <span class="eyebrow">Langkah 3</span>
                <h3>Konfirmasi Bayar</h3>
                <p>Upload atau kirim bukti pembayaran agar admin bisa membuka akses kelas.</p>
            </div>
        </section>

        <section class="section" id="program">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Paket Kelas</span>
                    <h2>Daftar Program TECHVERSE Learning</h2>
                </div>
                <a class="button" href="{{ route('login') }}">Sudah Punya Akun? Login</a>
            </div>

            <div class="grid courses">
                @forelse($courses as $course)
                    <article class="card package-card">
                        <span class="eyebrow">{{ $course->level }} / {{ ucfirst($course->status) }}</span>
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->summary }}</p>
                        <div class="meta">
                            <span class="badge">{{ $course->modules->count() }} modul</span>
                            <span class="badge">{{ $course->modules->sum(fn ($module) => $module->lessons->count()) }} lesson</span>
                            <span class="badge">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                        </div>
                        <a class="button" href="{{ route('purchase.create', $course) }}">Daftar Paket Ini</a>
                    </article>
                @empty
                    <article class="card">
                        <h3>Paket belum tersedia</h3>
                        <p>Admin belum menerbitkan paket kelas. Silakan cek kembali nanti atau hubungi admin.</p>
                        <a class="button" href="{{ route('lms.dashboard') }}#kontak">Kontak Admin</a>
                    </article>
                @endforelse
            </div>
        </section>
    </main>
@endsection
