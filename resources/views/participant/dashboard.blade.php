@extends('layouts.lms', ['title' => 'Dashboard Peserta'])

@section('content')
    @php
        $firstEnrollment = $enrollments->first();
        $firstCourse = optional($firstEnrollment)->course;
        $totalLessons = $enrollments->sum(fn ($enrollment) => optional($enrollment->course)->modules?->sum(fn ($module) => $module->lessons->count()) ?? 0);
        $completedLessons = $enrollments->sum(fn ($enrollment) => $enrollment->progress->where('progress_percent', 100)->count());
        $overallProgress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
        $ctaUrl = $firstCourse ? route('lms.courses.show', $firstCourse) : route('lms.dashboard') . '#program';
        $ctaLabel = $firstCourse ? 'My Course' : 'Pilih Program';
        $categoryCounts = $modules->groupBy('category')->map->count();
        $initials = collect(explode(' ', $user->name))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('');
        $completedModules = $modules->where('progress', 100)->count();
        $totalVideoLessons = $enrollments->sum(fn ($enrollment) => optional($enrollment->course)->modules?->sum(fn ($module) => $module->lessons->where('content_type', 'video')->count()) ?? 0);
        $watchedVideos = $enrollments->sum(fn ($enrollment) => $enrollment->progress->filter(fn ($progress) => $progress->progress_percent === 100 && optional($progress->lesson)->content_type === 'video')->count());
        $participantHeroImage = $siteSettings['participant_dashboard_image_url'] ?? '';
        $participantHeroImageUrl = $participantHeroImage
            ? (Str::startsWith($participantHeroImage, ['http://', 'https://']) ? $participantHeroImage : asset($participantHeroImage))
            : '';
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
        body > footer,
        .shell > footer {
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
            overflow: hidden;
        }
        .member-avatar img { width: 100%; height: 100%; display: block; object-fit: cover; }
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
        .sidebar-logout {
            margin: 0;
        }
        .sidebar-logout button {
            width: 100%;
            border: 0;
            cursor: pointer;
            font-family: inherit;
            text-align: left;
            background: transparent;
        }
        .sidebar-icon {
            width: 20px;
            height: 20px;
            display: inline-grid;
            place-items: center;
            color: #3157dc;
        }
        .sidebar-icon svg {
            width: 19px;
            height: 19px;
            stroke: currentColor;
            stroke-width: 2.1;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        .member-content {
            position: relative;
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
            grid-template-columns: minmax(300px, .9fr) minmax(420px, 1.1fr);
            align-items: center;
            gap: clamp(24px, 5vw, 64px);
            min-height: 280px;
            margin-top: 16px;
            padding: clamp(28px, 4vw, 42px) clamp(26px, 5vw, 60px);
            border-radius: 12px;
            color: #ffffff;
            background:
                radial-gradient(circle at 28% 42%, rgba(255,255,255,.3), transparent 17rem),
                radial-gradient(circle at 84% 16%, rgba(0, 212, 255, .18), transparent 13rem),
                linear-gradient(105deg, #86c8ff 0%, #4ba1ff 44%, #2f7bff 100%);
            box-shadow: 0 22px 48px rgba(16, 85, 245, .18);
            overflow: hidden;
        }
        .hero-copy {
            max-width: 620px;
        }
        .rocket-illustration {
            width: min(360px, 100%);
            justify-self: center;
            filter: drop-shadow(0 24px 28px rgba(16, 85, 245, .24));
        }
        .member-hero-image {
            width: min(360px, 100%);
            max-height: 260px;
            justify-self: center;
            object-fit: contain;
            filter: drop-shadow(0 24px 28px rgba(16, 85, 245, .24));
        }
        .member-hero h1 {
            margin: 0 0 14px;
            font-size: clamp(26px, 3vw, 36px);
            line-height: 1.12;
            letter-spacing: 0;
        }
        .member-hero p {
            max-width: 560px;
            margin: 0 0 22px;
            color: rgba(255, 255, 255, .9);
            font-size: 14px;
            line-height: 1.6;
            text-align: left;
        }
        .hero-course-button {
            color: #ffffff;
            background:
                linear-gradient(135deg, #2f7bff 0%, #4b3db8 46%, #7d16b8 100%);
            border: 1px solid rgba(255, 255, 255, .28);
            box-shadow:
                0 14px 0 rgba(62, 22, 126, .32),
                0 22px 34px rgba(95, 34, 168, .32),
                inset 0 1px 0 rgba(255, 255, 255, .3);
            transform: translateY(0);
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }
        .hero-course-button:hover {
            color: #ffffff;
            filter: brightness(1.06);
            transform: translateY(-2px);
            box-shadow:
                0 16px 0 rgba(62, 22, 126, .28),
                0 26px 42px rgba(125, 22, 184, .34),
                inset 0 1px 0 rgba(255, 255, 255, .34);
        }
        .member-section {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid rgba(15, 23, 42, .08);
        }
        .member-section h2 {
            margin: 0 0 16px;
            color: #07164d;
            font-size: 18px;
        }
        .member-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(210px, 1fr));
            gap: 20px;
        }
        .member-stat {
            position: relative;
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr);
            align-items: center;
            gap: 16px;
            min-height: 126px;
            padding: 20px;
            overflow: hidden;
            border: 1px solid rgba(47, 123, 255, .13);
            border-radius: 20px;
            background:
                radial-gradient(circle at 94% 12%, rgba(0, 212, 255, .14), transparent 9rem),
                linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
            box-shadow:
                0 18px 42px rgba(16, 85, 245, .1),
                inset 0 1px 0 rgba(255, 255, 255, .9);
        }
        .member-stat::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 5px;
            background: linear-gradient(180deg, #3157dc, #00d4ff);
        }
        .stat-icon {
            width: 64px;
            height: 64px;
            flex: 0 0 64px;
            display: grid;
            place-items: center;
            border-radius: 22px;
            color: #ffffff;
            box-shadow:
                0 14px 26px rgba(16, 85, 245, .16),
                inset 0 1px 0 rgba(255, 255, 255, .32);
        }
        .stat-icon svg {
            width: 34px;
            height: 34px;
            stroke: currentColor;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        .stat-icon.blue {
            background: linear-gradient(145deg, #42c8ec, #3157dc 58%, #4b3db8);
        }
        .stat-icon.cyan {
            background: linear-gradient(145deg, #53e0d4, #25aee4 52%, #3157dc);
        }
        .stat-icon.green {
            background: linear-gradient(145deg, #28d17c, #1facb8 52%, #3157dc);
        }
        .stat-icon.purple {
            background: linear-gradient(145deg, #2f7bff, #4b3db8 52%, #7d16b8);
        }
        .stat-copy {
            min-width: 0;
        }
        .stat-copy span {
            display: block;
            color: #4b587c;
            font-size: clamp(12px, .9vw, 13px);
            font-weight: 800;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }
        .stat-copy strong {
            display: block;
            margin-top: 8px;
            color: #2f7bff;
            font-size: clamp(20px, 1.8vw, 26px);
            line-height: 1.04;
            letter-spacing: 0;
            overflow-wrap: anywhere;
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
            font-size: 17px;
        }
        .access-card p {
            margin: 0;
            color: #4b587c;
            font-size: 14px;
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
            font-size: 14px;
        }
        .module-row p {
            margin: 4px 0 0;
            color: #4b587c;
            text-align: left;
            font-size: 13px;
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
            font-size: 14px;
        }
        .announcement-card p {
            margin: 6px 0 0;
            color: #4b587c;
            font-size: 13px;
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
            font-size: clamp(24px, 2.8vw, 34px);
            line-height: 1.12;
        }
        .dashboard-headline p {
            max-width: 760px;
            margin: 12px 0 0;
            color: #4b587c;
            font-size: 14px;
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
        .class-access-shell {
            display: grid;
            grid-template-columns: repeat(3, minmax(240px, 1fr));
            gap: 16px;
            align-items: start;
        }
        .class-hero-card {
            position: relative;
            min-height: 100%;
            padding: 42px 18px 18px;
            border: 1px solid rgba(47, 123, 255, .14);
            border-radius: 12px;
            color: #ffffff;
            background:
                radial-gradient(circle at 88% 16%, rgba(0, 212, 255, .2), transparent 11rem),
                linear-gradient(135deg, #162282 0%, #3157dc 52%, #12b7ff 100%);
            box-shadow: 0 16px 34px rgba(16, 85, 245, .14);
            overflow: hidden;
            transition: transform .18s ease, box-shadow .18s ease;
        }
        .class-hero-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 22px 42px rgba(16, 85, 245, .2);
        }
        .class-hero-card .status-pill {
            position: absolute;
            top: 0;
            left: 0;
            min-width: 108px;
            min-height: 30px;
            border-radius: 0 0 2px 0;
            color: #07164d;
            background: #cfe7bf;
            border: 0;
            font-size: 11px;
        }
        .class-hero-card .status-pill.in-progress {
            color: #07164d;
            background: #aeb9c8;
        }
        .class-hero-card .status-pill.not-started {
            color: #07164d;
            background: #b7b7b7;
        }
        .class-hero-card h3 {
            margin: 0 0 10px;
            font-size: 15px;
            line-height: 1.2;
        }
        .class-hero-card p {
            color: rgba(255, 255, 255, .88);
            font-size: 11px;
            text-align: left;
            line-height: 1.55;
        }
        .class-hero-card .meta {
            gap: 8px;
            margin-top: 14px;
        }
        .class-hero-card .badge {
            color: rgba(255, 255, 255, .78);
            background: rgba(7, 22, 77, .18);
            border-color: rgba(255, 255, 255, .12);
            font-size: 10px;
        }
        .class-hero-card .progress-track {
            height: 7px;
            margin-top: 14px;
            background: rgba(255, 255, 255, .82);
        }
        .class-hero-card .progress-fill {
            background: linear-gradient(90deg, #42c8ec, #7d16b8);
        }
        .checkout-note {
            margin-top: 18px;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 10px;
            background: rgba(255, 255, 255, .12);
            color: rgba(255, 255, 255, .9);
            font-size: 10px;
            line-height: 1.55;
        }
        .class-card-link {
            color: inherit;
            display: block;
            height: 100%;
        }
        .lesson-panel {
            display: grid;
            gap: 14px;
        }
        .module-access-card {
            padding: 18px;
            border: 1px solid rgba(47, 123, 255, .14);
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 14px 34px rgba(16, 85, 245, .08);
        }
        .module-access-card h3 {
            margin: 8px 0 8px;
            color: #07164d;
            font-size: 17px;
        }
        .lesson-access-list {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }
        .lesson-access-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 14px;
            align-items: center;
            padding: 13px;
            border: 1px solid rgba(47, 123, 255, .12);
            border-radius: 14px;
            background: #f8fbff;
        }
        .lesson-access-row strong {
            display: block;
            color: #07164d;
            font-size: 14px;
            line-height: 1.35;
        }
        .lesson-info {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 9px;
        }
        .lesson-info span {
            padding: 6px 9px;
            border-radius: 999px;
            color: #4b587c;
            background: #ffffff;
            border: 1px solid rgba(47, 123, 255, .12);
            font-size: 12px;
            font-weight: 800;
        }
        .discussion-split {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            align-items: stretch;
        }
        .discussion-banner {
            position: relative;
            display: grid;
            grid-template-columns: 160px minmax(0, 1fr);
            align-items: center;
            min-height: 190px;
            overflow: hidden;
            border-radius: 16px;
            color: #ffffff;
            box-shadow: 0 18px 42px rgba(49, 87, 220, .16);
        }
        .discussion-banner.telegram {
            background:
                radial-gradient(circle at 12% 30%, rgba(83,224,212,.28), transparent 11rem),
                linear-gradient(125deg, #3157dc 0%, #7040ea 62%, #843ff0 100%);
        }
        .discussion-banner.discord {
            background:
                radial-gradient(circle at 12% 30%, rgba(255,255,255,.2), transparent 10rem),
                linear-gradient(125deg, #d92d8f 0%, #ec4899 52%, #8b3ee8 100%);
        }
        .discussion-visual {
            position: relative;
            height: 100%;
            display: grid;
            place-items: center;
        }
        .discussion-visual::before {
            content: "";
            position: absolute;
            width: 126px;
            height: 126px;
            border: 17px solid rgba(255,255,255,.14);
            border-radius: 38px;
            transform: rotate(12deg);
        }
        .discussion-logo {
            position: relative;
            z-index: 1;
            width: 92px;
            height: 92px;
            display: grid;
            place-items: center;
            border-radius: 28px;
            color: #ffffff;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.2);
            box-shadow: 0 18px 34px rgba(7,22,77,.18);
            backdrop-filter: blur(10px);
        }
        .discussion-logo svg { width: 58px; height: 58px; }
        .discussion-copy {
            position: relative;
            z-index: 1;
            padding: 26px 26px 26px 4px;
        }
        .discussion-copy h3 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(18px, 2vw, 24px);
            line-height: 1.18;
        }
        .discussion-copy p {
            max-width: 430px;
            margin: 9px 0 16px;
            color: rgba(255,255,255,.82);
            font-size: 12px;
            line-height: 1.55;
            text-align: left;
        }
        .discussion-join {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 13px;
            border-radius: 8px;
            color: #4b3db8;
            background: #ffffff;
            font-size: 11px;
            font-weight: 900;
            box-shadow: 0 10px 24px rgba(7,22,77,.16);
            transition: transform .18s ease, box-shadow .18s ease;
        }
        .discussion-join:hover {
            color: #4b3db8;
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(7,22,77,.22);
        }
        .discussion-join svg { width: 15px; height: 15px; }
        @media (max-width: 620px) {
            .discussion-banner {
                grid-template-columns: 112px minmax(0, 1fr);
                min-height: 170px;
            }
            .discussion-logo { width: 72px; height: 72px; border-radius: 22px; }
            .discussion-logo svg { width: 44px; height: 44px; }
            .discussion-visual::before { width: 94px; height: 94px; border-width: 13px; }
            .discussion-copy { padding: 20px 16px 20px 0; }
            .discussion-copy h3 { font-size: 17px; }
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 18px;
        }
        .product-card {
            display: flex;
            min-width: 0;
            min-height: 100%;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid rgba(47, 123, 255, .14);
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 14px 34px rgba(16, 85, 245, .1);
        }
        .product-cover {
            position: relative;
            aspect-ratio: 1.6;
            overflow: hidden;
            background:
                radial-gradient(circle at 28% 32%, rgba(0, 212, 255, .2), transparent 8rem),
                linear-gradient(135deg, #07164d 0%, #3157dc 56%, #00d4ff 100%);
        }
        .product-cover img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }
        .product-cover-placeholder {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            padding: 18px;
            color: #ffffff;
            text-align: center;
            font-weight: 900;
            font-size: 13px;
            line-height: 1.18;
        }
        .product-badge {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
            padding: 5px 9px;
            color: #ffffff;
            background: #22c55e;
            border-radius: 0 0 8px 0;
            font-size: 11px;
            font-weight: 900;
        }
        .product-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 0;
            padding: 13px;
        }
        .product-price {
            color: #22c55e;
            font-size: 12px;
            font-weight: 900;
            margin-top: 3px;
        }
        .product-title {
            color: #4b587c;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.35;
        }
        .product-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 9px;
        }
        .product-chip {
            display: inline-flex;
            align-items: center;
            min-height: 24px;
            padding: 5px 8px;
            border-radius: 999px;
            color: #3157dc;
            background: rgba(47, 123, 255, .08);
            border: 1px solid rgba(47, 123, 255, .12);
            font-size: 10px;
            font-weight: 900;
            line-height: 1;
        }
        .product-actions {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }
        .product-actions form {
            margin: 0;
        }
        .product-actions .button {
            width: 100%;
            min-height: 38px;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 11px;
            box-shadow: none;
            text-shadow: none;
        }
        .button-sales {
            background: #22c9b9;
        }
        .button-order {
            background: #8b9cf6;
        }
        .news-grid {
            display: grid;
            grid-template-columns: minmax(220px, .82fr) minmax(260px, 1.08fr) minmax(260px, 1.1fr);
            gap: 16px;
        }
        .news-card {
            position: relative;
            min-width: 0;
            overflow: hidden;
            border: 1px solid rgba(47,123,255,.13);
            border-radius: 14px;
            color: #ffffff;
            background:
                radial-gradient(circle at 86% 8%, rgba(66,200,236,.14), transparent 12rem),
                #15151c;
            box-shadow: 0 16px 36px rgba(7,22,77,.12);
        }
        .news-card.feature {
            display: flex;
            flex-direction: column;
        }
        .news-media {
            position: relative;
            aspect-ratio: 1.65;
            overflow: hidden;
            background: linear-gradient(135deg, #0d183e, #3157dc 56%, #6a1ba9);
        }
        .news-media img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }
        .news-media::after {
            content: "";
            position: absolute;
            inset: auto 0 0;
            height: 48%;
            background: linear-gradient(transparent, rgba(8,10,20,.72));
        }
        .news-type {
            position: absolute;
            z-index: 2;
            top: 12px;
            left: 12px;
            padding: 6px 9px;
            border-radius: 6px;
            color: #ffffff;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .news-body { padding: 16px; }
        .news-body h3 {
            margin: 0;
            color: #ffffff;
            font-size: 16px;
            line-height: 1.28;
        }
        .news-body p {
            margin: 9px 0 0;
            color: rgba(255,255,255,.68);
            font-size: 11px;
            line-height: 1.6;
            text-align: left;
        }
        .news-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 14px;
            color: rgba(255,255,255,.62);
            font-size: 10px;
            font-weight: 700;
        }
        .news-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .news-meta svg { width: 13px; height: 13px; }
        .news-card.brief .news-body {
            display: flex;
            min-height: 100%;
            flex-direction: column;
        }
        .news-card.brief .news-thumb {
            margin-top: auto;
            padding-top: 16px;
        }
        .news-thumb img {
            width: 100%;
            aspect-ratio: 1.7;
            display: block;
            object-fit: cover;
            border-radius: 10px;
        }
        .news-card.promo .news-media {
            aspect-ratio: 1.3;
            margin: 14px;
            border-radius: 12px;
        }
        .news-promo-label {
            display: inline-flex;
            margin: 0 14px;
            padding: 8px 12px;
            border-radius: 0 0 8px 8px;
            color: #ffffff;
            background: #d97706;
            font-size: 12px;
            font-weight: 900;
        }
        .news-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 13px;
            color: #53e0d4;
            font-size: 10px;
            font-weight: 900;
        }
        .account-dropdown {
            position: absolute;
            top: 24px;
            right: clamp(18px, 4vw, 44px);
            z-index: 45;
        }
        .account-dropdown summary {
            min-width: 184px;
            min-height: 42px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border: 1px solid rgba(47, 123, 255, .18);
            border-radius: 10px;
            color: #07164d;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(16, 85, 245, .1);
            cursor: pointer;
            font-size: 13px;
            font-weight: 800;
            list-style: none;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }
        .account-dropdown summary:hover {
            border-color: rgba(75, 61, 184, .34);
            box-shadow: 0 14px 30px rgba(75, 61, 184, .14);
            transform: translateY(-1px);
        }
        .account-mini-avatar {
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #ffffff;
            background: linear-gradient(145deg, #42c8ec, #3157dc, #7d16b8);
            font-size: 10px;
            font-weight: 900;
            overflow: hidden;
        }
        .account-mini-avatar img { width: 100%; height: 100%; display: block; object-fit: cover; }
        .account-mini-copy { min-width: 0; flex: 1; }
        .account-mini-copy strong,
        .account-mini-copy span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .account-mini-copy strong { color: #07164d; font-size: 11px; }
        .account-mini-copy span {
            position: relative;
            margin-top: 1px;
            padding-left: 10px;
            color: #73809f;
            font-size: 9px;
            font-weight: 700;
        }
        .account-mini-copy span::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 3px rgba(34,197,94,.12);
            transform: translateY(-50%);
        }
        .account-chevron {
            width: 27px;
            height: 27px;
            flex: 0 0 27px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #4b3db8;
            background: rgba(75,61,184,.08);
            transition: transform .2s ease, background .2s ease;
        }
        .account-chevron svg { width: 15px; height: 15px; }
        .account-dropdown summary::-webkit-details-marker { display: none; }
        .account-dropdown[open] .account-chevron {
            background: rgba(75,61,184,.14);
            transform: rotate(180deg);
        }
        .account-dropdown-menu {
            min-width: 210px;
            display: grid;
            gap: 4px;
            margin-top: 8px;
            padding: 9px;
            border: 1px solid rgba(47, 123, 255, .14);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 18px 42px rgba(7, 22, 77, .16);
            transform-origin: top right;
            animation: accountMenuIn .18s ease both;
        }
        @keyframes accountMenuIn {
            from { opacity: 0; transform: translateY(-6px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .account-dropdown-menu a,
        .account-dropdown-menu button {
            width: 100%;
            min-height: 38px;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border: 0;
            border-radius: 8px;
            color: #4b587c;
            background: transparent;
            cursor: pointer;
            font: inherit;
            font-size: 12px;
            font-weight: 800;
            text-align: left;
        }
        .account-dropdown-menu a:hover,
        .account-dropdown-menu button:hover {
            color: #3157dc;
            background: rgba(47, 123, 255, .08);
        }
        .account-dropdown-menu form { margin: 0; }
        .account-menu-icon {
            width: 29px;
            height: 29px;
            flex: 0 0 29px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #3157dc;
            background: #eef4ff;
        }
        .account-menu-icon svg { width: 15px; height: 15px; }
        .account-dropdown-menu button:hover .account-menu-icon,
        .account-dropdown-menu a:hover .account-menu-icon {
            color: #ffffff;
            background: linear-gradient(145deg, #42c8ec, #3157dc, #7d16b8);
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
                text-align: center;
            }
            .rocket-illustration {
                max-width: 260px;
                order: -1;
            }
            .member-hero-image {
                max-width: 260px;
                order: -1;
            }
            .hero-copy {
                max-width: none;
            }
            .member-hero p {
                margin-inline: auto;
                text-align: center;
            }
            .member-stats,
            .course-access-grid,
            .announcement-grid,
            .product-grid,
            .news-grid {
                grid-template-columns: 1fr;
            }
            .module-row {
                grid-template-columns: 1fr;
            }
            .dashboard-headline,
            .discussion-card,
            .class-access-shell,
            .discussion-split,
            .lesson-access-row {
                grid-template-columns: 1fr;
            }
            .account-dropdown { top: 14px; right: 14px; }
            .headline-icon {
                width: 58px;
                height: 58px;
                border-radius: 18px;
                font-size: 17px;
            }
        }
        @media (min-width: 981px) and (max-width: 1380px) {
            .member-stats {
                grid-template-columns: repeat(2, minmax(240px, 1fr));
            }
            .product-grid {
                grid-template-columns: repeat(2, minmax(220px, 1fr));
            }
            .news-grid {
                grid-template-columns: repeat(2, minmax(240px, 1fr));
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
                <div class="member-avatar">
                    @if($user->avatar_path)
                        <img src="{{ route('participant.avatar') }}?v={{ $user->updated_at?->timestamp }}" alt="Foto {{ $user->name }}">
                    @else
                        {{ $initials ?: 'TV' }}
                    @endif
                </div>
                <strong>{{ $user->name }}</strong>
            </div>

            <div class="sidebar-group">
                <p class="sidebar-title">Overview</p>
                <a class="sidebar-link active" href="#dashboard">
                    <span class="sidebar-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 13h6V4H4z"/><path d="M14 20h6V4h-6z"/><path d="M4 20h6v-3H4z"/></svg></span>
                    Dashboard
                </a>
                <a class="sidebar-link" href="{{ route('participant.other-courses') }}">
                    <span class="sidebar-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H20v14H7a3 3 0 0 0-3 3z"/><path d="M8 8h8"/><path d="M8 12h6"/></svg></span>
                    Other Course
                </a>
                <a class="sidebar-link" href="#rekomendasi-modul">
                    <span class="sidebar-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="12" rx="2"/><path d="m10 9 5 2-5 2z" fill="currentColor" stroke="none"/><path d="M8 21h8"/></svg></span>
                    Webinar
                </a>
                <a class="sidebar-link" href="#grup-diskusi">
                    <span class="sidebar-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 14a4 4 0 1 1 0-8"/><path d="M17 14a4 4 0 1 0 0-8"/><path d="M3 20a5 5 0 0 1 8 0"/><path d="M13 20a5 5 0 0 1 8 0"/></svg></span>
                    Group Discussion
                </a>
            </div>

            <div class="sidebar-group">
                <p class="sidebar-title">My Account</p>
                <a class="sidebar-link" href="{{ route('participant.profile') }}">
                    <span class="sidebar-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></span>
                    Profile
                </a>
                <a class="sidebar-link" href="#kelas-dipilih">
                    <span class="sidebar-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 4h11a3 3 0 0 1 3 3v13H8a3 3 0 0 1-3-3z"/><path d="M8 4v13a3 3 0 0 0 3 3"/><path d="M9 8h6"/><path d="M9 12h5"/></svg></span>
                    My Course
                </a>
                <a class="sidebar-link" href="#rekomendasi-modul">
                    <span class="sidebar-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 7h15l-2 8H8z"/><path d="M6 7 5 4H2"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/></svg></span>
                    My Order
                </a>
                <form class="sidebar-logout" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="sidebar-link" type="submit">
                        <span class="sidebar-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M14 4h4a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-4"/></svg></span>
                        Log out
                    </button>
                </form>
            </div>

            <div class="sidebar-group">
                <p class="sidebar-title">Support</p>
                <a class="sidebar-link" href="{{ $support['whatsapp'] }}" target="_blank" rel="noopener">
                    <span class="sidebar-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 20l1.4-4.2A8 8 0 1 1 8.2 18z"/><path d="M9 9.5c.4 2 2 3.6 4 4l1.4-1.4"/></svg></span>
                    WhatsApp
                </a>
                <a class="sidebar-link" href="{{ $support['email'] }}">
                    <span class="sidebar-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span>
                    Email Admin
                </a>
            </div>
        </aside>

        <main class="member-content">
            <details class="account-dropdown">
                <summary>
                    <span class="account-mini-avatar">
                        @if($user->avatar_path)
                            <img src="{{ route('participant.avatar') }}?v={{ $user->updated_at?->timestamp }}" alt="">
                        @else
                            {{ $initials ?: 'TV' }}
                        @endif
                    </span>
                    <span class="account-mini-copy"><strong>{{ $user->name }}</strong><span>Participant</span></span>
                    <span class="account-chevron" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m8 10 4 4 4-4"/></svg>
                    </span>
                </summary>
                <div class="account-dropdown-menu">
                    <a href="{{ route('participant.profile') }}">
                        <span class="account-menu-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></span>
                        Lihat Account
                    </a>
                    <a href="{{ route('participant.profile') }}#password">
                        <span class="account-menu-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></span>
                        Reset Password
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">
                            <span class="account-menu-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M14 4h4a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-4"/></svg></span>
                            Log out
                        </button>
                    </form>
                </div>
            </details>

            <section id="dashboard">
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

                <div class="member-hero">
                    @if($participantHeroImageUrl)
                        <img class="member-hero-image" src="{{ $participantHeroImageUrl }}" alt="Ilustrasi dashboard peserta">
                    @else
                        <svg class="rocket-illustration" viewBox="0 0 420 300" fill="none" aria-hidden="true">
                            <path d="M106 244c26-4 51-17 73-39l39 39c-44 11-82 18-112 0z" fill="#3157DC"/>
                            <path d="M70 248c29-10 51-23 67-40l39 39c-38 10-74 11-106 1z" fill="#FFB400"/>
                            <path d="M121 112c58-60 133-88 224-87-1 92-29 166-88 224L121 112z" fill="#FFFFFF"/>
                            <path d="M142 133c48-46 107-71 176-76-5 69-30 128-76 176L142 133z" fill="#DCEBFF"/>
                            <path d="M121 112 88 129c-30 16-48 47-49 81l-1 27 88-52-5-73z" fill="#FFFFFF"/>
                            <path d="M257 249 240 282c-16 30-47 48-81 49l-27 1 52-88 73 5z" fill="#FFFFFF"/>
                            <path d="M121 112c58-60 133-88 224-87-1 92-29 166-88 224M121 112 88 129c-30 16-48 47-49 81l-1 27 88-52M121 112l136 137M257 249 240 282c-16 30-47 48-81 49l-27 1 52-88" stroke="#3157DC" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="263" cy="112" r="28" fill="#8CCBFF" stroke="#3157DC" stroke-width="10"/>
                            <circle cx="173" cy="141" r="28" fill="#FFFFFF" stroke="#3157DC" stroke-width="8"/>
                            <circle cx="162" cy="134" r="5" fill="#07164D"/>
                            <circle cx="186" cy="134" r="5" fill="#07164D"/>
                            <path d="M158 154c9 11 24 11 34 0" stroke="#07164D" stroke-width="6" stroke-linecap="round"/>
                            <path d="M180 92v-36m-20 22 20-22 20 22" stroke="#3157DC" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @endif
                    <div class="hero-copy">
                        <h1>Selamat Datang {{ $user->name }}</h1>
                        <p>
                            Buka menu My Course untuk melihat kelas aktif, melanjutkan modul,
                            memantau progress, dan menghubungi admin saat membutuhkan bantuan.
                        </p>
                        <a class="button hero-course-button" href="{{ $ctaUrl }}">{{ $ctaLabel }}</a>
                    </div>
                </div>
            </section>

            <section class="member-section" id="data-belajar">
                <h2>Ringkasan Progress Peserta</h2>
                <div class="member-stats">
                    <div class="member-stat">
                        <span class="stat-icon purple" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M3 8.5 12 4l9 4.5-9 4.5-9-4.5z"/>
                                <path d="M7 11v4.2c0 1.4 2.2 2.8 5 2.8s5-1.4 5-2.8V11"/>
                                <path d="M20 10v5"/>
                            </svg>
                        </span>
                        <div class="stat-copy"><span>Kelas Diikuti</span><strong>{{ $enrollments->count() }} kelas</strong></div>
                    </div>
                    <div class="member-stat">
                        <span class="stat-icon cyan" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M7 4h7l4 4v12H7z"/>
                                <path d="M14 4v4h4"/>
                                <path d="M10 12h5"/>
                                <path d="M10 16h4"/>
                                <path d="M5 7v13"/>
                            </svg>
                        </span>
                        <div class="stat-copy"><span>Modul Dibaca</span><strong>{{ $completedModules }} dari {{ $modules->count() }}</strong></div>
                    </div>
                    <div class="member-stat">
                        <span class="stat-icon blue" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="5" width="18" height="13" rx="3"/>
                                <path d="m10 9 5 3-5 3z" fill="currentColor" stroke="none"/>
                                <path d="M8 21h8"/>
                            </svg>
                        </span>
                        <div class="stat-copy"><span>Video Ditonton</span><strong>{{ $watchedVideos }} dari {{ $totalVideoLessons }}</strong></div>
                    </div>
                    <div class="member-stat">
                        <span class="stat-icon green" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M4 19V5"/>
                                <path d="M4 19h16"/>
                                <path d="m7 15 4-4 3 3 5-7"/>
                                <path d="M17 7h2v2"/>
                            </svg>
                        </span>
                        <div class="stat-copy"><span>Progress</span><strong>{{ $overallProgress }}%</strong></div>
                    </div>
                </div>
            </section>

            <section class="member-section" id="kelas-dipilih">
                <h2>My Course</h2>
                @if($firstEnrollment && $firstCourse)
                    <div class="class-access-shell">
                        @foreach($enrollments as $enrollment)
                            @php
                                $activeCourse = $enrollment->course;
                                $activeLessonCount = $activeCourse->modules->sum(fn ($module) => $module->lessons->count());
                                $activeDuration = $activeCourse->modules->sum(fn ($module) => $module->duration_minutes);
                                $activeCompleted = $enrollment->progress->where('progress_percent', 100)->count();
                                $activeProgress = $activeLessonCount > 0 ? round(($activeCompleted / $activeLessonCount) * 100) : 0;
                                $statusClass = $activeProgress >= 100 ? 'done' : ($activeProgress > 0 ? 'in-progress' : 'not-started');
                                $statusLabel = $activeProgress >= 100 ? 'Selesai' : ($activeProgress > 0 ? 'Berlangsung' : 'Belum mulai');
                            @endphp
                            <a class="class-card-link" href="{{ route('lms.courses.show', $activeCourse) }}">
                                <article class="class-hero-card">
                                    <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                                    <h3>{{ $activeCourse->title }}</h3>
                                    <p>{{ $activeCourse->summary }}</p>
                                    <div class="meta">
                                        <span class="badge">{{ $activeCourse->modules->count() }} modul</span>
                                        <span class="badge">{{ $activeLessonCount }} lesson</span>
                                        <span class="badge">{{ $activeDuration }} menit</span>
                                        <span class="badge">{{ $activeProgress }}% selesai</span>
                                    </div>
                                    <div class="progress-track"><div class="progress-fill" style="width:{{ $activeProgress }}%"></div></div>
                                    <div class="checkout-note">
                                        Checkout manual: akses kelas aktif setelah pembayaran diverifikasi admin. Jika lesson belum terbuka,
                                        hubungi admin melalui menu bantuan.
                                    </div>
                                </article>
                            </a>
                        @endforeach
                    </div>
                @else
                    <article class="access-card">
                        <span class="status-pill">Belum mulai</span>
                        <h3>Belum ada kelas aktif</h3>
                        <p>Kelas akan tampil setelah pembayaran diverifikasi dan akses peserta diaktifkan.</p>
                        <div class="meta"><a class="button" href="{{ route('register') }}">Pilih Paket Kelas</a></div>
                    </article>
                @endif
            </section>

            <section class="member-section" id="rekomendasi-modul">
                <h2>Rekomendasi Modul Pembelajaran</h2>
                <div class="product-grid">
                    @forelse($latestCourses as $course)
                        @php
                            $moduleCount = $course->modules->count();
                            $lessonCount = $course->modules->sum(fn ($module) => $module->lessons->count());
                        @endphp
                        <article class="product-card">
                            <div class="product-cover">
                                <span class="product-badge">Lifetime</span>
                                @if($course->cover_image)
                                    <img src="{{ $course->cover_image }}" alt="{{ $course->title }}">
                                @else
                                    <div class="product-cover-placeholder">
                                        <span>{{ $course->title }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="product-body">
                                <div class="product-title">{{ $course->title }}</div>
                                <div class="product-price">Rp. {{ number_format($course->price, 0, ',', '.') }}</div>
                                <div class="product-chips">
                                    <span class="product-chip">{{ $moduleCount }} Modul</span>
                                    <span class="product-chip">{{ $lessonCount }} Lesson</span>
                                    <span class="product-chip">Lab Practice</span>
                                    <span class="product-chip">Quiz</span>
                                </div>
                                <div class="product-actions">
                                    <form method="POST" action="{{ route('purchase.order', $course) }}">
                                        @csrf
                                        <button class="button button-order" type="submit">Order</button>
                                    </form>
                                </div>
                            </div>
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

            <section class="member-section" id="update-news">
                <h2>Update News</h2>
                <div class="news-grid">
                    @php
                        $newsCourses = $latestCourses->take(3)->values();
                        $newsFallback = asset('images/trama-verse-logo.png');
                    @endphp

                    <article class="news-card brief">
                        <div class="news-body">
                            <span class="news-type">Update</span>
                            <h3>{{ optional($newsCourses->get(0))->title ?? 'Update Materi Cyber Security' }}</h3>
                            <p>
                                {{ optional($newsCourses->get(0))->summary
                                    ?? 'Materi, insight, dan informasi kelas terbaru dari Trama Verse untuk mendukung proses belajar peserta.' }}
                            </p>
                            <div class="news-thumb">
                                <img src="{{ optional($newsCourses->get(0))->cover_image ?: $newsFallback }}" alt="Update news Trama Verse">
                            </div>
                            <div class="news-meta">
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                    {{ optional(optional($newsCourses->get(0))->created_at)->format('d M Y') ?? now()->format('d M Y') }}
                                </span>
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                                    Trama Verse
                                </span>
                            </div>
                        </div>
                    </article>

                    <article class="news-card feature">
                        <div class="news-media">
                            <span class="news-type">Learning Update</span>
                            <img src="{{ optional($newsCourses->get(1))->cover_image ?: $newsFallback }}" alt="Update kelas dan video pembelajaran">
                        </div>
                        <div class="news-body">
                            <h3>{{ optional($newsCourses->get(1))->title ?? 'Update Video & Kelas Pembelajaran' }}</h3>
                            <p>
                                {{ optional($newsCourses->get(1))->summary
                                    ?? 'Ikuti pembaruan modul, video YouTube, webinar, dan praktik terbaru yang telah ditambahkan oleh tim pembelajaran.' }}
                            </p>
                            <div class="news-meta">
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                    {{ optional(optional($newsCourses->get(1))->created_at)->format('d M Y') ?? now()->format('d M Y') }}
                                </span>
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                                    {{ optional(optional($newsCourses->get(1))->mentor)->name ?? 'Admin LMS' }}
                                </span>
                            </div>
                            <a class="news-action" href="#kelas-dipilih">Lihat pembaruan kelas &rarr;</a>
                        </div>
                    </article>

                    <article class="news-card promo">
                        <span class="news-promo-label">Promo & Event</span>
                        <div class="news-media">
                            <img src="{{ optional($newsCourses->get(2))->cover_image ?: $newsFallback }}" alt="Promo dan event Trama Verse">
                        </div>
                        <div class="news-body">
                            <h3>{{ optional($newsCourses->get(2))->title ?? 'Promo Program & Event Terbaru' }}</h3>
                            <p>
                                {{ optional($newsCourses->get(2))->summary
                                    ?? 'Dapatkan informasi promo program, webinar, event komunitas, dan kesempatan belajar terbaru.' }}
                            </p>
                            <a class="news-action" href="#rekomendasi-modul">Lihat program tersedia &rarr;</a>
                        </div>
                    </article>
                </div>
            </section>

            <section class="member-section" id="grup-diskusi">
                <h2>Forum Belajar Peserta</h2>
                @php
                    $telegramGroup = collect($discussionGroups)->firstWhere('name', 'Telegram Community');
                    $discordGroup = collect($discussionGroups)->firstWhere('name', 'Discord Lab Room');
                @endphp
                <div class="discussion-split">
                    <article class="discussion-banner telegram">
                        <div class="discussion-visual">
                            <span class="discussion-logo" aria-hidden="true">
                                <svg viewBox="0 0 64 64" fill="none">
                                    <circle cx="32" cy="32" r="29" fill="rgba(255,255,255,.12)"/>
                                    <path d="M14 30.5 49 17c1.8-.7 3.4.7 2.8 3L46 48.5c-.4 2.1-2 2.7-3.8 1.6L31.6 42l-5.2 5c-.6.6-1.1 1.1-2.2 1.1l.8-10.8 19.7-17.8c.9-.8-.2-1.2-1.4-.4L19 34.4l-10.5-3.3c-2.3-.7-2.3-2.3.5-3.4z" fill="currentColor"/>
                                </svg>
                            </span>
                        </div>
                        <div class="discussion-copy">
                            <h3>Gabung Group Telegram Trama Verse</h3>
                            <p>{{ $telegramGroup['description'] ?? 'Diskusi, berbagi insight, dan bangun relasi dengan sesama peserta Trama Verse.' }}</p>
                            <a class="discussion-join" href="{{ $telegramGroup['url'] ?? '#' }}" target="_blank" rel="noopener">
                                Join Group Sekarang
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        </div>
                    </article>

                    <article class="discussion-banner discord">
                        <div class="discussion-visual">
                            <span class="discussion-logo" aria-hidden="true">
                                <svg viewBox="0 0 64 64" fill="none">
                                    <path d="M20 18c8-4 16-4 24 0 6 8 8 17 7 26-5 5-10 7-15 8l-2.3-3.2c2.3-.7 4.5-1.8 6.4-3.1-7.1 3.3-14.7 3.3-22.1 0 1.9 1.3 4.1 2.4 6.4 3.1L22 52c-5-1-10-3-15-8-1-9 1-18 7-26l6 0z" fill="currentColor"/>
                                    <circle cx="24" cy="34" r="4" fill="#d92d8f"/>
                                    <circle cx="40" cy="34" r="4" fill="#d92d8f"/>
                                </svg>
                            </span>
                        </div>
                        <div class="discussion-copy">
                            <h3>Gabung Forum Diskusi Discord</h3>
                            <p>{{ $discordGroup['description'] ?? 'Tanyakan kendala praktik, troubleshooting tools, dan review workflow bersama komunitas.' }}</p>
                            <a class="discussion-join" href="{{ $discordGroup['url'] ?? '#' }}" target="_blank" rel="noopener">
                                Gabung Forum
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        </div>
                    </article>
                </div>
            </section>

            @include('partials.footer', ['footerMode' => 'participant'])
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
