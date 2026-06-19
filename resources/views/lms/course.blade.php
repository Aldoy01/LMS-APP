@extends('layouts.lms', ['title' => 'Akses Kelas - ' . $course->title])

@section('content')
@php
    $coverImage = $course->cover_image
        ? (Str::startsWith($course->cover_image, ['http://', 'https://']) ? $course->cover_image : asset($course->cover_image))
        : '';
    $totalDuration = $course->modules->sum('duration_minutes');
@endphp

<style>
    .page-footer { display:none; }
    body { background:radial-gradient(circle at 10% 8%,rgba(49,87,220,.12),transparent 30rem),radial-gradient(circle at 90% 18%,rgba(0,212,255,.13),transparent 27rem),#f4f7fc; }
    .course-access { width:min(1220px,calc(100% - 32px)); margin:0 auto; padding:36px 0 60px; }
    .access-hero { position:relative; overflow:hidden; border:1px solid rgba(96,165,250,.25); border-radius:28px; background:#07164d; box-shadow:0 28px 70px rgba(7,22,77,.2); }
    .access-hero::after { content:""; position:absolute; width:310px; height:310px; right:-120px; bottom:-190px; border:42px solid rgba(103,232,249,.09); border-radius:50%; pointer-events:none; }
    .access-cover { position:relative; min-height:390px; display:grid; grid-template-columns:minmax(0,1.25fr) minmax(310px,.75fr); align-items:center; gap:38px; padding:clamp(32px,5vw,68px); background:radial-gradient(circle at 78% 16%,rgba(0,212,255,.2),transparent 18rem),radial-gradient(circle at 8% 100%,rgba(125,22,184,.22),transparent 22rem),linear-gradient(135deg,#07164d 0%,#123b8f 58%,#1d4ed8 100%); }
    .access-cover.has-image { color:#fff; background-position:center; background-size:cover; }
    .access-cover.has-image::before { content:""; position:absolute; inset:0; background:linear-gradient(90deg,rgba(7,22,77,.92),rgba(7,22,77,.56) 58%,rgba(7,22,77,.18)); }
    .access-copy,.access-summary { position:relative; z-index:1; }
    .access-copy small { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border:1px solid rgba(103,232,249,.22); border-radius:999px; color:#67e8f9; background:rgba(255,255,255,.07); font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .access-copy small::before { content:""; width:8px; height:8px; border-radius:50%; background:#34d399; box-shadow:0 0 0 5px rgba(52,211,153,.14); }
    .has-image .access-copy small { color:#67e8f9; }
    .access-copy h1 { max-width:760px; margin:20px 0 14px; color:#fff; font-size:clamp(38px,5.5vw,68px); line-height:1; letter-spacing:-.035em; }
    .has-image .access-copy h1,.has-image .access-copy p { color:#fff; }
    .access-copy p { max-width:680px; margin:0; color:#dbeafe; font-size:16px; line-height:1.75; }
    .access-chips { display:flex; flex-wrap:wrap; gap:8px; margin-top:19px; }
    .access-chip { padding:8px 11px; border:1px solid rgba(255,255,255,.18); border-radius:999px; color:#fff; background:rgba(255,255,255,.1); backdrop-filter:blur(10px); font-size:11px; font-weight:900; }
    .access-summary { padding:25px; border:1px solid rgba(255,255,255,.2); border-radius:22px; background:rgba(255,255,255,.92); backdrop-filter:blur(18px); box-shadow:0 22px 50px rgba(7,22,77,.24); }
    .access-summary strong { display:block; color:#07164d; font-size:18px; }
    .access-summary p { margin:8px 0 0; color:#4b587c; font-size:13px; line-height:1.6; }
    .access-progress { height:13px; margin:18px 0 9px; overflow:hidden; border-radius:999px; background:#dce6f2; box-shadow:inset 0 1px 3px rgba(7,22,77,.12); }
    .access-progress span { height:100%; display:block; border-radius:inherit; background:linear-gradient(90deg,#3157dc,#00d4ff,#34d399); box-shadow:0 0 20px rgba(0,212,255,.35); }
    .access-progress-copy { display:flex; justify-content:space-between; color:#526078; font-size:11px; font-weight:900; }
    .access-actions { display:flex; flex-wrap:wrap; gap:10px; margin-top:18px; }
    .access-button { min-height:48px; display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 19px; border-radius:13px; color:#fff; background:linear-gradient(135deg,#087ee1,#3157dc); box-shadow:0 12px 26px rgba(49,87,220,.22); font-size:13px; font-weight:900; transition:transform .18s ease,box-shadow .18s ease; }
    .access-button:hover { color:#fff; transform:translateY(-2px); box-shadow:0 16px 32px rgba(49,87,220,.3); }
    .access-button.secondary { color:#3157dc; border:1px solid rgba(49,87,220,.18); background:#fff; }
    .curriculum { margin-top:38px; padding:28px; border:1px solid rgba(47,123,255,.12); border-radius:24px; background:rgba(255,255,255,.78); box-shadow:0 20px 50px rgba(16,85,245,.08); backdrop-filter:blur(14px); }
    .curriculum-head { display:flex; justify-content:space-between; align-items:end; gap:20px; margin-bottom:16px; }
    .curriculum-head h2 { margin:5px 0 0; color:#07164d; font-size:28px; }
    .curriculum-head p { margin:0; color:#64748b; }
    .module-card { margin-bottom:16px; overflow:hidden; border:1px solid rgba(47,123,255,.14); border-radius:18px; background:#fff; box-shadow:0 12px 30px rgba(16,85,245,.07); transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease; }
    .module-card:hover { transform:translateY(-2px); border-color:rgba(0,212,255,.34); box-shadow:0 18px 38px rgba(16,85,245,.12); }
    .module-card[open] { border-color:rgba(49,87,220,.35); box-shadow:0 18px 42px rgba(16,85,245,.13); }
    .module-card summary { list-style:none; cursor:pointer; }
    .module-card summary::-webkit-details-marker { display:none; }
    .module-head { position:relative; display:flex; justify-content:space-between; gap:20px; padding:22px 66px 22px 24px; background:linear-gradient(90deg,#f3f7ff,#fff); }
    .module-head::after { content:"›"; position:absolute; top:50%; right:24px; color:#087ee1; font-size:28px; font-weight:900; transform:translateY(-50%) rotate(90deg); transition:transform .2s ease; }
    .module-card[open] .module-head::after { transform:translateY(-50%) rotate(-90deg); }
    .module-head small { color:#3157dc; font-size:11px; font-weight:900; letter-spacing:.07em; text-transform:uppercase; }
    .module-head h3 { margin:6px 0; color:#07164d; font-size:20px; }
    .module-head p { margin:0; color:#64748b; font-size:13px; }
    .module-count { flex:0 0 auto; align-self:center; padding:8px 12px; border-radius:999px; color:#087ee1; background:#eaf6ff; font-size:14px; font-weight:900; }
    .lesson-access { display:grid; grid-template-columns:34px minmax(0,1fr) auto; gap:14px; align-items:center; padding:18px 24px; border-top:1px solid #e8edf4; transition:background .18s ease,padding-left .18s ease; }
    .lesson-access:hover { padding-left:29px; background:linear-gradient(90deg,#edf7ff,#fff); }
    .lesson-status { width:28px; height:28px; display:grid; place-items:center; border:2px solid #9ccfee; border-radius:9px; color:#fff; background:#fff; font-size:12px; }
    .lesson-status.done { border-color:#0798ec; background:linear-gradient(145deg,#0798ec,#3157dc); box-shadow:0 7px 16px rgba(49,87,220,.22); }
    .lesson-info strong { display:block; color:#14213d; font-size:14px; }
    .lesson-info > span { display:block; margin-top:4px; color:#718096; font-size:12px; }
    .material-list { display:flex; flex-wrap:wrap; gap:7px; margin-top:10px; }
    .material-badge { padding:7px 10px; border:1px solid #cddfff; border-radius:999px; color:#536b9a; background:#f1f6ff; font-size:10px; font-weight:900; }
    .open-lesson { align-self:center; padding:9px 14px; border-radius:10px; color:#fff; background:linear-gradient(135deg,#0798ec,#3157dc); box-shadow:0 8px 18px rgba(49,87,220,.18); font-size:11px; font-weight:900; }
    .empty-course { padding:30px; text-align:center; color:#64748b; }
    .learning-forum { margin-top:38px; padding:28px; border:1px solid rgba(47,123,255,.12); border-radius:24px; background:rgba(255,255,255,.8); box-shadow:0 20px 50px rgba(16,85,245,.08); }
    .learning-forum h2 { margin:6px 0 8px; color:#07164d; font-size:28px; }
    .learning-forum > p { margin:0 0 18px; color:#64748b; }
    @media(max-width:760px) {
        .access-cover { grid-template-columns:1fr; padding:28px 20px; }
        .course-access { width:min(100% - 18px,1220px); padding-top:18px; }
        .curriculum,.learning-forum { padding:20px 14px; border-radius:18px; }
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
            <details class="module-card" data-access-module>
                <summary class="module-head">
                    <div>
                        <small>{{ $module->category }} · Modul {{ $module->sort_order }} · {{ $module->duration_minutes ?? 0 }} Menit</small>
                        <h3>{{ $module->title }}</h3>
                        <p>{{ $module->summary }}</p>
                    </div>
                    <span class="module-count">
                        {{ $module->lessons->filter(fn ($item) => optional($progress->get($item->id))->progress_percent === 100)->count() }}/{{ $module->lessons->count() }}
                    </span>
                </summary>

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
            </details>
        @empty
            <div class="module-card empty-course">Modul kelas sedang disiapkan oleh admin.</div>
        @endforelse
    </section>

    <section class="learning-forum">
        <span class="eyebrow">Community Learning</span>
        <h2>Forum Belajar Peserta</h2>
        <p>Diskusikan materi kelas, tanyakan kendala, dan lanjutkan praktik bersama komunitas.</p>
        @include('partials.learning-forum')
    </section>

    @include('partials.footer', ['footerMode' => 'participant'])
</main>
<script>
    (function () {
        const modules = Array.from(document.querySelectorAll('[data-access-module]'));

        modules.forEach((module) => {
            module.addEventListener('toggle', () => {
                if (!module.open) return;
                modules.forEach((otherModule) => {
                    if (otherModule !== module) otherModule.open = false;
                });
            });
        });
    }());
</script>
@endsection
