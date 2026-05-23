@extends('layouts.lms', ['title' => 'TECHVERSE Learning'])

@section('content')
    @php
        $memberUrl = auth()->check() ? route('participant.home') : route('login');
        $memberLabel = auth()->check() ? 'Masuk Beranda Belajar' : 'Login Member Area';
        $contactUrl = 'https://wa.me/628513332305';
    @endphp

    <section class="hero">
        <div>
            <span class="eyebrow" style="color:var(--gold)">Cyber Security Learning Platform</span>
            <h1>TECHVERSE Learning</h1>
            <p>
                Bangun skill cyber security dari basic sampai practical lab, ikuti learning path terarah,
                dan pantau progres belajar dalam satu platform LMS modern.
            </p>
            <div class="chips">
                <span class="chip">Cyber Modules</span>
                <span class="chip">Secure Dashboard</span>
                <span class="chip">Practical Lab</span>
                <span class="chip">Learning Path</span>
                <span class="chip">Admin Support</span>
            </div>
            <div class="meta" style="margin-top:22px">
                <a class="button" href="{{ $memberUrl }}">{{ $memberLabel }}</a>
                <a class="button" style="background:var(--night)" href="#program">Lihat Program</a>
                <a class="button" style="background:var(--accent)" href="{{ $contactUrl }}" target="_blank" rel="noopener">Hubungi Admin</a>
            </div>
        </div>
        <div class="hero-panel">
            <img class="hero-logo" src="{{ asset('images/techverse-color.png') }}" alt="TECHVERSE Learning">
            <strong>Cyber Learning Hub</strong>
            <p style="margin:0;color:var(--hero-copy)">
                TECHVERSE Learning membantu peserta memahami konsep keamanan, tools, workflow pentest,
                dan dokumentasi report melalui modul yang terstruktur.
            </p>
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
