@extends('layouts.lms', ['title' => 'Home LMS'])

@section('content')
    @php
        $firstEnrollment = $enrollments->first();
        $firstCourse = optional($firstEnrollment)->course;
        $totalLessons = $enrollments->sum(fn ($enrollment) => optional($enrollment->course)->modules?->sum(fn ($module) => $module->lessons->count()) ?? 0);
        $completedLessons = $enrollments->sum(fn ($enrollment) => $enrollment->progress->where('progress_percent', 100)->count());
        $overallProgress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
        $ctaUrl = $firstCourse ? route('lms.courses.show', $firstCourse) : route('lms.dashboard') . '#courses';
        $ctaLabel = $firstCourse ? 'Lanjutkan Modul' : 'Mulai Belajar';
    @endphp

    <section class="hero">
        <div>
            <span class="eyebrow" style="color:var(--gold)">Home / Beranda LMS</span>
            <h1>TECHVERSE Learning</h1>
            <p>
                Selamat datang, {{ $user->name }}. TECHVERSE Learning membantu peserta belajar terarah,
                memantau progress, dan mengakses modul resmi dari satu beranda.
            </p>
            <div class="chips">
                <span class="chip">{{ $enrollments->count() }} course aktif</span>
                <span class="chip">{{ $overallProgress }}% progress belajar</span>
                <span class="chip">{{ $user->email }}</span>
                @if($user->company)
                    <span class="chip">{{ $user->company }}</span>
                @endif
            </div>
            <div class="meta" style="margin-top:22px">
                <a class="button" href="{{ $ctaUrl }}">{{ $ctaLabel }}</a>
                <a class="button" style="background:#172033" href="#arah-belajar">Arahan Peserta Baru</a>
            </div>
        </div>
        <div class="hero-panel">
            <img class="hero-logo" src="{{ asset('images/techverse-learning-logo.jpeg') }}" alt="TECHVERSE Learning">
            <strong>Tujuan Platform</strong>
            <p style="margin:0;color:var(--hero-copy)">
                Menyediakan ruang belajar digital untuk mengikuti modul, mengecek progres, menerima
                pengumuman admin, dan meminta bantuan saat ada kendala akses.
            </p>
        </div>
    </section>

    <main class="main">
        <section class="section">
            <div class="grid metrics">
                <a class="metric" href="#modul">
                    <span>Modul</span>
                    <strong>{{ $totalLessons }}</strong>
                </a>
                <a class="metric" href="{{ route('participant.dashboard') }}">
                    <span>Dashboard</span>
                    <strong>{{ $overallProgress }}%</strong>
                </a>
                <a class="metric" href="#profil">
                    <span>Profil</span>
                    <strong>{{ $enrollments->count() }}</strong>
                </a>
                <a class="metric" href="#bantuan">
                    <span>Bantuan</span>
                    <strong>CS</strong>
                </a>
            </div>
        </section>

        <section class="section" id="modul">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Modul Belajar</span>
                    <h2>Lanjutkan Course Terdaftar</h2>
                </div>
                <a class="button" href="{{ $ctaUrl }}">{{ $ctaLabel }}</a>
            </div>

            <div class="grid courses">
                @forelse($enrollments as $enrollment)
                    @php
                        $course = $enrollment->course;
                        $lessonCount = $course->modules->sum(fn ($module) => $module->lessons->count());
                        $completedCount = $enrollment->progress->where('progress_percent', 100)->count();
                        $progress = $lessonCount > 0 ? round(($completedCount / $lessonCount) * 100) : 0;
                    @endphp

                    <article class="card">
                        <span class="eyebrow">{{ $enrollment->access_type }} · {{ optional($enrollment->started_at)->format('d M Y') ?? 'Belum mulai' }}</span>
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->summary }}</p>
                        <div class="meta">
                            <span class="badge">Mentor: {{ optional($course->mentor)->name ?? 'Belum ditentukan' }}</span>
                            <span class="badge">{{ $lessonCount }} lesson</span>
                            <span class="badge">{{ $progress }}% selesai</span>
                        </div>
                        <div style="height:10px;background:#e9edf5;border-radius:999px;overflow:hidden">
                            <div style="height:100%;width:{{ $progress }}%;background:var(--brand)"></div>
                        </div>
                        <div class="meta">
                            <a class="button" href="{{ route('lms.courses.show', $course) }}">Lanjutkan Modul</a>
                        </div>
                    </article>
                @empty
                    <div class="card">
                        <h3>Belum ada course aktif</h3>
                        <p>Course akan tampil setelah pembayaran berhasil dan enrollment aktif.</p>
                        <a class="button" href="{{ route('lms.dashboard') }}#courses">Mulai Belajar</a>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="section" id="arah-belajar">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Arahan Belajar</span>
                    <h2>Mulai dari Mana?</h2>
                </div>
            </div>
            <div class="grid split">
                <div class="card">
                    <h3>1. Buka modul pertama</h3>
                    <p>Pilih tombol “Lanjutkan Modul”, baca ringkasan course, lalu mulai dari lesson paling awal.</p>
                    <a class="button" href="{{ $ctaUrl }}">{{ $ctaLabel }}</a>
                </div>
                <div class="card">
                    <h3>2. Pantau progress belajar</h3>
                    <p>Selesaikan lesson bertahap dan gunakan progress bar untuk melihat perkembangan belajar.</p>
                    <a class="button" href="#modul">Lihat Progress</a>
                </div>
            </div>
        </section>

        <section class="section" id="pengumuman">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Pengumuman Admin</span>
                    <h2>Update Penting</h2>
                </div>
            </div>
            <div class="list">
                @foreach($announcements as $announcement)
                    <div class="list-row">
                        <strong>{{ $announcement['title'] }}</strong>
                        <span class="muted">{{ $announcement['body'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="section" id="profil">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Profil Peserta</span>
                    <h2>Informasi Akun</h2>
                </div>
            </div>
            <div class="card">
                <div class="meta">
                    <span class="badge">{{ $user->name }}</span>
                    <span class="badge">{{ $user->email }}</span>
                    <span class="badge">{{ optional($user->role)->label ?? 'Peserta' }}</span>
                    @if($user->company)
                        <span class="badge">{{ $user->company }}</span>
                    @endif
                </div>
                <p class="muted">Jika nama, email, atau akses course belum sesuai, hubungi admin melalui kontak bantuan.</p>
            </div>
        </section>

        <section class="section" id="bantuan">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Kontak Bantuan</span>
                    <h2>Butuh Bantuan?</h2>
                </div>
            </div>
            <div class="grid split">
                <div class="card">
                    <h3>WhatsApp Admin</h3>
                    <p>Gunakan WhatsApp untuk kendala akses login, enrollment, atau modul yang belum tampil.</p>
                    <a class="button" href="{{ $support['whatsapp'] }}" target="_blank" rel="noopener">{{ $support['whatsapp_label'] }}</a>
                </div>
                <div class="card">
                    <h3>Email Admin</h3>
                    <p>Kirim detail kendala beserta email akun peserta agar admin bisa melakukan pengecekan.</p>
                    <a class="button" href="{{ $support['email'] }}">{{ $support['email_label'] }}</a>
                </div>
            </div>
        </section>
    </main>
@endsection
