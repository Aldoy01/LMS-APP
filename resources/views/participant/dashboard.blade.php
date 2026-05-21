@extends('layouts.lms', ['title' => 'Dashboard Peserta'])

@section('content')
    @php
        $firstEnrollment = $enrollments->first();
        $firstCourse = optional($firstEnrollment)->course;
        $totalLessons = $enrollments->sum(fn ($enrollment) => optional($enrollment->course)->modules?->sum(fn ($module) => $module->lessons->count()) ?? 0);
        $completedLessons = $enrollments->sum(fn ($enrollment) => $enrollment->progress->where('progress_percent', 100)->count());
        $overallProgress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
        $ctaUrl = $firstCourse ? route('lms.courses.show', $firstCourse) : route('lms.dashboard') . '#program';
        $ctaLabel = $firstCourse ? 'Lanjutkan Modul' : 'Mulai Belajar';
        $categoryCounts = $modules->groupBy('category')->map->count();
    @endphp

    <section class="hero">
        <div>
            <span class="eyebrow" style="color:var(--gold)">Cyber Learning Dashboard</span>
            <h1>TECHVERSE Learning</h1>
            <p>
                Selamat datang, {{ $user->name }}. Dashboard ini menampilkan modul yang tersedia,
                urutan belajar cyber security, progress, pengumuman, dan bantuan admin dalam satu halaman.
            </p>
            <div class="chips">
                <span class="chip">{{ $enrollments->count() }} course aktif</span>
                <span class="chip">{{ $modules->count() }} cyber module</span>
                <span class="chip">{{ $overallProgress }}% progress belajar</span>
                <span class="chip">Secure Account</span>
            </div>
            <div class="meta" style="margin-top:22px">
                <a class="button" href="{{ $ctaUrl }}">{{ $ctaLabel }}</a>
                <a class="button" style="background:var(--night)" href="#daftar-modul">Lihat Daftar Modul</a>
                <a class="button" style="background:var(--accent)" href="{{ $support['whatsapp'] }}" target="_blank" rel="noopener">WhatsApp Admin</a>
            </div>
        </div>
        <div class="hero-panel">
            <img class="hero-logo" src="{{ asset('images/techverse-learning-logo.jpeg') }}" alt="TECHVERSE Learning">
            <strong>Cyber Learning Path</strong>
            <p style="margin:0;color:var(--hero-copy)">
                Mulai dari Basic, lanjutkan Intermediate, lalu masuk ke Practical untuk tools,
                pentest workflow, case study, dan reporting.
            </p>
            <div class="chips">
                <a class="chip" href="#dashboard">Dashboard</a>
                <a class="chip" href="#daftar-modul">Modul</a>
                <a class="chip" href="#pengumuman">Pengumuman</a>
                <a class="chip" href="#bantuan">Bantuan</a>
            </div>
        </div>
    </section>

    <main class="main">
        <section class="section" id="dashboard">
            <div class="grid metrics">
                <a class="metric" href="#daftar-modul">
                    <span>Modul</span>
                    <strong>{{ $modules->count() }}</strong>
                </a>
                <a class="metric" href="#daftar-modul">
                    <span>Progress</span>
                    <strong>{{ $overallProgress }}%</strong>
                </a>
                <a class="metric" href="#kategori">
                    <span>Kategori</span>
                    <strong>{{ $categoryCounts->count() }}</strong>
                </a>
                <a class="metric" href="#bantuan">
                    <span>Bantuan</span>
                    <strong>CS</strong>
                </a>
            </div>
        </section>

        <section class="section" id="kategori">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Kategori Modul</span>
                    <h2>Urutan Belajar</h2>
                </div>
                <a class="button" href="#daftar-modul">Buka Modul</a>
            </div>
            <div class="grid courses">
                @foreach(['Basic' => 'Mulai dari konsep cyber security, networking, Linux, web security, dan tipe hacker.', 'Intermediate' => 'Lanjutkan ke SQL Injection, XSS, vulnerability, reconnaissance, dan traffic analysis.', 'Practical' => 'Masuk ke Burp Suite, Nmap, workflow pentest, case study, dan reporting.'] as $category => $description)
                    <article class="card">
                        <span class="eyebrow">{{ $category }}</span>
                        <h3>{{ $categoryCounts[$category] ?? 0 }} modul</h3>
                        <p>{{ $description }}</p>
                        <a class="button" href="#daftar-modul">Lihat {{ $category }}</a>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section" id="modul">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Course Terdaftar</span>
                    <h2>Lanjutkan Belajar</h2>
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
                        <span class="eyebrow">{{ $enrollment->access_type }} / {{ optional($enrollment->started_at)->format('d M Y') ?? 'Belum mulai' }}</span>
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->summary }}</p>
                        <div class="meta">
                            <span class="badge">Mentor: {{ optional($course->mentor)->name ?? 'Belum ditentukan' }}</span>
                            <span class="badge">{{ $lessonCount }} lesson</span>
                            <span class="badge">{{ $progress }}% selesai</span>
                        </div>
                        <div style="height:10px;background:var(--line);border-radius:999px;overflow:hidden">
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
                        <a class="button" href="{{ route('lms.dashboard') }}#program">Mulai Belajar</a>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="section" id="daftar-modul">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Daftar Modul</span>
                    <h2>Modul Tersedia Berurutan</h2>
                </div>
                <a class="button" href="{{ $ctaUrl }}">{{ $ctaLabel }}</a>
            </div>
            <div class="list">
                @forelse($modules as $item)
                    <article class="list-row">
                        <div class="section-head" style="margin-bottom:8px;align-items:center">
                            <div>
                                <span class="eyebrow">{{ $item['category'] }} / Modul {{ $item['module']->sort_order }}</span>
                                <strong>{{ $item['module']->title }}</strong>
                                <span class="muted">{{ $item['course']->title }}</span>
                            </div>
                            <span class="badge">{{ $item['progress'] }}% selesai</span>
                        </div>
                        <p class="muted" style="margin:0 0 12px">{{ $item['module']->summary }}</p>
                        <div class="meta">
                            <span class="badge">{{ $item['duration_minutes'] }} menit</span>
                            <span class="badge">{{ $item['lesson_count'] }} lesson</span>
                            <span class="badge">{{ $item['completed_count'] }} selesai</span>
                            <a class="button" href="{{ route('lms.courses.show', $item['course']) }}">Buka Modul</a>
                        </div>
                        <div style="height:8px;background:var(--line);border-radius:999px;overflow:hidden">
                            <div style="height:100%;width:{{ $item['progress'] }}%;background:var(--brand)"></div>
                        </div>
                    </article>
                @empty
                    <div class="card">
                        <h3>Belum ada modul aktif</h3>
                        <p>Modul akan tampil setelah admin mengaktifkan enrollment course untuk akun Anda.</p>
                        <a class="button" href="{{ $support['whatsapp'] }}" target="_blank" rel="noopener">Hubungi Admin</a>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="section" id="pengumuman">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Pengumuman Admin</span>
                    <h2>Informasi Penting</h2>
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
