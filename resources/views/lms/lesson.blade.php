@extends('layouts.lms', ['title' => $lesson->title])

@section('content')
<style>
    .topbar, .page-footer { display:none; }
    .classroom { min-height:100vh; display:grid; grid-template-columns:330px minmax(0,1fr); color:#172033; background:#f5f7fb; }
    .class-sidebar { position:sticky; top:0; height:100vh; overflow-y:auto; background:#fff; border-right:1px solid #dbe3ef; }
    .class-brand { padding:22px 20px; color:#fff; background:linear-gradient(135deg,#087ee1,#09a5ee); }
    .class-brand a { display:inline-flex; align-items:center; gap:8px; margin-bottom:16px; font-size:12px; font-weight:800; opacity:.9; }
    .class-brand h2 { margin:0; font-size:18px; line-height:1.35; }
    .class-brand p { margin:7px 0 0; color:#dff4ff; font-size:12px; }
    .module-block { border-bottom:1px solid #e5e9f0; background:#fff; }
    .module-block summary { position:relative; display:block; padding:20px 46px 18px 22px; cursor:pointer; list-style:none; }
    .module-block summary::-webkit-details-marker { display:none; }
    .module-block summary::after { content:"›"; position:absolute; top:50%; right:22px; color:#087ee1; font-size:24px; font-weight:900; transform:translateY(-50%) rotate(90deg); transition:transform .2s ease; }
    .module-block[open] summary { background:linear-gradient(90deg,#f5f9ff,#fff); }
    .module-block[open] summary::after { transform:translateY(-50%) rotate(-90deg); }
    .module-title { display:block; color:#087ee1; font-size:15px; font-weight:900; line-height:1.4; }
    .module-meta { display:block; margin-top:9px; color:#7b8aa4; font-size:11px; font-weight:800; }
    .module-lessons { overflow:hidden; border-top:1px solid #eef1f5; }
    .lesson-link { display:grid; grid-template-columns:22px 1fr auto; gap:9px; align-items:center; min-height:66px; padding:12px 22px; border-top:1px solid #eef1f5; color:#46526a; font-size:13px; }
    .module-lessons .lesson-link:first-child { border-top:0; }
    .lesson-link:hover { color:#087ee1; background:#f4faff; }
    .lesson-link.active { color:#087ee1; background:#e9f6ff; font-weight:900; box-shadow:inset 4px 0 #0798ec; }
    .lesson-state { width:18px; height:18px; display:grid; place-items:center; border:2px solid #91cdef; border-radius:50%; color:#fff; font-size:11px; }
    .lesson-state.done { border-color:#0798ec; background:#0798ec; }
    .lesson-duration { color:#8b96aa; font-size:10px; }
    .class-main { min-width:0; padding:30px clamp(20px,4vw,54px) 36px; }
    .class-head { display:flex; justify-content:space-between; gap:24px; align-items:flex-start; padding-bottom:25px; border-bottom:1px solid #dbe3ef; }
    .class-head small { color:#0798ec; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .class-head h1 { margin:8px 0 5px; color:#111827; font-size:clamp(27px,4vw,46px); line-height:1.08; }
    .class-head p { margin:0; color:#667085; }
    .progress-panel { width:min(380px,100%); padding:17px 19px; border:1px solid #dbe3ef; border-radius:14px; background:#fff; box-shadow:0 12px 30px rgba(31,64,104,.08); }
    .progress-copy { display:flex; justify-content:space-between; gap:12px; color:#536077; font-size:13px; }
    .progress-copy strong { color:#0798ec; font-size:20px; }
    .progress-track { height:12px; margin-top:10px; overflow:hidden; border-radius:999px; background:#dfe7f1; }
    .progress-fill { height:100%; border-radius:inherit; background:linear-gradient(90deg,#087ee1,#11b7ed); }
    .current-section { padding-top:28px; }
    .current-section h2 { margin:0 0 18px; color:#222c3d; font-size:24px; }
    .learning-stage { overflow:hidden; border:1px solid #dce3ed; border-radius:14px; background:#fff; box-shadow:0 18px 42px rgba(31,64,104,.1); }
    .learning-stage video, .learning-stage iframe { width:100%; aspect-ratio:16/9; display:block; border:0; border-radius:0; background:#06122e; }
    .pdf-stage { min-height:520px; }
    .lesson-copy { padding:22px; }
    .lesson-copy h3 { margin:0 0 8px; color:#111827; font-size:20px; }
    .lesson-copy p { margin:0; color:#667085; line-height:1.7; }
    .resource-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; margin-top:15px; }
    .resource-link { display:flex; justify-content:space-between; gap:14px; padding:14px; border:1px solid #dce3ed; border-radius:11px; color:#3157dc; background:#f8fbff; font-size:13px; font-weight:800; }
    .class-navigation { display:grid; grid-template-columns:1fr auto 1fr; gap:18px; align-items:center; margin-top:22px; padding-top:22px; border-top:2px solid #e1e7ef; }
    .class-nav-button, .complete-button { min-height:46px; display:inline-flex; align-items:center; justify-content:center; gap:10px; padding:11px 24px; border:0; border-radius:999px; color:#fff; background:#0798ec; cursor:pointer; font:inherit; font-size:13px; font-weight:900; }
    .class-nav-button.next, .complete-form { justify-self:end; }
    .class-back { color:#0798ec; font-size:12px; font-weight:800; text-decoration:underline; }
    .status-message { margin-bottom:16px; padding:12px 15px; border-radius:10px; color:#075985; background:#e0f2fe; font-weight:800; }
    @media(max-width:900px) {
        .classroom { grid-template-columns:1fr; }
        .class-sidebar { position:relative; height:auto; max-height:420px; border-right:0; border-bottom:1px solid #dbe3ef; }
        .class-head { flex-direction:column; }
        .progress-panel { width:100%; }
    }
    @media(max-width:600px) {
        .class-main { padding:22px 14px 28px; }
        .resource-grid { grid-template-columns:1fr; }
        .class-navigation { grid-template-columns:1fr 1fr; }
        .class-back { grid-column:1/-1; grid-row:2; justify-self:center; }
        .class-nav-button, .complete-button { width:100%; padding-inline:15px; }
    }
</style>

<div class="classroom">
    <aside class="class-sidebar">
        <div class="class-brand">
            <a href="{{ route('lms.courses.show', $course) }}">← Akses Kelas</a>
            <h2>{{ $course->title }}</h2>
            <p>{{ $course->level }} · {{ $lessons->count() }} pelajaran</p>
        </div>

        @foreach($course->modules as $module)
            @php $isActiveModule = (int) $lesson->course_module_id === (int) $module->id; @endphp
            <details class="module-block" data-module-accordion @if($isActiveModule) open @endif>
                <summary>
                <div class="module-title">{{ $module->title }}</div>
                <div class="module-meta">{{ $module->lessons->count() }} topik · {{ $module->duration_minutes ?? 0 }} menit</div>
                </summary>
                <div class="module-lessons">
                @foreach($module->lessons as $sidebarLesson)
                    @php $isDone = optional($progress->get($sidebarLesson->id))->progress_percent === 100; @endphp
                    <a class="lesson-link {{ $sidebarLesson->id === $lesson->id ? 'active' : '' }}" href="{{ route('lms.lessons.show', [$course, $sidebarLesson]) }}">
                        <span class="lesson-state {{ $isDone ? 'done' : '' }}">{{ $isDone ? '✓' : '' }}</span>
                        <span>{{ $sidebarLesson->title }}</span>
                        <span class="lesson-duration">{{ $sidebarLesson->duration_minutes }}m</span>
                    </a>
                @endforeach
                </div>
            </details>
        @endforeach
    </aside>

    <main class="class-main">
        @if(session('lesson_status')) <div class="status-message">{{ session('lesson_status') }}</div> @endif

        <header class="class-head">
            <div>
                <small>{{ $lesson->module->title }} · Pelajaran {{ $currentIndex + 1 }}</small>
                <h1>{{ $lesson->title }}</h1>
                <p>{{ $lesson->summary }}</p>
            </div>
            <div class="progress-panel">
                <div class="progress-copy">
                    <span><strong>{{ $progressPercent }}%</strong> COMPLETE</span>
                    <span>{{ $completedLessons }}/{{ $lessons->count() }} Steps</span>
                </div>
                <div class="progress-track"><div class="progress-fill" style="width:{{ $progressPercent }}%"></div></div>
            </div>
        </header>

        <section class="current-section">
            <h2>Sedang Dipelajari</h2>
            <div class="learning-stage">
                @if($primaryMaterial && $primaryMaterial->type === 'video-upload')
                    <video controls controlsList="nodownload">
                        <source src="{{ route('materials.show', $primaryMaterial) }}">
                    </video>
                @elseif($primaryMaterial && $embedUrl)
                    <iframe src="{{ $embedUrl }}" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                @elseif($primaryMaterial && in_array($primaryMaterial->type, ['pdf', 'pdf-slide'], true))
                    <iframe class="pdf-stage" src="{{ route('materials.show', $primaryMaterial) }}"></iframe>
                @else
                    <div class="lesson-copy">
                        <h3>{{ $lesson->title }}</h3>
                        <p>Materi utama belum ditambahkan. Admin dapat memasukkan URL embed ITBOX/YouTube atau mengunggah video dan PDF dari menu Kelola Materi.</p>
                    </div>
                @endif

                <div class="lesson-copy">
                    <h3>{{ optional($primaryMaterial)->title ?? $lesson->title }}</h3>
                    <p>{{ $lesson->summary ?: 'Pelajari materi ini sampai selesai, kemudian tandai pelajaran sebagai selesai untuk melanjutkan progres.' }}</p>
                    @php
                        $resources = $lesson->materials
                            ->whereIn('type', ['tool', 'resource'])
                            ->reject(fn ($material) => optional($primaryMaterial)->id === $material->id)
                            ->values();
                    @endphp
                    @if($resources->isNotEmpty())
                        <div class="resource-grid">
                            @foreach($resources as $resource)
                                <a class="resource-link" href="{{ route('materials.show', $resource) }}" target="_blank" rel="noopener">
                                    <span>{{ $resource->title }}</span><span>↗</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <nav class="class-navigation" aria-label="Navigasi pelajaran">
            @if($previousLesson)
                <a class="class-nav-button" href="{{ route('lms.lessons.show', [$course, $previousLesson]) }}">‹ Previous Pelajaran</a>
            @else
                <span></span>
            @endif

            <a class="class-back" href="{{ route('lms.courses.show', $course) }}">Back to Akses Kelas</a>

            @if($enrollment)
                <form class="complete-form" method="POST" action="{{ route('lms.lessons.complete', [$course, $lesson]) }}">
                    @csrf
                    <button class="complete-button" type="submit">
                        {{ $nextLesson ? 'Selesai & Next Topic' : 'Selesaikan Course' }} ›
                    </button>
                </form>
            @elseif($nextLesson)
                <a class="class-nav-button next" href="{{ route('lms.lessons.show', [$course, $nextLesson]) }}">Next Topic ›</a>
            @endif
        </nav>
    </main>
</div>
<script>
    (function () {
        const modules = Array.from(document.querySelectorAll('[data-module-accordion]'));
        const activeLesson = document.querySelector('.lesson-link.active');

        modules.forEach((module) => {
            module.addEventListener('toggle', () => {
                if (!module.open) return;

                modules.forEach((otherModule) => {
                    if (otherModule !== module) {
                        otherModule.open = false;
                    }
                });
            });
        });

        activeLesson?.scrollIntoView({ block: 'center' });
    }());
</script>
@endsection
