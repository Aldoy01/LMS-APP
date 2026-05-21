<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Techverse Learning LMS' }}</title>
    <style>
        :root {
            --ink: #17172f;
            --muted: #68728c;
            --line: #dcd8ff;
            --bg: #f7f6ff;
            --panel: #ffffff;
            --night: #100b3f;
            --night-soft: #20105f;
            --brand: #8921C2;
            --brand-dark: #531079;
            --brand-soft: #f4e8ff;
            --accent: #FE39A4;
            --accent-soft: #ffe8f6;
            --gold: #FFFDBB;
            --teal: #53E8D4;
            --teal-soft: #e8fffb;
            --cyan: #25C4F8;
            --cyan-soft: #e6f8ff;
            --danger: #e21b5b;
            --hero-copy: #f5f7ff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(137, 33, 194, .14), transparent 32rem),
                radial-gradient(circle at top right, rgba(37, 196, 248, .18), transparent 30rem),
                linear-gradient(180deg, #fbfaff 0%, var(--bg) 42%, #f2fdff 100%);
        }
        a { color: inherit; text-decoration: none; }
        .shell { min-height: 100vh; }
        .topbar {
            position: sticky;
            top: 0;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 18px clamp(18px, 4vw, 48px);
            background: rgba(255, 255, 255, .9);
            border-bottom: 1px solid var(--line);
            box-shadow: 0 12px 34px rgba(16, 11, 63, .08);
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
        .nav { display: flex; gap: 10px; flex-wrap: wrap; color: var(--muted); font-size: 14px; }
        .nav a { padding: 8px 10px; border-radius: 6px; }
        .nav a:hover { color: var(--brand-dark); background: linear-gradient(135deg, var(--brand-soft), var(--cyan-soft)); }
        .main { width: min(1180px, calc(100% - 32px)); margin: 0 auto; padding: 30px 0 54px; }
        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(300px, .8fr);
            gap: 28px;
            align-items: stretch;
            padding: clamp(26px, 5vw, 46px);
            color: #fff;
            background:
                radial-gradient(circle at 78% 22%, rgba(254, 57, 164, .56), transparent 20rem),
                radial-gradient(circle at 25% 72%, rgba(83, 232, 212, .36), transparent 18rem),
                linear-gradient(125deg, rgba(16, 11, 63, .98), rgba(83, 16, 121, .9) 45%, rgba(37, 196, 248, .82)),
                url('https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1800&q=80');
            background-size: cover;
            background-position: center;
            border-bottom: 5px solid var(--teal);
            box-shadow: 0 28px 70px rgba(16, 11, 63, .24);
        }
        .hero h1 { margin: 0; font-size: clamp(34px, 6vw, 64px); line-height: 1; letter-spacing: 0; }
        .hero p { max-width: 720px; margin: 18px 0 0; color: var(--hero-copy); font-size: 17px; line-height: 1.7; }
        .hero-panel {
            align-self: end;
            background: rgba(16, 11, 63, .42);
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
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 18px;
            box-shadow: 0 14px 34px rgba(16, 11, 63, .07);
        }
        .metric span, .eyebrow { color: var(--brand); font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .metric strong { display: block; margin-top: 8px; font-size: 28px; }
        .section { margin-top: 30px; }
        .section-head { display: flex; justify-content: space-between; gap: 16px; align-items: end; margin-bottom: 14px; }
        .section h2 { margin: 0; font-size: 24px; }
        .courses { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .card h3 { margin: 8px 0 8px; font-size: 21px; }
        .card p { color: var(--muted); line-height: 1.6; }
        .meta { display: flex; flex-wrap: wrap; gap: 8px; margin: 14px 0; }
        .badge { padding: 6px 9px; border-radius: 6px; background: linear-gradient(135deg, var(--brand-soft), var(--cyan-soft)); color: var(--brand-dark); font-size: 13px; font-weight: 700; }
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 10px 14px;
            border-radius: 6px;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            color: #fff;
            font-weight: 800;
            border: 0;
            cursor: pointer;
            box-shadow: 0 10px 22px rgba(137, 33, 194, .24);
        }
        .link-button {
            border: 0;
            background: transparent;
            color: var(--muted);
            font: inherit;
            padding: 8px 10px;
            border-radius: 6px;
            cursor: pointer;
        }
        .link-button:hover { color: var(--brand-dark); background: var(--brand-soft); }
        .split { grid-template-columns: 1fr 1fr; }
        .list { display: grid; gap: 12px; }
        .list-row { padding: 14px; border: 1px solid var(--line); border-radius: 8px; background: #fff; box-shadow: 0 8px 22px rgba(16, 11, 63, .04); }
        .list-row strong { display: block; margin-bottom: 5px; }
        .muted { color: var(--muted); }
        .pipeline { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .stage { border-left: 4px solid var(--cyan); }
        .risk-high { color: var(--accent); font-weight: 800; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .form-grid label { display: grid; gap: 7px; color: var(--muted); font-size: 14px; font-weight: 700; }
        .form-grid .wide { grid-column: 1 / -1; }
        input, select, textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 11px 12px;
            color: var(--ink);
            background: #fff;
            font: inherit;
        }
        input:focus, select:focus, textarea:focus {
            outline: 3px solid rgba(83, 232, 212, .32);
            border-color: var(--cyan);
        }
        textarea { resize: vertical; }
        small { color: var(--accent); }
        footer { border-top: 1px solid var(--line); padding: 24px clamp(18px, 4vw, 48px); color: var(--hero-copy); background: linear-gradient(135deg, var(--night), var(--brand-dark)); }
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
            <a href="{{ route('lms.dashboard') }}">Home</a>
            <a href="{{ route('lms.dashboard') }}#program">Program</a>
            <a href="{{ route('lms.dashboard') }}#tentang">Tentang</a>
            <a href="{{ route('lms.dashboard') }}#kontak">Kontak</a>
            @auth
                <a href="{{ route('participant.home') }}">Beranda Belajar</a>
                <a href="{{ route('participant.home') }}#modul">Modul</a>
                <a href="{{ route('participant.home') }}#profil">Profil</a>
                <a href="{{ route('participant.home') }}#bantuan">Bantuan</a>
            @endauth
            @auth
                @if(in_array(optional(auth()->user()->role)->name, ['super-admin', 'admin-lms'], true))
                    <a href="{{ route('admin.courses.index') }}">Admin Course</a>
                    <a href="{{ route('admin.users.index') }}">User & Akses</a>
                    <a href="{{ route('admin.payments.index') }}">Pembayaran</a>
                @endif
                <a href="{{ route('participant.dashboard') }}">Kelas Saya</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="link-button" type="submit">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login Peserta</a>
                <a href="{{ route('admin.login') }}">Login Admin</a>
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
