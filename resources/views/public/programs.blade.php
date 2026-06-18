@extends('layouts.lms', ['title' => 'Program Belajar'])

@section('content')
<main class="main">
    <section class="hero">
        <div>
            <span class="eyebrow">Program Trama Verse</span>
            <h1>Temukan Jalur Belajar yang Sesuai Tujuanmu</h1>
            <p>Pilih course, pelajari struktur modul, dan ikuti webinar atau event untuk memperkuat praktik bersama komunitas.</p>
            <div class="meta" style="margin-top:22px">
                <a class="button" href="#course">Lihat Course</a>
                <a class="button" style="background:var(--night)" href="#event">Webinar &amp; Event</a>
            </div>
        </div>
        <div class="hero-panel">
            <span class="eyebrow">Learning Path</span>
            <h3>Belajar bertahap, bukan sekadar menonton.</h3>
            <p>Setiap program menggabungkan modul, lesson, materi praktik, dan sesi belajar yang dapat diikuti peserta.</p>
        </div>
    </section>

    <section class="section" id="course">
        <div class="section-head">
            <div>
                <span class="eyebrow">Course</span>
                <h2>Program yang Tersedia</h2>
            </div>
        </div>
        <div class="grid courses">
            @forelse($courses as $course)
                <article class="card">
                    <span class="eyebrow">{{ $course->level }}</span>
                    <h3>{{ $course->title }}</h3>
                    <p>{{ $course->summary }}</p>
                    <div class="meta">
                        <span class="badge">{{ $course->modules->count() }} modul</span>
                        <span class="badge">{{ $course->modules->sum(fn ($module) => $module->lessons->count()) }} lesson</span>
                        <span class="badge">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                    </div>
                    <a class="button" href="{{ route('purchase.create', $course) }}">Lihat Program</a>
                </article>
            @empty
                <article class="card">
                    <h3>Program sedang disiapkan</h3>
                    <p>Course baru akan tampil di halaman ini setelah dipublikasikan oleh admin.</p>
                </article>
            @endforelse
        </div>
    </section>

    <section class="section" id="module">
        <div class="section-head">
            <div>
                <span class="eyebrow">Learning Module</span>
                <h2>Struktur Pembelajaran</h2>
            </div>
        </div>
        <div class="grid courses">
            @forelse($courses as $course)
                <article class="card">
                    <h3>{{ $course->title }}</h3>
                    @forelse($course->modules->take(4) as $module)
                        <div class="list-row">
                            <strong>{{ $module->title }}</strong>
                            <span class="muted">{{ $module->lessons->count() }} lesson · {{ $module->duration_minutes ?? 0 }} menit</span>
                        </div>
                    @empty
                        <p>Struktur modul sedang disusun.</p>
                    @endforelse
                </article>
            @empty
                <article class="card"><p>Modul akan muncul saat program telah tersedia.</p></article>
            @endforelse
        </div>
    </section>

    <section class="section" id="event">
        <div class="section-head">
            <div>
                <span class="eyebrow">Webinar &amp; Event</span>
                <h2>Agenda Belajar Mendatang</h2>
            </div>
        </div>
        <div class="grid courses">
            @forelse($liveSessions as $session)
                <article class="card">
                    <span class="eyebrow">{{ optional($session->starts_at)->format('d M Y · H:i') }} WIB</span>
                    <h3>{{ $session->title }}</h3>
                    <p>{{ $session->description ?: 'Sesi belajar langsung bersama mentor Trama Verse.' }}</p>
                    <span class="badge">{{ optional($session->course)->title ?? 'Event Umum' }}</span>
                </article>
            @empty
                <article class="card">
                    <h3>Belum ada agenda terdekat</h3>
                    <p>Informasi webinar dan event berikutnya akan diumumkan di halaman ini.</p>
                </article>
            @endforelse
        </div>
    </section>
</main>
@endsection
