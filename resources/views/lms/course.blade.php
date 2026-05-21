@extends('layouts.lms', ['title' => $course->title])

@section('content')
    <section class="hero">
        <div>
            <span class="eyebrow" style="color:var(--gold)">{{ $course->level }} · {{ ucfirst($course->status) }}</span>
            <h1>{{ $course->title }}</h1>
            <p>{{ $course->description ?: $course->summary }}</p>
            <div class="chips">
                <span class="chip">Mentor: {{ optional($course->mentor)->name ?? 'Belum ditentukan' }}</span>
                <span class="chip">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                <span class="chip">{{ $course->modules->count() }} modul</span>
            </div>
        </div>
        <div class="hero-panel">
            <strong>Checkout Manual</strong>
            <p style="margin:0;color:var(--hero-copy)">Peserta melakukan transfer, admin memverifikasi payment, lalu enrollment aktif otomatis melalui dashboard admin.</p>
        </div>
    </section>

    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Kurikulum</span>
                    <h2>Modul dan Lesson</h2>
                </div>
            </div>
            <div class="list">
                @foreach($course->modules as $module)
                    <article class="card">
                        <span class="eyebrow">{{ $module->category }} / Modul {{ $module->sort_order }} / {{ $module->duration_minutes }} menit</span>
                        <h3>{{ $module->title }}</h3>
                        <p>{{ $module->summary }}</p>
                        <div class="list">
                            @foreach($module->lessons as $lesson)
                                <div class="list-row">
                                    <strong>{{ $lesson->title }}</strong>
                                    <span class="muted">{{ ucfirst($lesson->content_type) }} · {{ $lesson->duration_minutes }} menit</span>
                                    <div class="meta">
                                        @foreach($lesson->materials as $material)
                                            <span class="badge">{{ strtoupper($material->type) }}: {{ $material->title }}</span>
                                        @endforeach
                                    </div>
                                    <div class="meta">
                                        <a class="button" href="{{ route('lms.lessons.show', [$course, $lesson]) }}">Buka Materi</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Live Q&A</span>
                    <h2>Jadwal Sesi</h2>
                </div>
            </div>
            <div class="grid courses">
                @foreach($course->liveSessions as $session)
                    <div class="card">
                        <span class="eyebrow">{{ optional($session->starts_at)->format('d M Y H:i') }}</span>
                        <h3>{{ $session->title }}</h3>
                        <p>{{ $session->description }}</p>
                        @if($session->meeting_url)
                            <a class="button" href="{{ $session->meeting_url }}">Link Meeting</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    </main>
@endsection
