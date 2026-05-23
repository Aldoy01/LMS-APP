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
            left: 18px;
            z-index: 60;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            padding: 10px 14px;
            border: 1px solid rgba(47, 123, 255, .2);
            border-radius: 12px;
            color: #07164d;
            background: rgba(255, 255, 255, .9);
            box-shadow: 0 12px 30px rgba(16, 85, 245, .14);
            backdrop-filter: blur(14px);
            cursor: pointer;
            font-weight: 900;
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
        @media (max-width: 980px) {
            .member-area {
                display: block;
            }
            .member-menu-button {
                top: 12px;
                left: 12px;
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
                <a class="sidebar-link" href="#akses-kelas"><span class="sidebar-icon">PK</span> Produk Kelas</a>
                <a class="sidebar-link" href="#modul"><span class="sidebar-icon">MD</span> Modul</a>
                <a class="sidebar-link" href="#pengumuman"><span class="sidebar-icon">UP</span> Update</a>
            </div>

            <div class="sidebar-group">
                <p class="sidebar-title">Akses</p>
                <a class="sidebar-link" href="#akses-kelas"><span class="sidebar-icon">AK</span> Akses Kelas</a>
                <a class="sidebar-link" href="#profil"><span class="sidebar-icon">PF</span> Profil</a>
                <a class="sidebar-link" href="#bantuan"><span class="sidebar-icon">CS</span> Bantuan</a>
            </div>

            <div class="sidebar-group">
                <p class="sidebar-title">Support</p>
                <a class="sidebar-link" href="{{ $support['whatsapp'] }}" target="_blank" rel="noopener"><span class="sidebar-icon">WA</span> WhatsApp</a>
                <a class="sidebar-link" href="{{ $support['email'] }}"><span class="sidebar-icon">@</span> Email Admin</a>
            </div>
        </aside>

        <main class="member-content">
            <section id="dashboard">
                <div class="purchase-alert">
                    <svg class="purchase-icon" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                        <path d="M10 15h23v9a9 9 0 0 1-9 9h-5a9 9 0 0 1-9-9v-9z" fill="currentColor" opacity=".18"/>
                        <path d="M10 15h23v9a9 9 0 0 1-9 9h-5a9 9 0 0 1-9-9v-9zM33 18h5a5 5 0 0 1 0 10h-5M8 38h30" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div>
                        <strong>Terima kasih telah membeli paket kelas TechVerse Learning.</strong>
                        <a href="{{ $ctaUrl }}">{{ $firstCourse ? 'Klik di sini untuk akses kelas pembelian' : 'Pilih program learning untuk mulai belajar' }}</a>
                    </div>
                </div>

                <div class="member-hero">
                    <svg class="rocket-illustration" viewBox="0 0 420 300" fill="none" aria-hidden="true">
                        <ellipse cx="190" cy="264" rx="116" ry="18" fill="#1e3a8a" opacity=".14"/>
                        <path d="M194 218c-44 14-80 38-104 70 43-10 78-26 105-50l-1-20z" fill="#dbeafe"/>
                        <path d="M235 225c13 36 35 62 76 76-3-42-19-78-48-103l-28 27z" fill="#dbeafe"/>
                        <path d="M118 196c54-98 132-151 236-168-14 103-67 181-166 236l-70-68z" fill="#ffffff" stroke="#3157dc" stroke-width="6"/>
                        <path d="M300 42c-5 36-22 70-51 102-28 30-61 51-98 63l37 37c99-55 152-133 166-236-18 3-36 7-54 14z" fill="#2f7bff" opacity=".22"/>
                        <circle cx="266" cy="108" r="34" fill="#dbeafe" stroke="#3157dc" stroke-width="6"/>
                        <circle cx="266" cy="108" r="17" fill="#2f7bff"/>
                        <path d="M112 197l75 75-65 17-27-27 17-65z" fill="#3157dc"/>
                        <path d="M95 262c-19 8-37 22-52 43 26-7 47-16 63-30l-11-13z" fill="#f59e0b"/>
                        <path d="M182 67c-22 1-50 7-74 28 29 2 53 9 72 23l2-51z" fill="#dbeafe" stroke="#3157dc" stroke-width="6"/>
                        <path d="M91 122c-19 21-29 48-28 79 26-19 53-29 79-31l-51-48z" fill="#dbeafe" stroke="#3157dc" stroke-width="6"/>
                        <path d="M225 184l-67-67" stroke="#3157dc" stroke-width="8" stroke-linecap="round"/>
                        <circle cx="166" cy="111" r="28" fill="#ffffff" stroke="#dbeafe" stroke-width="8"/>
                        <path d="M166 86c-10-18-2-38 18-45 17 15 16 34 3 50" stroke="#3157dc" stroke-width="5" stroke-linecap="round"/>
                        <circle cx="156" cy="111" r="6" fill="#07164d"/>
                        <circle cx="179" cy="111" r="6" fill="#07164d"/>
                        <path d="M158 130c8 6 18 6 26 0" stroke="#07164d" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                    <div>
                        <h1>Selamat Datang {{ $user->name }}</h1>
                        <p>
                            Nikmati kemudahan akses seluruh kelas cyber security dalam satu dashboard.
                            Lanjutkan modul, pantau progress, dan hubungi admin jika membutuhkan bantuan.
                        </p>
                        <a class="button" href="{{ $ctaUrl }}">{{ $ctaLabel }}</a>
                    </div>
                </div>
            </section>

            <section class="member-section" id="data-hari-ini">
                <h2>Data Belajar Hari Ini</h2>
                <div class="member-stats">
                    <div class="member-stat">
                        <span class="stat-icon yellow">CL</span>
                        <div><span>Kelas Aktif</span><strong>{{ $enrollments->count() }}</strong></div>
                    </div>
                    <div class="member-stat">
                        <span class="stat-icon cyan">OK</span>
                        <div><span>Total Modul</span><strong>{{ $modules->count() }}</strong></div>
                    </div>
                    <div class="member-stat">
                        <span class="stat-icon blue">%</span>
                        <div><span>Progress</span><strong>{{ $overallProgress }}%</strong></div>
                    </div>
                    <div class="member-stat">
                        <span class="stat-icon green">CS</span>
                        <div><span>Bantuan</span><strong>Aktif</strong></div>
                    </div>
                </div>
            </section>

            <section class="member-section" id="akses-kelas">
                <h2>Akses Kelas Saya</h2>
                <div class="course-access-grid">
                    @forelse($enrollments as $enrollment)
                        @php
                            $course = $enrollment->course;
                            $lessonCount = $course->modules->sum(fn ($module) => $module->lessons->count());
                            $completedCount = $enrollment->progress->where('progress_percent', 100)->count();
                            $progress = $lessonCount > 0 ? round(($completedCount / $lessonCount) * 100) : 0;
                        @endphp
                        <article class="access-card">
                            <span class="badge">{{ $enrollment->access_type }} / {{ optional($enrollment->started_at)->format('d M Y') ?? 'Belum mulai' }}</span>
                            <h3>{{ $course->title }}</h3>
                            <p>{{ $course->summary }}</p>
                            <div class="meta">
                                <span class="badge">{{ $lessonCount }} lesson</span>
                                <span class="badge">{{ $progress }}% selesai</span>
                            </div>
                            <div class="progress-track"><div class="progress-fill" style="width:{{ $progress }}%"></div></div>
                            <div class="meta">
                                <a class="button" href="{{ route('lms.courses.show', $course) }}">Masuk Kelas</a>
                            </div>
                        </article>
                    @empty
                        <article class="access-card">
                            <h3>Belum ada kelas aktif</h3>
                            <p>Kelas akan tampil setelah pembayaran diverifikasi dan akses peserta diaktifkan.</p>
                            <div class="meta"><a class="button" href="{{ route('lms.dashboard') }}#program">Pilih Program</a></div>
                        </article>
                    @endforelse
                </div>
            </section>

            <section class="member-section" id="modul">
                <h2>Modul Pembelajaran</h2>
                <div class="module-list">
                    @forelse($modules as $item)
                        <article class="module-row">
                            <div>
                                <span class="badge">{{ $item['category'] }} / Modul {{ $item['module']->sort_order }}</span>
                                <strong>{{ $item['module']->title }}</strong>
                                <p>{{ $item['module']->summary }}</p>
                                <div class="meta">
                                    <span class="badge">{{ $item['duration_minutes'] }} menit</span>
                                    <span class="badge">{{ $item['lesson_count'] }} lesson</span>
                                    <span class="badge">{{ $item['progress'] }}% selesai</span>
                                </div>
                            </div>
                            <a class="button" href="{{ route('lms.courses.show', $item['course']) }}">Buka Modul</a>
                        </article>
                    @empty
                        <article class="module-row">
                            <div>
                                <strong>Belum ada modul aktif</strong>
                                <p>Hubungi admin jika Anda sudah melakukan pembayaran tetapi kelas belum tampil.</p>
                            </div>
                            <a class="button" href="{{ $support['whatsapp'] }}" target="_blank" rel="noopener">Hubungi Admin</a>
                        </article>
                    @endforelse
                </div>
            </section>

            <section class="member-section" id="pengumuman">
                <h2>Pengumuman Admin</h2>
                <div class="announcement-grid">
                    @foreach($announcements as $announcement)
                        <article class="announcement-card">
                            <strong>{{ $announcement['title'] }}</strong>
                            <p>{{ $announcement['body'] }}</p>
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
