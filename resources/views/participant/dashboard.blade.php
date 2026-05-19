@extends('layouts.lms', ['title' => 'Kelas Saya'])

@section('content')
    <section class="hero">
        <div>
            <span class="eyebrow" style="color:var(--gold)">Dashboard Peserta</span>
            <h1>Kelas Saya</h1>
            <p>
                Selamat datang, {{ $user->name }}. Halaman ini hanya menampilkan course yang sudah aktif
                untuk akun peserta berdasarkan data enrollment.
            </p>
            <div class="chips">
                <span class="chip">{{ $enrollments->count() }} course aktif</span>
                <span class="chip">{{ $user->email }}</span>
                @if($user->company)
                    <span class="chip">{{ $user->company }}</span>
                @endif
            </div>
        </div>
        <div class="hero-panel">
            <strong>Akses Peserta</strong>
            <p style="margin:0;color:var(--hero-copy)">
                Course muncul otomatis setelah pembayaran diverifikasi dan enrollment dibuat oleh admin.
            </p>
        </div>
    </section>

    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Learning Progress</span>
                    <h2>Course Terdaftar</h2>
                </div>
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
                            <a class="button" href="{{ route('lms.courses.show', $course) }}">Masuk Course</a>
                        </div>
                    </article>
                @empty
                    <div class="card">
                        <h3>Belum ada course aktif</h3>
                        <p>Course akan tampil setelah pembayaran berhasil dan enrollment aktif.</p>
                        <a class="button" href="{{ route('lms.dashboard') }}#courses">Lihat Program</a>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
@endsection
