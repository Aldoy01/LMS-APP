<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Techverse Learning LMS' }}</title>
    <style>
        :root {
            --ink: #17172f;
            --muted: #a9b6d3;
            --line: rgba(83, 232, 212, .24);
            --bg: #f8fbff;
            --panel: #0d1024;
            --night: #100b3f;
            --night-soft: #20105f;
            --brand: #8921C2;
            --brand-dark: #531079;
            --brand-soft: rgba(137, 33, 194, .22);
            --accent: #FE39A4;
            --accent-soft: rgba(254, 57, 164, .16);
            --gold: #FFFDBB;
            --teal: #53E8D4;
            --teal-soft: rgba(83, 232, 212, .14);
            --cyan: #25C4F8;
            --cyan-soft: rgba(37, 196, 248, .14);
            --danger: #e21b5b;
            --hero-copy: #f5f7ff;
            --card-ink: #f7f9ff;
            --card-muted: #a9b6d3;
            --card-border: rgba(83, 232, 212, .28);
            --card-gradient: linear-gradient(145deg, rgba(6, 8, 20, .96), rgba(16, 11, 63, .92) 48%, rgba(30, 9, 45, .94));
        }
        * { box-sizing: border-box; }
        body {
            position: relative;
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(137, 33, 194, .13), transparent 34rem),
                radial-gradient(circle at top right, rgba(37, 196, 248, .16), transparent 32rem),
                radial-gradient(circle at 58% 18%, rgba(83, 232, 212, .16), transparent 28rem),
                linear-gradient(180deg, #ffffff 0%, #f8fbff 46%, #eefbff 100%);
            background-size: auto, auto, auto, auto;
            background-attachment: fixed;
            animation: cyberBackground 18s ease-in-out infinite alternate;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 8% 18%, rgba(83, 232, 212, .16), transparent 13rem),
                radial-gradient(circle at 88% 34%, rgba(137, 33, 194, .12), transparent 15rem),
                radial-gradient(circle at 72% 82%, rgba(254, 57, 164, .1), transparent 14rem);
            filter: blur(8px);
            opacity: .68;
            animation: circuitSweep 12s linear infinite;
        }
        body::after {
            content: "";
            position: fixed;
            inset: -20%;
            pointer-events: none;
            background:
                radial-gradient(circle at 20% 30%, rgba(137, 33, 194, .12), transparent 18rem),
                radial-gradient(circle at 78% 22%, rgba(37, 196, 248, .16), transparent 16rem),
                radial-gradient(circle at 62% 78%, rgba(83, 232, 212, .14), transparent 17rem);
            filter: blur(10px);
            opacity: .72;
            animation: glowDrift 16s ease-in-out infinite alternate;
        }
        a { color: inherit; text-decoration: none; }
        .shell { position: relative; min-height: 100vh; }
        .topbar {
            position: sticky;
            top: 0;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 18px clamp(18px, 4vw, 48px);
            background:
                linear-gradient(135deg, rgba(5, 6, 17, .96), rgba(16, 11, 63, .9) 58%, rgba(33, 6, 42, .94));
            border-bottom: 1px solid rgba(83, 232, 212, .28);
            box-shadow: 0 16px 38px rgba(0, 0, 0, .42), inset 0 -1px 0 rgba(254, 57, 164, .16);
            backdrop-filter: blur(16px);
        }
        .brand { display: flex; align-items: center; gap: 12px; font-weight: 800; }
        .brand-logo {
            width: 220px;
            height: 62px;
            display: block;
            object-fit: contain;
            object-position: left center;
        }
        .nav { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; color: #d8e4ff; font-size: 14px; }
        .nav a, .nav .link-button {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid rgba(83, 232, 212, .18);
            background: linear-gradient(135deg, rgba(255, 255, 255, .04), rgba(37, 196, 248, .05));
        }
        .nav a:hover, .nav .link-button:hover {
            color: #fff;
            background: linear-gradient(135deg, rgba(137, 33, 194, .46), rgba(37, 196, 248, .22));
            border-color: rgba(83, 232, 212, .48);
            box-shadow: 0 0 22px rgba(83, 232, 212, .14);
        }
        .icon-link::before {
            content: "";
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background: linear-gradient(135deg, var(--brand), var(--cyan));
            mask: var(--icon) center / contain no-repeat;
            -webkit-mask: var(--icon) center / contain no-repeat;
        }
        .icon-home { --icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m3 10 9-7 9 7'/%3E%3Cpath d='M5 10v10h14V10'/%3E%3Cpath d='M9 20v-6h6v6'/%3E%3C/svg%3E"); }
        .icon-book { --icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 19.5A2.5 2.5 0 0 1 6.5 17H20'/%3E%3Cpath d='M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z'/%3E%3C/svg%3E"); }
        .icon-info { --icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cpath d='M12 16v-4'/%3E%3Cpath d='M12 8h.01'/%3E%3C/svg%3E"); }
        .icon-help { --icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z'/%3E%3Cpath d='M9.5 9a2.5 2.5 0 0 1 5 0c0 2-2.5 2-2.5 4'/%3E%3Cpath d='M12 17h.01'/%3E%3C/svg%3E"); }
        .icon-dashboard { --icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='3' width='7' height='9' rx='1'/%3E%3Crect x='14' y='3' width='7' height='5' rx='1'/%3E%3Crect x='14' y='12' width='7' height='9' rx='1'/%3E%3Crect x='3' y='16' width='7' height='5' rx='1'/%3E%3C/svg%3E"); }
        .icon-shield { --icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/%3E%3Cpath d='m9 12 2 2 4-5'/%3E%3C/svg%3E"); }
        .icon-user { --icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 21a8 8 0 0 0-16 0'/%3E%3Ccircle cx='12' cy='7' r='4'/%3E%3C/svg%3E"); }
        .icon-card { --icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='2' y='5' width='20' height='14' rx='2'/%3E%3Cpath d='M2 10h20'/%3E%3C/svg%3E"); }
        .icon-login { --icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4'/%3E%3Cpath d='m10 17 5-5-5-5'/%3E%3Cpath d='M15 12H3'/%3E%3C/svg%3E"); }
        .main { width: min(1180px, calc(100% - 32px)); margin: 0 auto; padding: 30px 0 54px; }
        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(300px, .8fr);
            gap: 28px;
            align-items: stretch;
            padding: clamp(26px, 5vw, 46px);
            color: #fff;
            background:
                linear-gradient(rgba(83, 232, 212, .08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 253, 187, .08) 1px, transparent 1px),
                radial-gradient(circle at 78% 22%, rgba(254, 57, 164, .56), transparent 20rem),
                radial-gradient(circle at 25% 72%, rgba(83, 232, 212, .36), transparent 18rem),
                linear-gradient(125deg, rgba(16, 11, 63, .98), rgba(83, 16, 121, .9) 45%, rgba(37, 196, 248, .82)),
                url('https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1800&q=80');
            background-size: 36px 36px, 36px 36px, auto, auto, auto, cover;
            background-position: center;
            border-bottom: 5px solid var(--teal);
            box-shadow: 0 28px 70px rgba(16, 11, 63, .24);
        }
        .hero h1 { margin: 0; font-size: clamp(34px, 6vw, 64px); line-height: 1; letter-spacing: 0; }
        .hero p { max-width: 720px; margin: 18px 0 0; color: var(--hero-copy); font-size: 17px; line-height: 1.7; }
        .hero-panel {
            align-self: end;
            background: linear-gradient(145deg, rgba(6, 8, 20, .78), rgba(16, 11, 63, .66));
            border: 1px solid rgba(83, 232, 212, .36);
            border-radius: 8px;
            padding: 18px;
            box-shadow: inset 0 0 0 1px rgba(255, 253, 187, .08);
        }
        .hero-logo {
            display: block;
            max-width: 380px;
            width: 100%;
            height: auto;
            margin-bottom: 16px;
            border-radius: 8px;
            background: #fff;
            padding: 12px;
        }
        .hero-panel strong { display: block; margin-bottom: 10px; }
        .chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 18px; }
        .chip { padding: 7px 10px; border: 1px solid rgba(83, 232, 212, .55); border-radius: 999px; font-size: 13px; background: rgba(37, 196, 248, .12); }
        .grid { display: grid; gap: 18px; }
        .metrics { grid-template-columns: repeat(4, minmax(0, 1fr)); margin-top: 24px; }
        .metric, .card {
            position: relative;
            overflow: hidden;
            color: var(--card-ink);
            background:
                radial-gradient(circle at top right, rgba(37, 196, 248, .16), transparent 13rem),
                radial-gradient(circle at bottom left, rgba(254, 57, 164, .13), transparent 12rem),
                var(--card-gradient);
            border: 1px solid var(--card-border);
            border-radius: 8px;
            padding: 18px;
            box-shadow: 0 18px 42px rgba(0, 0, 0, .34), inset 0 1px 0 rgba(255, 255, 255, .06);
        }
        .metric::before, .card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, var(--cyan), var(--accent), var(--brand));
        }
        .metric::after {
            content: "";
            position: absolute;
            top: 16px;
            right: 16px;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background:
                linear-gradient(135deg, rgba(137, 33, 194, .13), rgba(37, 196, 248, .18));
            border: 1px solid rgba(83, 232, 212, .35);
        }
        .metric span, .eyebrow { color: var(--teal); font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .metric strong { display: block; margin-top: 8px; font-size: 28px; }
        .section { margin-top: 30px; }
        .section-head { display: flex; justify-content: space-between; gap: 16px; align-items: end; margin-bottom: 14px; }
        .section h2 { margin: 0; font-size: 24px; }
        .courses { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .card h3 { margin: 8px 0 8px; font-size: 21px; }
        .card p { color: var(--card-muted); line-height: 1.6; }
        .meta { display: flex; flex-wrap: wrap; gap: 8px; margin: 14px 0; }
        .badge { padding: 6px 9px; border-radius: 6px; background: linear-gradient(135deg, rgba(137, 33, 194, .34), rgba(37, 196, 248, .2)); color: #f7f9ff; border: 1px solid rgba(83, 232, 212, .28); font-size: 13px; font-weight: 700; }
        .button {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 10px 14px;
            border-radius: 6px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.28), rgba(255,255,255,0) 42%),
                linear-gradient(135deg, var(--brand), var(--accent));
            color: #fff;
            font-weight: 800;
            border: 1px solid rgba(255, 255, 255, .28);
            cursor: pointer;
            text-shadow: 0 1px 1px rgba(0,0,0,.34);
            box-shadow:
                0 6px 0 rgba(83, 16, 121, .78),
                0 14px 26px rgba(137, 33, 194, .28),
                inset 0 1px 0 rgba(255, 255, 255, .38),
                inset 0 -1px 0 rgba(0, 0, 0, .22);
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }
        .button:hover {
            transform: translateY(-2px);
            filter: saturate(1.12);
            box-shadow:
                0 8px 0 rgba(83, 16, 121, .78),
                0 18px 32px rgba(137, 33, 194, .34),
                inset 0 1px 0 rgba(255, 255, 255, .42),
                inset 0 -1px 0 rgba(0, 0, 0, .2);
        }
        .button:active {
            transform: translateY(3px);
            box-shadow:
                0 2px 0 rgba(83, 16, 121, .82),
                0 8px 18px rgba(137, 33, 194, .24),
                inset 0 2px 8px rgba(0, 0, 0, .24);
        }
        .link-button {
            border: 0;
            background: transparent;
            color: #d8e4ff;
            font: inherit;
            padding: 8px 10px;
            border-radius: 6px;
            cursor: pointer;
        }
        .link-button:hover { color: #fff; background: rgba(37, 196, 248, .12); }
        .split { grid-template-columns: 1fr 1fr; }
        .list { display: grid; gap: 12px; }
        .list-row {
            padding: 14px;
            border: 1px solid var(--card-border);
            border-radius: 8px;
            color: var(--card-ink);
            background:
                radial-gradient(circle at top right, rgba(83, 232, 212, .11), transparent 10rem),
                linear-gradient(145deg, rgba(6, 8, 20, .96), rgba(14, 16, 38, .95) 52%, rgba(16, 11, 63, .9));
            box-shadow: 0 12px 30px rgba(0, 0, 0, .26);
        }
        .list-row strong { display: block; margin-bottom: 5px; }
        .muted { color: var(--card-muted); }
        .pipeline { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .stage { border-left: 4px solid var(--cyan); }
        .risk-high { color: var(--accent); font-weight: 800; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .form-grid label { display: grid; gap: 7px; color: var(--card-muted); font-size: 14px; font-weight: 700; }
        .form-grid .wide { grid-column: 1 / -1; }
        input, select, textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 11px 12px;
            color: var(--card-ink);
            background: rgba(5, 6, 17, .72);
            font: inherit;
        }
        input:focus, select:focus, textarea:focus {
            outline: 3px solid rgba(83, 232, 212, .32);
            border-color: var(--cyan);
        }
        textarea { resize: vertical; }
        small { color: var(--accent); }
        footer { border-top: 1px solid var(--line); padding: 24px clamp(18px, 4vw, 48px); color: var(--hero-copy); background: linear-gradient(135deg, var(--night), var(--brand-dark)); }
        @keyframes cyberBackground {
            0% { background-position: left top, right top, 58% 18%, center; }
            100% { background-position: 8% 4%, 92% 8%, 55% 22%, center; }
        }
        @keyframes circuitSweep {
            0% { transform: translate3d(-18px, -12px, 0); opacity: .52; }
            50% { opacity: .78; }
            100% { transform: translate3d(22px, 18px, 0); opacity: .58; }
        }
        @keyframes glowDrift {
            0% { transform: translate3d(-2%, -1%, 0) scale(1); }
            100% { transform: translate3d(2%, 1%, 0) scale(1.04); }
        }
        @media (prefers-reduced-motion: reduce) {
            body, body::before, body::after { animation: none; }
            .button { transition: none; }
        }
        @media (max-width: 820px) {
            .hero, .metrics, .courses, .split, .pipeline, .form-grid { grid-template-columns: 1fr; }
            .topbar { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="shell">
    <header class="topbar">
        <a href="{{ route('lms.dashboard') }}" class="brand">
            <img class="brand-logo" src="{{ asset('images/techverse-learning-logo.jpeg') }}" alt="Techverse Learning">
        </a>
        <nav class="nav">
            <a class="icon-link icon-home" href="{{ route('lms.dashboard') }}">Home</a>
            <a class="icon-link icon-book" href="{{ route('lms.dashboard') }}#program">Program</a>
            <a class="icon-link icon-info" href="{{ route('lms.dashboard') }}#tentang">Tentang</a>
            <a class="icon-link icon-help" href="{{ route('lms.dashboard') }}#kontak">Kontak</a>
            @auth
                <a class="icon-link icon-dashboard" href="{{ route('participant.home') }}">Beranda Belajar</a>
                <a class="icon-link icon-book" href="{{ route('participant.home') }}#modul">Modul</a>
                <a class="icon-link icon-user" href="{{ route('participant.home') }}#profil">Profil</a>
                <a class="icon-link icon-help" href="{{ route('participant.home') }}#bantuan">Bantuan</a>
            @endauth
            @auth
                @if(in_array(optional(auth()->user()->role)->name, ['super-admin', 'admin-lms'], true))
                    <a class="icon-link icon-shield" href="{{ route('admin.courses.index') }}">Admin Course</a>
                    <a class="icon-link icon-user" href="{{ route('admin.users.index') }}">User & Akses</a>
                    <a class="icon-link icon-card" href="{{ route('admin.payments.index') }}">Pembayaran</a>
                @endif
                <a class="icon-link icon-dashboard" href="{{ route('participant.dashboard') }}">Kelas Saya</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="link-button icon-link icon-login" type="submit">Logout</button>
                </form>
            @else
                <a class="icon-link icon-login" href="{{ route('login') }}">Login Peserta</a>
                <a class="icon-link icon-shield" href="{{ route('admin.login') }}">Login Admin</a>
            @endauth
        </nav>
    </header>

    @yield('content')
</div>
<footer>
    Techverse Learning LMS berbasis Laravel dan PostgreSQL. Dibangun dari blueprint implementasi Mei 2026.
</footer>
</body>
</html>
