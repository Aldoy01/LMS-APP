@extends('layouts.lms', ['title' => 'Akses Kelas - ' . $course->title])

@section('content')
@php
    $coverImage = $course->cover_image
        ? (Str::startsWith($course->cover_image, ['http://', 'https://']) ? $course->cover_image : asset($course->cover_image))
        : '';
    $totalDuration = $course->modules->sum('duration_minutes');
@endphp

<style>
    .course-access { width:min(1180px,calc(100% - 32px)); margin:0 auto; padding:36px 0 60px; }
    .access-hero { overflow:hidden; border:1px solid rgba(47,123,255,.16); border-radius:22px; background:#fff; box-shadow:0 18px 46px rgba(16,85,245,.1); }
    .access-cover { position:relative; min-height:300px; display:grid; grid-template-columns:minmax(0,1.25fr) minmax(290px,.75fr); align-items:center; gap:28px; padding:clamp(28px,5vw,62px); background:radial-gradient(circle at 72% 20%,rgba(0,212,255,.23),transparent 18rem),linear-gradient(135deg,#edf5ff,#fff 55%,#eaf7ff); }
    .access-cover.has-image { color:#fff; background-position:center; background-size:cover; }
    .access-cover.has-image::before { content:""; position:absolute; inset:0; background:linear-gradient(90deg,rgba(7,22,77,.92),rgba(7,22,77,.56) 58%,rgba(7,22,77,.18)); }
    .access-copy,.access-summary { position:relative; z-index:1; }
    .access-copy small { color:#137bb2; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .has-image .access-copy small { color:#67e8f9; }
    .access-copy h1 { margin:10px 0 12px; color:#07164d; font-size:clamp(32px,5vw,62px); line-height:1.04; }
    .has-image .access-copy h1,.has-image .access-copy p { color:#fff; }
    .access-copy p { max-width:680px; margin:0; color:#4b587c; font-size:16px; line-height:1.7; }
    .access-chips { display:flex; flex-wrap:wrap; gap:8px; margin-top:19px; }
    .access-chip { padding:8px 11px; border:1px solid rgba(49,87,220,.16); border-radius:999px; color:#3157dc; background:rgba(255,255,255,.86); font-size:11px; font-weight:900; }
    .access-summary { padding:22px; border:1px solid rgba(49,87,220,.15); border-radius:18px; background:rgba(255,255,255,.88); backdrop-filter:blur(14px); box-shadow:0 14px 34px rgba(16,85,245,.1); }
    .access-summary strong { display:block; color:#07164d; font-size:18px; }
    .access-summary p { margin:8px 0 0; color:#4b587c; font-size:13px; line-height:1.6; }
    .access-progress { height:11px; margin:15px 0 8px; overflow:hidden; border-radius:999px; background:#dce6f2; }
    .access-progress span { height:100%; display:block; border-radius:inherit; background:linear-gradient(90deg,#087ee1,#11b7ed); }
    .access-progress-copy { display:flex; justify-content:space-between; color:#526078; font-size:11px; font-weight:900; }
    .access-actions { display:flex; flex-wrap:wrap; gap:10px; margin-top:18px; }
    .access-button { min-height:45px; display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:11px 18px; border-radius:12px; color:#fff; background:linear-gradient(135deg,#087ee1,#3157dc); font-size:13px; font-weight:900; }
    .access-button.secondary { color:#3157dc; border:1px solid rgba(49,87,220,.18); background:#fff; }
    .curriculum { margin-top:30px; }
    .curriculum-head { display:flex; justify-content:space-between; align-items:end; gap:20px; margin-bottom:16px; }
    .curriculum-head h2 { margin:5px 0 0; color:#07164d; font-size:28px; }
    .curriculum-head p { margin:0; color:#64748b; }
    .module-card { margin-bottom:18px; overflow:hidden; border:1px solid rgba(47,123,255,.15); border-radius:18px; background:#fff; box-shadow:0 12px 30px rgba(16,85,245,.07); }
    .module-head { display:flex; justify-content:space-between; gap:20px; padding:21px 22px; background:linear-gradient(90deg,#f4f8ff,#fff); }
    .module-head small { color:#3157dc; font-size:11px; font-weight:900; letter-spacing:.07em; text-transform:uppercase; }
    .module-head h3 { margin:6px 0; color:#07164d; font-size:20px; }
    .module-head p { margin:0; color:#64748b; font-size:13px; }
    .module-count { flex:0 0 auto; color:#087ee1; font-size:20px; font-weight:900; }
    .lesson-access { display:grid; grid-template-columns:28px minmax(0,1fr) auto; gap:12px; align-items:start; padding:17px 22px; border-top:1px solid #e8edf4; }
    .lesson-access:hover { background:#f8fbff; }
    .lesson-status { width:22px; height:22px; display:grid; place-items:center; margin-top:1px; border:2px solid #9ccfee; border-radius:50%; color:#fff; font-size:12px; }
    .lesson-status.done { border-color:#0798ec; background:#0798ec; }
    .lesson-info strong { display:block; color:#14213d; font-size:14px; }
    .lesson-info > span { display:block; margin-top:4px; color:#718096; font-size:12px; }
    .material-list { display:flex; flex-wrap:wrap; gap:7px; margin-top:10px; }
    .material-badge { padding:7px 10px; border:1px solid #cddfff; border-radius:999px; color:#536b9a; background:#f1f6ff; font-size:10px; font-weight:900; }
    .open-lesson { align-self:center; padding:9px 13px; border-radius:9px; color:#fff; background:#0798ec; font-size:11px; font-weight:900; }
    .empty-course { padding:30px; text-align:center; color:#64748b; }
    @media(max-width:760px) {
        .access-cover { grid-template-columns:1fr; padding:28px 20px; }
        .curriculum-head,.module-head { align-items:flex-start; flex-direction:column; }
        .lesson-access { grid-template-columns:24px minmax(0,1fr); padding:15px; }
        .open-lesson { grid-column:2; justify-self:start; }
    }
</style>

<main class="course-access">
    <section class="access-hero">
        <div class="access-cover {{ $coverImage ? 'has-image' : '' }}" @if($coverImage) style="background-image:url('{{ $coverImage }}')" @endif>
            <div class="access-copy">
                <small>{{ $course->level }} · {{ $course->modules->count() }} Modul · {{ $totalDuration }} Menit</small>
                <h1>{{ $course->title }}</h1>
                <p>{{ $course->description ?: $course->summary }}</p>
                <div class="access-chips">
                    <span class="access-chip">Mentor: {{ optional($course->mentor)->name ?? 'Tim Trama Verse' }}</span>
                    <span class="access-chip">{{ $lessons->count() }} Lesson</span>
                    <span class="access-chip">Akses Kelas Aktif</span>
                </div>
            </div>

            <aside class="access-summary">
                <strong>Progress Belajar</strong>
                <p>Selesaikan lesson secara berurutan. Progres akan tersimpan otomatis saat tombol selesai ditekan.</p>
                <div class="access-progress"><span style="width:{{ $progressPercent }}%"></span></div>
                <div class="access-progress-copy">
                    <span>{{ $progressPercent }}% Complete</span>
                    <span>{{ $completedLessons }}/{{ $lessons->count() }} Lesson</span>
                </div>
                <div class="access-actions">
                    @if($continueLesson)
                        <a class="access-button" href="{{ route('lms.lessons.show', [$course, $continueLesson]) }}">
                            {{ $progressPercent > 0 ? 'Lanjutkan Belajar' : 'Mulai Kelas' }} →
                        </a>
                    @endif
                    <a class="access-button secondary" href="{{ route('participant.dashboard') }}">Back to Dashboard</a>
                </div>
            </aside>
        </div>
    </section>

    <section class="curriculum">
        <div class="curriculum-head">
            <div>
                <span class="eyebrow">Data Belajar</span>
                <h2>Modul dan Lesson Kelas</h2>
            </div>
            <p>Pilih lesson untuk masuk ke ruang belajar.</p>
        </div>

        @forelse($course->modules as $module)
            <article class="module-card">
                <header class="module-head">
                    <div>
                        <small>{{ $module->category }} · Modul {{ $module->sort_order }} · {{ $module->duration_minutes ?? 0 }} Menit</small>
                        <h3>{{ $module->title }}</h3>
                        <p>{{ $module->summary }}</p>
                    </div>
                    <span class="module-count">
                        {{ $module->lessons->filter(fn ($item) => optional($progress->get($item->id))->progress_percent === 100)->count() }}/{{ $module->lessons->count() }}
                    </span>
                </header>

                @forelse($module->lessons as $lesson)
                    @php $isDone = optional($progress->get($lesson->id))->progress_percent === 100; @endphp
                    <a class="lesson-access" href="{{ route('lms.lessons.show', [$course, $lesson]) }}">
                        <span class="lesson-status {{ $isDone ? 'done' : '' }}">{{ $isDone ? '✓' : '' }}</span>
                        <span class="lesson-info">
                            <strong>{{ $lesson->title }}</strong>
                            <span>{{ ucfirst($lesson->content_type) }} · {{ $lesson->duration_minutes }} menit</span>
                            @if($lesson->materials->isNotEmpty())
                                <span class="material-list">
                                    @foreach($lesson->materials as $material)
                                        <span class="material-badge">{{ strtoupper(str_replace('-', ' ', $material->type)) }}: {{ $material->title }}</span>
                                    @endforeach
                                </span>
                            @endif
                        </span>
                        <span class="open-lesson">{{ $isDone ? 'Buka Ulang' : 'Buka Lesson' }}</span>
                    </a>
                @empty
                    <div class="empty-course">Lesson pada modul ini sedang disiapkan.</div>
                @endforelse
            </article>
        @empty
            <div class="module-card empty-course">Modul kelas sedang disiapkan oleh admin.</div>
        @endforelse
    </section>
</main>
@endsection
