@extends('layouts.lms', ['title' => 'TECHVERSE Learning'])

@section('content')
    @php
        $memberUrl = auth()->check() ? route('participant.home') : route('login');
        $memberLabel = auth()->check() ? 'Masuk Beranda Belajar' : 'Login Member Area';
        $contactUrl = 'https://wa.me/628513332305';
    @endphp

    <style>
        .academy-hero {
            grid-template-columns: minmax(0, .95fr) minmax(320px, 1.05fr);
            align-items: center;
            min-height: calc(100vh - 92px);
            padding: clamp(34px, 6vw, 78px) clamp(24px, 5vw, 80px);
            background: #f8fbff;
            box-shadow: none;
        }
        .academy-hero h1 {
            max-width: 780px;
            color: #1f1f28;
            font-size: clamp(38px, 4.6vw, 64px);
            line-height: 1.18;
            letter-spacing: 0;
        }
        .academy-hero p {
            max-width: 720px;
            color: #252533;
            font-size: clamp(20px, 2.2vw, 32px);
            line-height: 1.45;
        }
        .academy-cta {
            margin-top: 30px;
            min-width: 220px;
            min-height: 64px;
            border-radius: 7px;
            font-size: 24px;
            background: var(--brand-dark);
        }
        .academy-visual {
            display: grid;
            place-items: center;
            min-height: 520px;
        }
        .academy-visual-frame {
            position: relative;
            width: min(720px, 100%);
            aspect-ratio: 1.25;
            border-radius: 26px;
            overflow: hidden;
            background:
                radial-gradient(circle at 62% 36%, rgba(0, 212, 255, .22), transparent 15rem),
                linear-gradient(145deg, #ffffff, #edf6ff);
        }
        .academy-visual-frame::before {
            content: "";
            position: absolute;
            inset: 6%;
            border-radius: 24px;
            background: url('{{ asset('images/techverse-hero-bg.webp') }}') center / cover no-repeat;
            box-shadow: 0 26px 60px rgba(16, 85, 245, .14);
        }
        .academy-visual-frame::after {
            content: "SQL  XSS  LAB";
            position: absolute;
            right: 18px;
            bottom: 28px;
            padding: 12px 16px;
            border-radius: 999px;
            color: #ffffff;
            background: var(--brand-dark);
            box-shadow: 0 16px 30px rgba(49, 87, 220, .24);
            font-weight: 900;
            letter-spacing: .04em;
        }
        @media (max-width: 820px) {
            .academy-hero {
                min-height: auto;
                grid-template-columns: 1fr;
                padding: 34px 18px;
            }
            .academy-hero p {
                font-size: 18px;
            }
            .academy-cta {
                width: auto;
                min-width: 190px;
                min-height: 54px;
                font-size: 19px;
            }
            .academy-visual {
                min-height: 320px;
            }
        }
    </style>

    <section class="hero academy-hero">
        <div>
            <h1>Bangun Karirmu sebagai Cyber Security Profesional</h1>
            <p>
                Pelajari Konsep dan Teknik Cyber Security dari para Pengajar Terbaik yang
                berpengalaman di Industri sampai Bisa!
            </p>
            <a class="button academy-cta" href="#program">Belajar Sekarang</a>
        </div>
        <div class="academy-visual" aria-hidden="true">
            <div class="academy-visual-frame"></div>
        </div>
    </section>

    <main class="main">
        <section class="grid metrics" id="tentang">
            <div class="metric course-icon"><span>Course Aktif</span><strong>{{ $metrics['courses'] }}</strong></div>
            <div class="metric users-icon"><span>Peserta</span><strong>{{ $metrics['participants'] }}</strong></div>
            <div class="metric format-icon"><span>Format Belajar</span><strong>4+</strong></div>
            <div class="metric help-icon"><span>Bantuan</span><strong>CS</strong></div>
        </section>

        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Kenapa TECHVERSE Learning</span>
                    <h2>Cyber Learning yang Terarah</h2>
                </div>
            </div>
            <div class="grid courses">
                <article class="card">
                    <h3>Learning path bertahap</h3>
                    <p>Peserta mulai dari konsep keamanan, jaringan, Linux, web security, lalu masuk ke praktik tools dan reporting.</p>
                    <a class="button" href="#program">Lihat Modul</a>
                </article>
                <article class="card">
                    <h3>Dashboard belajar aman</h3>
                    <p>Setelah login, peserta bisa melihat course aktif, lesson, progress, dan modul berikutnya dengan tampilan ringkas.</p>
                    <a class="button" href="{{ $memberUrl }}">{{ $memberLabel }}</a>
                </article>
                <article class="card">
                    <h3>Update materi dan support</h3>
                    <p>Pengumuman admin membantu peserta mengikuti update tools, materi praktik, dan jadwal pendampingan.</p>
                    <a class="button" href="#kontak">Kontak Bantuan</a>
                </article>
                <article class="card">
                    <h3>Akses peserta resmi</h3>
                    <p>Login hanya untuk user terdaftar sehingga akses kelas cyber security tetap terkontrol oleh admin LMS.</p>
                    <a class="button" href="{{ route('login') }}">Login Peserta</a>
                </article>
            </div>
        </section>

        <section class="section" id="program">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Program Belajar</span>
                    <h2>Pilih Modul Sesuai Kebutuhan</h2>
                </div>
                <a class="button" href="{{ $memberUrl }}">{{ $memberLabel }}</a>
            </div>
            <div class="grid courses">
                @forelse($courses as $course)
                    <article class="card">
                        <span class="eyebrow">{{ $course->level }} / {{ ucfirst($course->status) }}</span>
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->summary }}</p>
                        <div class="meta">
                            <span class="badge">{{ $course->modules->count() }} modul</span>
                            <span class="badge">{{ $course->modules->sum(fn ($module) => $module->lessons->count()) }} lesson</span>
                            <span class="badge">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                        </div>
                        <a class="button" href="{{ route('purchase.create', $course) }}">Beli Paket</a>
                    </article>
                @empty
                    <div class="card">
                        <h3>Program segera tersedia</h3>
                        <p>Admin LMS akan menambahkan program belajar yang bisa diakses peserta terdaftar.</p>
                        <a class="button" href="{{ route('admin.login') }}">Login Admin</a>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="section grid split">
            <div>
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Alur Peserta</span>
                        <h2>Cara Mulai Belajar</h2>
                    </div>
                </div>
                <div class="list">
                    <div class="list-row">
                        <strong>1. Login sebagai peserta</strong>
                        <span class="muted">Gunakan email dan password resmi yang dibuat oleh admin.</span>
                    </div>
                    <div class="list-row">
                        <strong>2. Buka Beranda Belajar</strong>
                        <span class="muted">Lihat course aktif, progress belajar, dan pengumuman admin.</span>
                    </div>
                    <div class="list-row">
                        <strong>3. Lanjutkan modul</strong>
                        <span class="muted">Mulai dari lesson pertama dan ikuti arahan belajar secara bertahap.</span>
                    </div>
                </div>
            </div>
            <div id="kontak">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Bantuan</span>
                        <h2>Kontak Admin</h2>
                    </div>
                </div>
                <div class="card">
                    <h3>Butuh akses atau kendala login?</h3>
                    <p>Hubungi admin untuk verifikasi akun, reset password, atau pengecekan course yang belum muncul.</p>
                    <div class="meta">
                        <a class="button" href="{{ $contactUrl }}" target="_blank" rel="noopener">WhatsApp Admin</a>
                        <a class="button" style="background:var(--night)" href="mailto:admin@techverselearning.test">Email Admin</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
