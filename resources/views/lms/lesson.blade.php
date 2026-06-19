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
    .material-viewer + .material-viewer { border-top:8px solid #eef2f7; }
    .material-viewer-title { padding:14px 18px; color:#14213d; background:#f8fbff; border-bottom:1px solid #e1e7ef; font-size:13px; font-weight:900; }
    .learning-stage video, .learning-stage iframe { width:100%; aspect-ratio:16/9; display:block; border:0; border-radius:0; background:#06122e; }
    .learning-stage .pdf-stage { min-height:680px; aspect-ratio:auto; background:#eef2f7; }
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
    .lesson-forum { margin-top:34px; padding-top:28px; border-top:1px solid #dbe3ef; }
    .lesson-forum h2 { margin:6px 0 8px; color:#07164d; font-size:24px; }
    .lesson-forum p { margin:0 0 16px; color:#64748b; }
    .lesson-footer { margin-top:30px; }

    /* Modern digital classroom */
    body { background:#07164d; }
    .classroom { grid-template-columns:350px minmax(0,1fr); background:radial-gradient(circle at 84% 5%,rgba(0,212,255,.12),transparent 26rem),#f3f6fb; }
    .class-sidebar { z-index:20; color:#dbeafe; background:linear-gradient(180deg,#07164d 0%,#0b2865 48%,#102f78 100%); border-right-color:rgba(103,232,249,.16); box-shadow:16px 0 45px rgba(7,22,77,.14); scrollbar-width:thin; scrollbar-color:#3157dc transparent; }
    .class-brand { position:relative; padding:26px 24px 24px; overflow:hidden; background:radial-gradient(circle at 92% 8%,rgba(0,212,255,.25),transparent 11rem),linear-gradient(145deg,rgba(49,87,220,.28),rgba(7,22,77,.2)); border-bottom:1px solid rgba(255,255,255,.1); }
    .class-brand::after { content:""; position:absolute; width:120px; height:120px; right:-64px; bottom:-72px; border:18px solid rgba(103,232,249,.1); border-radius:50%; }
    .class-brand a { position:relative; z-index:1; min-height:42px; padding:7px 14px 7px 8px; border:1px solid rgba(255,255,255,.16); border-radius:14px; color:#e8f8ff; background:linear-gradient(135deg,rgba(255,255,255,.13),rgba(255,255,255,.06)); box-shadow:inset 0 1px 0 rgba(255,255,255,.12),0 10px 24px rgba(0,0,0,.12); backdrop-filter:blur(12px); font-size:11px; font-weight:900; transition:transform .2s ease,background .2s ease,box-shadow .2s ease; }
    .class-brand a:hover { color:#fff; background:rgba(255,255,255,.17); box-shadow:inset 0 1px 0 rgba(255,255,255,.16),0 14px 28px rgba(0,0,0,.18); transform:translateX(-3px); }
    .nav-icon { width:28px; height:28px; display:grid; place-items:center; flex:0 0 auto; border-radius:10px; background:rgba(103,232,249,.16); }
    .nav-icon svg { width:15px; height:15px; }
    .class-brand h2,.class-brand p { position:relative; z-index:1; }
    .class-brand h2 { font-size:20px; }
    .class-brand p { color:#9edffa; font-weight:800; }
    .sidebar-progress { position:relative; z-index:1; margin-top:18px; padding:13px; border:1px solid rgba(255,255,255,.12); border-radius:13px; background:rgba(255,255,255,.07); }
    .sidebar-progress-copy { display:flex; justify-content:space-between; gap:12px; color:#dbeafe; font-size:10px; font-weight:900; }
    .sidebar-progress-track { height:7px; margin-top:9px; overflow:hidden; border-radius:999px; background:rgba(255,255,255,.13); }
    .sidebar-progress-track span { height:100%; display:block; border-radius:inherit; background:linear-gradient(90deg,#67e8f9,#34d399); box-shadow:0 0 14px rgba(103,232,249,.4); }
    .module-block { border-bottom-color:rgba(255,255,255,.08); background:transparent; }
    .module-block summary { padding:20px 48px 18px 24px; transition:background .18s ease; }
    .module-block summary::after { color:#67e8f9; }
    .module-block[open] summary { background:rgba(255,255,255,.08); }
    .module-title { color:#fff; }
    .module-meta { color:#93c5fd; }
    .module-lessons { border-top-color:rgba(255,255,255,.08); background:rgba(2,10,36,.18); }
    .lesson-link { grid-template-columns:25px 1fr auto; gap:11px; min-height:68px; padding:13px 22px; border-top-color:rgba(255,255,255,.07); color:#c7d8f8; transition:background .18s ease,color .18s ease,padding-left .18s ease; }
    .lesson-link:hover { padding-left:27px; color:#fff; background:rgba(255,255,255,.08); }
    .lesson-link.active { color:#fff; background:linear-gradient(90deg,rgba(0,212,255,.2),rgba(49,87,220,.16)); box-shadow:inset 4px 0 #67e8f9; }
    .lesson-state { width:22px; height:22px; border-color:#5da9d7; border-radius:8px; background:rgba(255,255,255,.05); }
    .lesson-state.done { border-color:#34d399; background:linear-gradient(145deg,#06b6d4,#34d399); box-shadow:0 6px 16px rgba(52,211,153,.22); }
    .lesson-duration { color:#93c5fd; }
    .class-main { padding:32px clamp(22px,4vw,58px) 42px; }
    .class-head { position:relative; padding:28px; overflow:hidden; border:1px solid rgba(47,123,255,.14); border-radius:24px; background:rgba(255,255,255,.9); box-shadow:0 18px 46px rgba(16,85,245,.09); backdrop-filter:blur(14px); }
    .class-head::after { content:""; position:absolute; width:180px; height:180px; top:-110px; right:-80px; border:28px solid rgba(0,212,255,.08); border-radius:50%; }
    .class-head > * { position:relative; z-index:1; }
    .class-head small { display:inline-flex; padding:7px 10px; border-radius:999px; color:#3157dc; background:#eaf2ff; }
    .class-head h1 { margin-top:14px; color:#07164d; font-size:clamp(30px,4vw,50px); letter-spacing:-.025em; }
    .class-head p { max-width:720px; line-height:1.7; }
    .progress-panel { padding:20px; border-color:rgba(49,87,220,.16); border-radius:18px; background:linear-gradient(145deg,#fff,#f4f8ff); box-shadow:0 14px 32px rgba(31,64,104,.1); }
    .progress-copy strong { color:#3157dc; font-size:22px; }
    .progress-track { box-shadow:inset 0 1px 3px rgba(7,22,77,.1); }
    .progress-fill { background:linear-gradient(90deg,#3157dc,#00d4ff,#34d399); box-shadow:0 0 18px rgba(0,212,255,.3); }
    .current-section { padding-top:34px; }
    .current-section h2 { color:#07164d; font-size:26px; }
    .learning-stage { border-color:rgba(47,123,255,.15); border-radius:22px; box-shadow:0 24px 60px rgba(31,64,104,.13); }
    .material-viewer-title { display:flex; align-items:center; gap:9px; padding:15px 20px; background:linear-gradient(90deg,#eff6ff,#fff); }
    .material-viewer-title::before { content:""; width:9px; height:9px; border-radius:3px; background:linear-gradient(145deg,#3157dc,#00d4ff); box-shadow:0 0 0 5px rgba(49,87,220,.08); }
    .lesson-copy { padding:26px; }
    .resource-link { padding:15px; border-color:#cddfff; border-radius:13px; background:linear-gradient(145deg,#f8fbff,#eef5ff); transition:transform .18s ease,box-shadow .18s ease; }
    .resource-link:hover { transform:translateY(-2px); box-shadow:0 10px 24px rgba(49,87,220,.1); }
    .class-navigation { margin-top:26px; padding:14px; border:1px solid rgba(47,123,255,.12); border-radius:22px; background:rgba(255,255,255,.74); box-shadow:0 18px 45px rgba(16,85,245,.09); backdrop-filter:blur(16px); }
    .class-nav-button,.complete-button { position:relative; min-height:58px; display:inline-flex; align-items:center; gap:12px; padding:8px 18px 8px 9px; overflow:hidden; border:1px solid rgba(255,255,255,.18); border-radius:18px; background:linear-gradient(135deg,#087ee1,#3157dc 72%,#5634bb); box-shadow:0 12px 28px rgba(49,87,220,.24),inset 0 1px 0 rgba(255,255,255,.18); transition:transform .2s ease,box-shadow .2s ease,filter .2s ease; }
    .class-nav-button.next,.complete-button { padding:8px 9px 8px 18px; }
    .class-nav-button::before,.complete-button::before { content:""; position:absolute; inset:0; background:linear-gradient(110deg,transparent 20%,rgba(255,255,255,.16) 45%,transparent 70%); transform:translateX(-120%); transition:transform .5s ease; }
    .class-nav-button:hover,.complete-button:hover { color:#fff; filter:saturate(1.1); transform:translateY(-3px); box-shadow:0 18px 34px rgba(49,87,220,.32),inset 0 1px 0 rgba(255,255,255,.2); }
    .class-nav-button:hover::before,.complete-button:hover::before { transform:translateX(120%); }
    .nav-button-icon { position:relative; z-index:1; width:40px; height:40px; display:grid; place-items:center; flex:0 0 auto; border-radius:13px; color:#fff; background:rgba(255,255,255,.15); box-shadow:inset 0 1px 0 rgba(255,255,255,.18); }
    .nav-button-icon svg { width:18px; height:18px; }
    .nav-button-copy { position:relative; z-index:1; display:grid; gap:1px; text-align:left; }
    .nav-button-copy small { color:rgba(255,255,255,.72); font-size:9px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; }
    .nav-button-copy strong { color:#fff; font-size:12px; }
    .class-back { min-height:46px; display:inline-flex; align-items:center; gap:8px; padding:9px 15px; border:1px solid rgba(49,87,220,.12); border-radius:15px; color:#3157dc; background:linear-gradient(145deg,#fff,#edf4ff); box-shadow:0 8px 20px rgba(49,87,220,.09); text-decoration:none; transition:transform .18s ease,box-shadow .18s ease; }
    .class-back:hover { color:#3157dc; transform:translateY(-2px); box-shadow:0 12px 26px rgba(49,87,220,.14); }
    .class-back svg { width:16px; height:16px; }
    .lesson-forum { padding:28px; border:1px solid rgba(47,123,255,.12); border-radius:22px; background:rgba(255,255,255,.84); box-shadow:0 18px 46px rgba(16,85,245,.08); }
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
            <a href="{{ route('lms.courses.show', $course) }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m15 18-6-6 6-6"/></svg>
                </span>
                <span>Kembali ke Akses Kelas</span>
            </a>
            <h2>{{ $course->title }}</h2>
            <div class="sidebar-progress">
                <div class="sidebar-progress-copy">
                    <span>PROGRESS KELAS</span>
                    <span>{{ $progressPercent }}%</span>
                </div>
                <div class="sidebar-progress-track"><span style="width:{{ $progressPercent }}%"></span></div>
            </div>
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
                @forelse($displayMaterials as $displayMaterial)
                    @php
                        $viewerMaterial = $displayMaterial['material'];
                        $viewerUrl = filter_var($viewerMaterial->url, FILTER_VALIDATE_URL)
                            ? $viewerMaterial->url
                            : route('materials.show', $viewerMaterial);
                    @endphp
                    <div class="material-viewer">
                        <div class="material-viewer-title">{{ $viewerMaterial->title }}</div>
                        @if($displayMaterial['kind'] === 'video-upload')
                            <video controls controlsList="nodownload">
                                <source src="{{ route('materials.show', $viewerMaterial) }}">
                            </video>
                        @elseif($displayMaterial['kind'] === 'video-embed')
                            <iframe src="{{ $displayMaterial['url'] }}" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                        @elseif($displayMaterial['kind'] === 'pdf')
                            <iframe class="pdf-stage" src="{{ $viewerUrl }}" title="{{ $viewerMaterial->title }}"></iframe>
                        @endif
                    </div>
                @empty
                    <div class="lesson-copy">
                        <h3>{{ $lesson->title }}</h3>
                        <p>Materi utama belum ditambahkan. Admin dapat memasukkan URL embed ITBOX/YouTube atau mengunggah video dan PDF dari menu Kelola Materi.</p>
                    </div>
                @endforelse

                <div class="lesson-copy">
                    <h3>{{ optional($primaryMaterial)->title ?? $lesson->title }}</h3>
                    <p>{{ $lesson->summary ?: 'Pelajari materi ini sampai selesai, kemudian tandai pelajaran sebagai selesai untuk melanjutkan progres.' }}</p>
                    @php
                        $displayMaterialIds = $displayMaterials->pluck('material.id');
                        $resources = $lesson->materials
                            ->whereIn('type', ['tool', 'resource'])
                            ->reject(fn ($material) => $displayMaterialIds->contains($material->id))
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
                <a class="class-nav-button" href="{{ route('lms.lessons.show', [$course, $previousLesson]) }}">
                    <span class="nav-button-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m15 18-6-6 6-6"/></svg>
                    </span>
                    <span class="nav-button-copy"><small>Sebelumnya</small><strong>Previous Pelajaran</strong></span>
                </a>
            @else
                <span></span>
            @endif

            <a class="class-back" href="{{ route('lms.courses.show', $course) }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/></svg>
                <span>Daftar Modul</span>
            </a>

            @if($enrollment)
                <form class="complete-form" method="POST" action="{{ route('lms.lessons.complete', [$course, $lesson]) }}">
                    @csrf
                    <button class="complete-button" type="submit">
                        <span class="nav-button-copy">
                            <small>{{ $nextLesson ? 'Tandai selesai' : 'Progress lengkap' }}</small>
                            <strong>{{ $nextLesson ? 'Next Topic' : 'Selesaikan Course' }}</strong>
                        </span>
                        <span class="nav-button-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m9 18 6-6-6-6"/></svg>
                        </span>
                    </button>
                </form>
            @elseif($nextLesson)
                <a class="class-nav-button next" href="{{ route('lms.lessons.show', [$course, $nextLesson]) }}">
                    <span class="nav-button-copy"><small>Berikutnya</small><strong>Next Topic</strong></span>
                    <span class="nav-button-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m9 18 6-6-6-6"/></svg>
                    </span>
                </a>
            @endif
        </nav>

        <section class="lesson-forum">
            <span class="eyebrow">Community Learning</span>
            <h2>Forum Belajar Peserta</h2>
            <p>Tanyakan materi, diskusikan praktik, atau hubungi admin tanpa meninggalkan alur kelas.</p>
            @include('partials.learning-forum')
        </section>

        <div class="lesson-footer">
            @include('partials.footer', ['footerMode' => 'participant'])
        </div>
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
