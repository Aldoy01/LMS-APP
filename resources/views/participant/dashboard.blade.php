@extends('layouts.lms', ['title' => 'Dashboard Peserta'])

@section('content')
    @php
        $firstEnrollment = $enrollments->first();
        $firstCourse = optional($firstEnrollment)->course;
        $totalLessons = $enrollments->sum(fn ($enrollment) => optional($enrollment->course)->modules?->sum(fn ($module) => $module->lessons->count()) ?? 0);
        $completedLessons = $enrollments->sum(fn ($enrollment) => $enrollment->progress->where('progress_percent', 100)->count());
        $overallProgress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
        $ctaUrl = $firstCourse ? route('lms.courses.show', $firstCourse) : route('lms.dashboard') . '#program';
        $ctaLabel = $firstCourse ? 'Akses Kelas Saya' : 'Pilih Program';
        $categoryCounts = $modules->groupBy('category')->map->count();
        $initials = collect(explode(' ', $user->name))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('');
        $completedModules = $modules->where('progress', 100)->count();
        $totalVideoLessons = $enrollments->sum(fn ($enrollment) => optional($enrollment->course)->modules?->sum(fn ($module) => $module->lessons->where('content_type', 'video')->count()) ?? 0);
        $watchedVideos = $enrollments->sum(fn ($enrollment) => $enrollment->progress->filter(fn ($progress) => $progress->progress_percent === 100 && optional($progress->lesson)->content_type === 'video')->count());
    @endphp

    <style>
        .member-area {
            display: grid;
            grid-template-columns: 260px minmax(0, 1fr);
            min-height: 100vh;
            background: #f4f7fc;
            transition: grid-template-columns .24s ease;
        }
        .topbar,
        footer {
            display: none;
        }
        .member-area.sidebar-closed {
            grid-template-columns: 0 minmax(0, 1fr);
        }
        .member-menu-button {
            position: fixed;
            top: 18px;
            left: 260px;
            z-index: 60;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            padding: 10px 13px;
            border: 1px solid rgba(47, 123, 255, .2);
            border-left: 0;
            border-radius: 0 14px 14px 0;
            color: #07164d;
            background: rgba(255, 255, 255, .9);
            box-shadow: 12px 12px 30px rgba(16, 85, 245, .12);
            backdrop-filter: blur(14px);
            cursor: pointer;
            font-weight: 900;
            transition: left .24s ease, box-shadow .2s ease, background .2s ease;
        }
        .member-area.sidebar-closed .member-menu-button {
            left: 0;
        }
        .member-menu-button span {
            width: 18px;
            height: 2px;
            display: block;
            position: relative;
            border-radius: 999px;
            background: #2f7bff;
        }
        .member-menu-button span::before,
        .member-menu-button span::after {
            content: "";
            position: absolute;
            left: 0;
            width: 18px;
            height: 2px;
            border-radius: 999px;
            background: #2f7bff;
        }
        .member-menu-button span::before {
            top: -6px;
        }
        .member-menu-button span::after {
            top: 6px;
        }
        .member-menu-overlay {
            display: none;
        }
        .member-sidebar {
            background: #ffffff;
            border-right: 1px solid rgba(47, 123, 255, .12);
            padding: 28px 24px;
            overflow: hidden;
            transition: transform .24s ease, opacity .2s ease, padding .24s ease, border-color .2s ease;
        }
        .member-area.sidebar-closed .member-sidebar {
            transform: translateX(-100%);
            opacity: 0;
            padding-inline: 0;
            border-color: transparent;
        }
        .member-profile {
            display: grid;
            justify-items: center;
            gap: 12px;
            margin-bottom: 30px;
            text-align: center;
        }
        .member-avatar {
            width: 104px;
            height: 104px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            color: #ffffff;
            font-size: 34px;
            font-weight: 900;
            background:
                radial-gradient(circle at 35% 25%, rgba(255,255,255,.45), transparent 28%),
                linear-gradient(145deg, #2f7bff, #00d4ff);
            border: 6px solid #e5f0ff;
            box-shadow: 0 16px 34px rgba(16, 85, 245, .14);
        }
        .member-profile strong {
            color: #07164d;
            font-size: 14px;
        }
        .sidebar-group {
            border-top: 1px solid rgba(15, 23, 42, .08);
            padding-top: 14px;
            margin-top: 14px;
        }
        .sidebar-title {
            margin: 0 0 10px;
            color: #4b587c;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 38px;
            padding: 8px 10px;
            border-radius: 8px;
            color: #4b587c;
            font-size: 14px;
            font-weight: 700;
        }
        .sidebar-link.active,
        .sidebar-link:hover {
            color: #3157dc;
            background: rgba(47, 123, 255, .08);
        }
        .sidebar-icon {
            width: 20px;
            height: 20px;
            display: inline-grid;
            place-items: center;
            color: #3157dc;
        }
        .member-content {
            padding: 28px clamp(18px, 4vw, 44px) 48px;
            padding-top: 82px;
            min-width: 0;
        }
        .purchase-alert {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
            padding: 14px 18px;
            border: 1px solid rgba(47, 123, 255, .42);
            border-radius: 8px;
            background: #dff2ff;
            color: #31577d;
        }
        .purchase-alert strong {
            display: block;
            color: #31577d;
        }
        .purchase-alert a {
            color: #3157dc;
            font-weight: 800;
        }
        .purchase-icon {
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            color: #2f7bff;
        }
        .member-hero {
            display: grid;
            grid-template-columns: minmax(260px, .9fr) minmax(0, 1.1fr);
            align-items: center;
            gap: 18px;
            min-height: 280px;
            padding: 28px 36px;
            border-radius: 12px;
            color: #ffffff;
            background:
                radial-gradient(circle at 30% 38%, rgba(255,255,255,.28), transparent 18rem),
                linear-gradient(105deg, #86c8ff 0%, #4ba1ff 44%, #2f7bff 100%);
            box-shadow: 0 22px 48px rgba(16, 85, 245, .18);
            overflow: hidden;
        }
        .rocket-illustration {
            width: min(340px, 100%);
            justify-self: center;
            filter: drop-shadow(0 24px 28px rgba(16, 85, 245, .24));
        }
        .member-hero h1 {
            margin: 0 0 14px;
            font-size: clamp(26px, 3.4vw, 38px);
            line-height: 1.2;
        }
        .member-hero p {
            max-width: 560px;
            margin: 0 0 22px;
            color: rgba(255, 255, 255, .9);
            text-align: left;
        }
        .member-section {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid rgba(15, 23, 42, .08);
        }
        .member-section h2 {
            margin: 0 0 16px;
            color: #07164d;
            font-size: 22px;
        }
        .member-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }
        .member-stat {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 98px;
            padding: 18px;
            border: 1px solid rgba(47, 123, 255, .12);
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 12px 28px rgba(16, 85, 245, .08);
        }
        .stat-icon {
            width: 54px;
            height: 54px;
            flex: 0 0 54px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .03em;
            line-height: 1;
            text-align: center;
        }
        .stat-icon.blue { background: #2f7bff; }
        .stat-icon.cyan { background: #22c9d7; }
        .stat-icon.green { background: #22c55e; }
        .stat-icon.yellow { background: #facc15; }
        .member-stat span {
            display: block;
            color: #4b587c;
            font-size: 14px;
            font-weight: 800;
        }
        .member-stat strong {
            display: block;
            margin-top: 4px;
            color: #2f7bff;
            font-size: 22px;
        }
        .course-access-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }
        .access-card {
            border: 1px solid rgba(47, 123, 255, .14);
            border-radius: 14px;
            padding: 18px;
            background: #ffffff;
            box-shadow: 0 12px 28px rgba(16, 85, 245, .08);
        }
        .access-card h3 {
            margin: 6px 0 8px;
            color: #07164d;
        }
        .access-card p {
            margin: 0;
            color: #4b587c;
            text-align: left;
        }
        .progress-track {
            height: 10px;
            margin-top: 14px;
            border-radius: 999px;
            overflow: hidden;
            background: #e9f1ff;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #2f7bff, #00d4ff);
        }
        .module-list {
            display: grid;
            gap: 12px;
        }
        .module-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 16px;
            align-items: center;
            border: 1px solid rgba(47, 123, 255, .14);
            border-radius: 12px;
            padding: 16px;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(16, 85, 245, .06);
        }
        .module-row strong {
            display: block;
            color: #07164d;
        }
        .module-row p {
            margin: 4px 0 0;
            color: #4b587c;
            text-align: left;
            font-size: 14px;
        }
        .announcement-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }
        .announcement-card {
            border: 1px solid rgba(47, 123, 255, .14);
            border-radius: 12px;
            padding: 16px;
            background: #ffffff;
        }
        .announcement-card strong {
            color: #07164d;
        }
        .announcement-card p {
            margin: 6px 0 0;
            color: #4b587c;
            text-align: left;
        }
        .dashboard-headline {
            display: grid;
            grid-template-columns: 72px minmax(0, 1fr) auto;
            gap: 18px;
            align-items: center;
            margin-bottom: 18px;
            padding: 22px;
            border: 1px solid rgba(47, 123, 255, .14);
            border-radius: 20px;
            background:
                radial-gradient(circle at 88% 20%, rgba(0, 212, 255, .16), transparent 16rem),
                #ffffff;
            box-shadow: 0 16px 36px rgba(16, 85, 245, .08);
        }
        .headline-icon {
            width: 72px;
            height: 72px;
            display: grid;
            place-items: center;
            border-radius: 22px;
            color: #ffffff;
            background:
                linear-gradient(145deg, #3157dc, #00d4ff);
            box-shadow: 0 18px 32px rgba(49, 87, 220, .22);
            font-size: 20px;
            font-weight: 900;
            letter-spacing: .04em;
        }
        .dashboard-headline h1 {
            margin: 0;
            color: #07164d;
            font-size: clamp(28px, 3.5vw, 44px);
            line-height: 1.12;
        }
        .dashboard-headline p {
            max-width: 760px;
            margin: 12px 0 0;
            color: #4b587c;
            text-align: left;
            line-height: 1.65;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            color: #3157dc;
            background: rgba(47, 123, 255, .09);
            border: 1px solid rgba(47, 123, 255, .16);
        }
        .status-pill.in-progress {
            color: #b45309;
            background: rgba(245, 158, 11, .14);
            border-color: rgba(245, 158, 11, .24);
        }
        .status-pill.done {
            color: #15803d;
            background: rgba(34, 197, 94, .12);
            border-color: rgba(34, 197, 94, .22);
        }
        .locked-card {
            position: relative;
        }
        .locked-card::after {
            content: "Terkunci";
            position: absolute;
            top: 16px;
            right: 16px;
            padding: 7px 10px;
            border-radius: 999px;
            color: #ffffff;
            background: #07164d;
            font-size: 12px;
            font-weight: 900;
        }
        .discussion-card {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr) auto;
            gap: 14px;
            align-items: center;
        }
        .discussion-icon {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            color: #ffffff;
            background: linear-gradient(145deg, #3157dc, #00d4ff);
            font-weight: 900;
        }
        .dashboard-footer {
            margin-top: 30px;
            padding: 20px;
            border-radius: 16px;
            color: #4b587c;
            background: #ffffff;
            border: 1px solid rgba(47, 123, 255, .14);
            box-shadow: 0 10px 24px rgba(16, 85, 245, .06);
        }
        @media (max-width: 980px) {
            .member-area {
                display: block;
            }
            .member-menu-button {
                top: 12px;
                left: 0;
            }
            .member-area:not(.sidebar-closed) .member-menu-button {
                left: min(84vw, 310px);
            }
            .member-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 55;
                width: min(84vw, 310px);
                max-width: 310px;
                overflow-y: auto;
                box-shadow: 24px 0 60px rgba(7, 22, 77, .18);
                transform: translateX(-105%);
                opacity: 1;
                padding: 76px 22px 28px;
            }
            .member-area:not(.sidebar-closed) .member-sidebar {
                transform: translateX(0);
                padding: 76px 22px 28px;
            }
            .member-area.sidebar-closed .member-sidebar {
                padding: 76px 22px 28px;
            }
            .member-menu-overlay {
                position: fixed;
                inset: 0;
                z-index: 50;
                display: block;
                background: rgba(7, 22, 77, .42);
                opacity: 0;
                pointer-events: none;
                transition: opacity .2s ease;
            }
            .member-area:not(.sidebar-closed) .member-menu-overlay {
                opacity: 1;
                pointer-events: auto;
            }
            .member-content {
                padding-top: 72px;
            }
            .member-hero {
                grid-template-columns: 1fr;
                padding: 24px 18px;
            }
            .rocket-illustration {
                max-width: 260px;
                order: -1;
            }
            .member-stats,
            .course-access-grid,
            .announcement-grid {
                grid-template-columns: 1fr;
            }
            .module-row {
                grid-template-columns: 1fr;
            }
            .dashboard-headline,
            .discussion-card {
                grid-template-columns: 1fr;
            }
            .headline-icon {
                width: 58px;
                height: 58px;
                border-radius: 18px;
                font-size: 17px;
            }
        }
    </style>

    <div class="member-area" id="participantDashboard">
        <button class="member-menu-button" type="button" aria-controls="participantMenu" aria-expanded="true">
            <span aria-hidden="true"></span>
            Menu
        </button>

        <div class="member-menu-overlay" data-close-participant-menu></div>

        <aside class="member-sidebar" id="participantMenu" aria-label="Menu peserta">
            <div class="member-profile">
                <div class="member-avatar">{{ $initials ?: 'TL' }}</div>
                <strong>{{ $user->name }}</strong>
            </div>

            <div class="sidebar-group">
                <p class="sidebar-title">Overview</p>
                <a class="sidebar-link active" href="#dashboard"><span class="sidebar-icon">DH</span> Dashboard</a>
                <a class="sidebar-link" href="#data-belajar"><span class="sidebar-icon">DB</span> Data Belajar</a>
                <a class="sidebar-link" href="#kelas-dipilih"><span class="sidebar-icon">KD</span> Kelas Dipilih</a>
                <a class="sidebar-link" href="#rekomendasi"><span class="sidebar-icon">RM</span> Rekomendasi</a>
            </div>

            <div class="sidebar-group">
                <p class="sidebar-title">LMS</p>
                <a class="sidebar-link" href="#produk-terbaru"><span class="sidebar-icon">PT</span> Produk Terbaru</a>
                <a class="sidebar-link" href="#grup-diskusi"><span class="sidebar-icon">GD</span> Grup Diskusi</a>
                <a class="sidebar-link" href="#profil"><span class="sidebar-icon">PF</span> Profil</a>
            </div>

            <div class="sidebar-group">
                <p class="sidebar-title">Support</p>
                <a class="sidebar-link" href="{{ $support['whatsapp'] }}" target="_blank" rel="noopener"><span class="sidebar-icon">WA</span> WhatsApp</a>
                <a class="sidebar-link" href="{{ $support['email'] }}"><span class="sidebar-icon">@</span> Email Admin</a>
            </div>
        </aside>

        <main class="member-content">
            <section id="dashboard">
                <div class="dashboard-headline">
                    <span class="headline-icon" aria-hidden="true">TL</span>
                    <div>
                        <h1>Dashboard LMS TECHVERSE Learning</h1>
                        <p>
                            Selamat datang, {{ $user->name }}. Pantau kelas yang sudah dibeli,
                            cek progres belajar, lanjutkan modul aktif, dan temukan rekomendasi level berikutnya.
                        </p>
                    </div>
                    <a class="button" href="{{ $ctaUrl }}">{{ $ctaLabel }}</a>
                </div>

                <div class="purchase-alert">
                    <svg class="purchase-icon" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                        <path d="M10 15h23v9a9 9 0 0 1-9 9h-5a9 9 0 0 1-9-9v-9z" fill="currentColor" opacity=".18"/>
                        <path d="M10 15h23v9a9 9 0 0 1-9 9h-5a9 9 0 0 1-9-9v-9zM33 18h5a5 5 0 0 1 0 10h-5M8 38h30" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div>
                        <strong>{{ $firstCourse ? 'Akses kelas Anda sudah aktif.' : 'Belum ada akses kelas aktif.' }}</strong>
                        <a href="{{ $ctaUrl }}">{{ $firstCourse ? 'Klik di sini untuk melanjutkan produk pembelian' : 'Pilih program untuk memulai registrasi kelas' }}</a>
                    </div>
                </div>
            </section>

            <section class="member-section" id="data-belajar">
                <h2>Ringkasan Progress Peserta</h2>
                <div class="member-stats">
                    <div class="member-stat">
                        <span class="stat-icon yellow">CL</span>
                        <div><span>Kelas Diikuti</span><strong>{{ $enrollments->count() }} kelas</strong></div>
                    </div>
                    <div class="member-stat">
                        <span class="stat-icon cyan">MD</span>
                        <div><span>Modul Dibaca</span><strong>{{ $completedModules }} dari {{ $modules->count() }}</strong></div>
                    </div>
                    <div class="member-stat">
                        <span class="stat-icon blue">VD</span>
                        <div><span>Video Ditonton</span><strong>{{ $watchedVideos }} dari {{ $totalVideoLessons }}</strong></div>
                    </div>
                    <div class="member-stat">
                        <span class="stat-icon green">%</span>
                        <div><span>Progress</span><strong>{{ $overallProgress }}%</strong></div>
                    </div>
                </div>
            </section>

            <section class="member-section" id="kelas-dipilih">
                <h2>Kelas Saya</h2>
                <div class="course-access-grid">
                    @forelse($enrollments as $enrollment)
                        @php
                            $course = $enrollment->course;
                            $lessonCount = $course->modules->sum(fn ($module) => $module->lessons->count());
                            $completedCount = $enrollment->progress->where('progress_percent', 100)->count();
                            $progress = $lessonCount > 0 ? round(($completedCount / $lessonCount) * 100) : 0;
                            $statusLabel = $progress >= 100 ? 'Selesai' : ($progress > 0 ? 'Berlangsung' : 'Belum mulai');
                            $statusClass = $progress >= 100 ? 'done' : ($progress > 0 ? 'in-progress' : '');
                        @endphp
                        <article class="access-card">
                            <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                            <h3>{{ $course->title }}</h3>
                            <p>{{ $course->summary }}</p>
                            <div class="meta">
                                <span class="badge">{{ $lessonCount }} lesson</span>
                                <span class="badge">{{ $course->modules->count() }} modul</span>
                                <span class="badge">{{ $progress }}% selesai</span>
                            </div>
                            <div class="progress-track"><div class="progress-fill" style="width:{{ $progress }}%"></div></div>
                            <div class="meta">
                                <a class="button" href="{{ route('lms.courses.show', $course) }}">{{ $progress > 0 ? 'Lanjutkan Kelas' : 'Mulai Kelas' }}</a>
                            </div>
                        </article>
                    @empty
                        <article class="access-card">
                            <span class="status-pill">Belum mulai</span>
                            <h3>Belum ada kelas aktif</h3>
                            <p>Kelas akan tampil setelah pembayaran diverifikasi dan akses peserta diaktifkan.</p>
                            <div class="meta"><a class="button" href="{{ route('register') }}">Pilih Paket Kelas</a></div>
                        </article>
                    @endforelse
                </div>
            </section>

            <section class="member-section" id="rekomendasi">
                <h2>Roadmap Level Berikutnya</h2>
                <div class="course-access-grid">
                    @forelse($recommendedCourses as $course)
                        <article class="access-card locked-card">
                            <span class="badge">{{ $course->level }}</span>
                            <h3>{{ $course->title }}</h3>
                            <p>{{ $course->summary }}</p>
                            <div class="meta">
                                <span class="badge">{{ $course->modules->count() }} modul</span>
                                <span class="badge">{{ $course->modules->sum(fn ($module) => $module->lessons->count()) }} lesson</span>
                                <span class="badge">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                            </div>
                            <a class="button" href="{{ route('purchase.create', $course) }}">Buka Akses Level Ini</a>
                        </article>
                    @empty
                        <article class="access-card">
                            <h3>Rekomendasi segera tersedia</h3>
                            <p>Semua kelas yang tersedia sudah masuk akses Anda atau admin belum menerbitkan roadmap baru.</p>
                        </article>
                    @endforelse
                </div>
            </section>

            <section class="member-section" id="produk-terbaru">
                <h2>Update Kelas Terbaru</h2>
                <div class="module-list">
                    @forelse($latestCourses as $course)
                        <article class="module-row">
                            <div>
                                <span class="badge">{{ $course->level }} / {{ ucfirst($course->status) }}</span>
                                <strong>{{ $course->title }}</strong>
                                <p>{{ $course->summary }}</p>
                                <div class="meta">
                                    <span class="badge">{{ $course->modules->count() }} modul</span>
                                    <span class="badge">{{ $course->modules->sum(fn ($module) => $module->lessons->count()) }} lesson</span>
                                    <span class="badge">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <a class="button" href="{{ route('purchase.create', $course) }}">Lihat Paket</a>
                        </article>
                    @empty
                        <article class="module-row">
                            <div>
                                <strong>Belum ada produk terbaru</strong>
                                <p>Admin akan menambahkan update modul terbaru di halaman ini.</p>
                            </div>
                        </article>
                    @endforelse
                </div>
            </section>

            <section class="member-section" id="grup-diskusi">
                <h2>Forum Belajar Peserta</h2>
                <div class="module-list">
                    @foreach($discussionGroups as $group)
                        <article class="access-card discussion-card">
                            <span class="discussion-icon">{{ str_starts_with($group['name'], 'Telegram') ? 'TG' : 'DC' }}</span>
                            <div>
                                <h3>{{ $group['name'] }}</h3>
                                <p>{{ $group['description'] }}</p>
                            </div>
                            <a class="button" href="{{ $group['url'] }}" target="_blank" rel="noopener">Gabung</a>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="member-section" id="profil">
                <h2>Profil & Bantuan</h2>
                <div class="announcement-grid" id="bantuan">
                    <article class="announcement-card">
                        <strong>{{ $user->name }}</strong>
                        <p>{{ $user->email }} - {{ optional($user->role)->label ?? 'Peserta' }}</p>
                    </article>
                    <article class="announcement-card">
                        <strong>Kontak Admin</strong>
                        <p>WhatsApp: <a href="{{ $support['whatsapp'] }}" target="_blank" rel="noopener">{{ $support['whatsapp_label'] }}</a><br>Email: <a href="{{ $support['email'] }}">{{ $support['email_label'] }}</a></p>
                    </article>
                </div>
            </section>

            <section class="dashboard-footer">
                <p>
                    TECHVERSE Learning LMS membantu peserta mengikuti roadmap cyber security secara bertahap:
                    belajar, praktik, berdiskusi, lalu naik ke level berikutnya saat siap.
                </p>
            </section>
        </main>
    </div>

    <script>
        (function () {
            const area = document.getElementById('participantDashboard');
            if (!area) {
                return;
            }

            const button = area.querySelector('.member-menu-button');
            const overlay = area.querySelector('[data-close-participant-menu]');
            const links = area.querySelectorAll('.sidebar-link');

            const setMenu = (isOpen) => {
                area.classList.toggle('sidebar-closed', !isOpen);
                button?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            };

            const syncDefault = () => {
                if (area.dataset.menuTouched === 'true') {
                    return;
                }

                setMenu(window.innerWidth > 980);
            };

            button?.addEventListener('click', () => {
                area.dataset.menuTouched = 'true';
                setMenu(area.classList.contains('sidebar-closed'));
            });

            overlay?.addEventListener('click', () => {
                area.dataset.menuTouched = 'true';
                setMenu(false);
            });

            links.forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 980) {
                        area.dataset.menuTouched = 'true';
                        setMenu(false);
                    }
                });
            });

            syncDefault();
            window.addEventListener('resize', syncDefault);
        })();
    </script>
@endsection
