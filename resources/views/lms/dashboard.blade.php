@extends('layouts.lms', ['title' => 'TECHVERSE Learning'])

@section('content')
    @php
        $settings = $siteSettings ?? \App\Models\SiteSetting::DEFAULTS;
        $memberUrl = auth()->check() ? route('participant.home') : route('login');
        $memberLabel = auth()->check() ? 'Buka Dashboard' : 'Start Learning';
        $whatsappNumber = preg_replace('/\D+/', '', $settings['contact_whatsapp'] ?? '08513332305');
        $whatsappNumber = str_starts_with($whatsappNumber, '0') ? '62' . substr($whatsappNumber, 1) : $whatsappNumber;
        $contactUrl = 'https://wa.me/' . $whatsappNumber;
    @endphp

    <style>
        .tv-home {
            color: #0f172a;
            background:
                radial-gradient(circle at 18% 18%, rgba(49, 87, 220, .16), transparent 22rem),
                radial-gradient(circle at 82% 14%, rgba(0, 212, 255, .18), transparent 20rem),
                linear-gradient(180deg, #f8fbff 0%, #ffffff 48%, #f4f8ff 100%);
        }
        .tv-hero {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, .9fr);
            gap: clamp(24px, 5vw, 68px);
            align-items: center;
            min-height: calc(100vh - 92px);
            padding: clamp(42px, 7vw, 92px) 0;
        }
        .tv-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 42px;
            padding: 9px 14px;
            border-radius: 999px;
            color: var(--brand-dark);
            background: #ffffff;
            border: 1px solid rgba(47, 123, 255, .16);
            box-shadow: 0 12px 30px rgba(16, 85, 245, .1);
            font-weight: 900;
        }
        .tv-hero h1 {
            margin: 24px 0 0;
            color: #07164d;
            font-size: clamp(42px, 7vw, 86px);
            line-height: .98;
            letter-spacing: 0;
        }
        .tv-hero h1 span {
            display: block;
            color: var(--brand-dark);
        }
        .tv-hero p {
            max-width: 720px;
            margin: 22px 0 0;
            color: #4b587c;
            font-size: clamp(17px, 2vw, 22px);
            line-height: 1.65;
            text-align: left;
        }
        .tv-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }
        .tv-button {
            min-height: 54px;
            padding: 13px 20px;
            border-radius: 14px;
            box-shadow: 0 14px 28px rgba(16, 85, 245, .16);
        }
        .tv-button.secondary {
            color: var(--brand-dark);
            background: #ffffff;
            border: 1px solid rgba(47, 123, 255, .2);
            text-shadow: none;
        }
        .tv-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 34px;
        }
        .tv-stat {
            padding: 14px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .82);
            border: 1px solid rgba(47, 123, 255, .13);
            box-shadow: 0 12px 30px rgba(16, 85, 245, .08);
        }
        .tv-stat strong {
            display: block;
            color: #07164d;
            font-size: 26px;
        }
        .tv-stat span {
            display: block;
            margin-top: 4px;
            color: #4b587c;
            font-size: 13px;
            font-weight: 800;
        }
        .tv-orbit {
            position: relative;
            min-height: 520px;
            display: grid;
            place-items: center;
        }
        .tv-orbit-card {
            position: relative;
            width: min(430px, 100%);
            min-height: 430px;
            border-radius: 40px;
            background:
                radial-gradient(circle at 50% 35%, rgba(255,255,255,.98), rgba(255,255,255,.72) 42%, rgba(219,234,254,.8) 100%);
            border: 1px solid rgba(47, 123, 255, .18);
            box-shadow: 0 30px 80px rgba(16, 85, 245, .18);
            overflow: hidden;
        }
        .tv-orbit-card::before {
            content: "";
            position: absolute;
            inset: 42px;
            border-radius: 999px;
            background:
                conic-gradient(from 140deg, var(--brand-dark), var(--accent), #22c55e, #f59e0b, var(--brand-dark));
            filter: blur(.2px);
            opacity: .9;
        }
        .tv-orbit-card::after {
            content: "TECHVERSE";
            position: absolute;
            inset: 104px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            color: #ffffff;
            background: linear-gradient(145deg, #07164d, var(--brand-dark));
            font-size: clamp(20px, 3vw, 34px);
            font-weight: 900;
            letter-spacing: .08em;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.24);
        }
        .floating-chip {
            position: absolute;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 14px;
            border-radius: 18px;
            color: #07164d;
            background: #ffffff;
            border: 1px solid rgba(47, 123, 255, .14);
            box-shadow: 0 18px 36px rgba(16, 85, 245, .14);
            font-weight: 900;
        }
        .chip-one { top: 58px; left: 4px; }
        .chip-two { right: 0; top: 132px; }
        .chip-three { bottom: 78px; left: 18px; }
        .tv-section {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 34px 0;
        }
        .tv-section-head {
            text-align: center;
            margin-bottom: 22px;
        }
        .tv-section-head h2 {
            margin: 0;
            color: #07164d;
            font-size: clamp(30px, 4vw, 48px);
        }
        .tv-section-head p {
            margin: 10px auto 0;
            max-width: 680px;
            color: #4b587c;
            line-height: 1.7;
        }
        .path-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }
        .path-card {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100%;
            padding: 26px;
            border-radius: 30px;
            color: #07164d;
            background:
                radial-gradient(circle at 86% 12%, var(--path-glow, rgba(0, 212, 255, .18)), transparent 11rem),
                linear-gradient(145deg, rgba(255,255,255,.96), rgba(248,251,255,.88));
            border: 1px solid rgba(47, 123, 255, .16);
            box-shadow:
                0 20px 48px rgba(16, 85, 245, .11),
                inset 0 1px 0 rgba(255, 255, 255, .9);
            transform: translateY(0);
            animation: pathRise .75s ease both;
            transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease;
        }
        .path-card:nth-child(2) { animation-delay: .08s; }
        .path-card:nth-child(3) { animation-delay: .16s; }
        .path-card::before {
            content: "";
            position: absolute;
            inset: auto -30% -42% 22%;
            z-index: -1;
            height: 150px;
            border-radius: 999px;
            background: linear-gradient(90deg, transparent, var(--path-accent, var(--accent)), transparent);
            filter: blur(22px);
            opacity: .42;
            transform: rotate(-8deg);
        }
        .path-card::after {
            content: "";
            position: absolute;
            top: 22px;
            right: 22px;
            width: 74px;
            height: 74px;
            border-radius: 999px;
            border: 1px dashed rgba(49, 87, 220, .24);
            animation: pathOrbit 8s linear infinite;
        }
        .path-card:hover {
            transform: translateY(-8px);
            border-color: rgba(47, 123, 255, .28);
            box-shadow:
                0 28px 64px rgba(16, 85, 245, .18),
                inset 0 1px 0 rgba(255, 255, 255, .96);
        }
        .path-card.learn {
            --path-accent: #3157dc;
            --path-glow: rgba(49, 87, 220, .18);
        }
        .path-card.tools {
            --path-accent: #00d4ff;
            --path-glow: rgba(0, 212, 255, .2);
        }
        .path-card.premium {
            --path-accent: #f59e0b;
            --path-glow: rgba(245, 158, 11, .18);
        }
        .path-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }
        .path-step {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            color: var(--brand-dark);
            background: rgba(47, 123, 255, .08);
            border: 1px solid rgba(47, 123, 255, .14);
            font-size: 12px;
            font-weight: 900;
        }
        .path-icon {
            position: relative;
            width: 66px;
            height: 66px;
            display: grid;
            place-items: center;
            border-radius: 22px;
            color: #ffffff;
            background:
                radial-gradient(circle at 30% 22%, rgba(255,255,255,.36), transparent 34%),
                linear-gradient(145deg, var(--path-accent, var(--brand-dark)), var(--accent));
            font-size: 28px;
            font-weight: 900;
            box-shadow:
                0 18px 34px rgba(16, 85, 245, .18),
                inset 0 1px 0 rgba(255,255,255,.3);
            animation: pathFloat 4s ease-in-out infinite;
        }
        .path-card h3 {
            margin: 22px 0 8px;
            font-size: 26px;
            letter-spacing: 0;
        }
        .path-card p {
            color: #4b587c;
            line-height: 1.65;
            min-height: 78px;
        }
        .tag-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 16px 0;
        }
        .tag-row span {
            padding: 7px 10px;
            border-radius: 999px;
            color: var(--brand-dark);
            background: rgba(255, 255, 255, .76);
            border: 1px solid rgba(47, 123, 255, .12);
            font-size: 12px;
            font-weight: 900;
        }
        .path-card .button {
            margin-top: auto;
            border-radius: 14px;
        }
        @keyframes pathFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-7px); }
        }
        @keyframes pathOrbit {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes pathRise {
            from {
                opacity: 0;
                transform: translateY(18px) scale(.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }
        .feature-card {
            padding: 20px;
            border-radius: 22px;
            background: rgba(255,255,255,.86);
            border: 1px solid rgba(47, 123, 255, .14);
        }
        .feature-card strong {
            display: block;
            margin-bottom: 8px;
            color: #07164d;
            font-size: 18px;
        }
        .program-card {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }
        .program-card .button {
            margin-top: auto;
        }
        @media (max-width: 900px) {
            .tv-hero,
            .path-grid,
            .feature-grid {
                grid-template-columns: 1fr;
            }
            .tv-hero {
                min-height: auto;
            }
            .tv-orbit {
                min-height: 380px;
            }
            .tv-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 520px) {
            .tv-stats {
                grid-template-columns: 1fr;
            }
            .tv-actions .button {
                width: 100%;
            }
            .floating-chip {
                position: static;
                margin: 8px;
            }
            .tv-orbit {
                display: block;
                min-height: auto;
            }
            .tv-orbit-card {
                min-height: 320px;
            }
            .path-card p {
                min-height: auto;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .path-card,
            .path-icon,
            .path-card::after {
                animation: none;
            }
            .path-card {
                transition: none;
            }
        }
    </style>

    <div class="tv-home">
        <section class="tv-hero" id="home">
            <div>
                <span class="tv-badge">Free Learning Platform</span>
                <h1>Welcome to {{ $settings['site_name'] ?? 'TechVerse' }} <span>Make Learning Easy & Fun</span></h1>
                <p>
                    Platform belajar cyber security dan teknologi yang membantu peserta belajar, praktik,
                    memilih roadmap, dan membangun karier digital dengan materi terarah.
                </p>
                <div class="tv-actions">
                    <a class="button tv-button" href="{{ $memberUrl }}">{{ $memberLabel }}</a>
                    <a class="button tv-button secondary" href="#program">Explore Courses</a>
                </div>
                <div class="tv-stats">
                    <div class="tv-stat"><strong>{{ $metrics['courses'] }}+</strong><span>Courses</span></div>
                    <div class="tv-stat"><strong>{{ $courses->sum(fn ($course) => $course->modules->count()) }}+</strong><span>Modules</span></div>
                    <div class="tv-stat"><strong>{{ $courses->sum(fn ($course) => $course->modules->sum(fn ($module) => $module->lessons->count())) }}+</strong><span>Lessons</span></div>
                    <div class="tv-stat"><strong>CS</strong><span>Admin Support</span></div>
                </div>
            </div>
            <div class="tv-orbit" aria-hidden="true">
                <div class="tv-orbit-card"></div>
                <div class="floating-chip chip-one">Courses</div>
                <div class="floating-chip chip-two">Tools</div>
                <div class="floating-chip chip-three">Cyber Lab</div>
            </div>
        </section>

        <section class="tv-section">
            <div class="tv-section-head">
                <h2>Choose Your Path</h2>
                <p>Pilih jalur belajar sesuai kebutuhan: mulai belajar, eksplor tools, atau masuk ke kelas premium.</p>
            </div>
            <div class="path-grid">
                <article class="path-card learn">
                    <div class="path-topline">
                        <div class="path-icon">📖</div>
                        <span class="path-step">01</span>
                    </div>
                    <h3>Start Learning</h3>
                    <p>Masuk ke materi, roadmap, modul cyber security, dan dashboard peserta.</p>
                    <div class="tag-row"><span>Courses</span><span>Roadmap</span><span>Lessons</span></div>
                    <a class="button" href="{{ $memberUrl }}">Begin Your Journey</a>
                </article>
                <article class="path-card tools">
                    <div class="path-topline">
                        <div class="path-icon">⚙</div>
                        <span class="path-step">02</span>
                    </div>
                    <h3>Explore Tools</h3>
                    <p>Temukan tools pendukung, resources, slide, PDF, video, dan workflow praktik.</p>
                    <div class="tag-row"><span>PDF</span><span>Video</span><span>Resources</span></div>
                    <a class="button" href="#program">Discover Resources</a>
                </article>
                <article class="path-card premium">
                    <div class="path-topline">
                        <div class="path-icon">✦</div>
                        <span class="path-step">03</span>
                    </div>
                    <h3>Premium Courses</h3>
                    <p>Akses kelas berbayar, modul lanjutan, praktik tools, dan studi kasus.</p>
                    <div class="tag-row"><span>Cyber Hacks</span><span>Practical</span><span>Support</span></div>
                    <a class="button" href="{{ route('register') }}">Register</a>
                </article>
            </div>
        </section>

        <section class="tv-section" id="tentang">
            <div class="tv-section-head">
                <h2>What is {{ $settings['site_name'] ?? 'TechVerse' }}?</h2>
                <p>Learning hub untuk peserta yang ingin belajar cyber security dari basic hingga praktik.</p>
            </div>
            <div class="feature-grid">
                <article class="feature-card"><strong>Comprehensive Learning</strong><p>Materi basic, intermediate, practical, PDF, video, dan resource tambahan.</p></article>
                <article class="feature-card"><strong>Powerful Tools</strong><p>Tools list, workflow pentest, dan bahan pendukung untuk latihan mandiri.</p></article>
                <article class="feature-card"><strong>Roadmap Terarah</strong><p>Peserta bisa mengikuti urutan modul dan melihat rekomendasi level berikutnya.</p></article>
                <article class="feature-card"><strong>Quick References</strong><p>Slide, cheat sheet, link resource, dan materi ringkas untuk revisi cepat.</p></article>
                <article class="feature-card"><strong>Student Benefits</strong><p>Akun peserta, akses kelas, progress belajar, dan bantuan admin dalam satu LMS.</p></article>
                <article class="feature-card"><strong>Community Driven</strong><p>Forum diskusi Telegram, WhatsApp, dan Discord untuk tanya jawab belajar.</p></article>
            </div>
        </section>

        <section class="tv-section" id="program">
            <div class="tv-section-head">
                <h2>Programming & Cyber Courses</h2>
                <p>Pilih paket kelas, daftar, lalu lanjut ke pembayaran dan aktivasi akses peserta.</p>
            </div>
            <div class="grid courses">
                @forelse($courses as $course)
                    <article class="card program-card">
                        <span class="eyebrow">{{ $course->level }} / {{ ucfirst($course->status) }}</span>
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->summary }}</p>
                        <div class="meta">
                            <span class="badge">{{ $course->modules->count() }} modul</span>
                            <span class="badge">{{ $course->modules->sum(fn ($module) => $module->lessons->count()) }} lesson</span>
                            <span class="badge">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                        </div>
                        <a class="button" href="{{ route('purchase.create', $course) }}">Order Paket</a>
                    </article>
                @empty
                    <article class="card">
                        <h3>Program segera tersedia</h3>
                        <p>Admin LMS akan menambahkan program belajar yang bisa diakses peserta terdaftar.</p>
                        <a class="button" href="{{ route('admin.login') }}">Login Admin</a>
                    </article>
                @endforelse
            </div>
        </section>

        <section class="tv-section" id="kontak">
            <div class="tv-section-head">
                <h2>Need Help?</h2>
                <p>Hubungi admin untuk akses akun, pembayaran, reset password, atau kendala kelas.</p>
                <div class="tv-actions" style="justify-content:center">
                    <a class="button tv-button" href="{{ $contactUrl }}" target="_blank" rel="noopener">WhatsApp Admin</a>
                    <a class="button tv-button secondary" href="mailto:{{ $settings['contact_email'] ?? 'admin@techverselearning.test' }}">Email Admin</a>
                </div>
            </div>
        </section>
    </div>
@endsection
