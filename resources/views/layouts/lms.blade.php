<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Techverse Learning LMS' }}</title>
    <style>
        :root {
            --ink: #07164d;
            --muted: #4b587c;
            --line: rgba(47, 123, 255, .16);
            --bg: #ffffff;
            --panel: #ffffff;
            --night: #1e3a8a;
            --night-soft: #dbeafe;
            --brand: #2f7bff;
            --brand-dark: #3157dc;
            --brand-soft: rgba(47, 123, 255, .08);
            --accent: #00d4ff;
            --accent-soft: rgba(0, 212, 255, .1);
            --gold: #f59e0b;
            --teal: #22c55e;
            --teal-soft: rgba(34, 197, 94, .1);
            --cyan: #60a5fa;
            --cyan-soft: rgba(96, 165, 250, .12);
            --danger: #f43f5e;
            --hero-copy: #4b587c;
            --card-ink: #07164d;
            --card-muted: #4b587c;
            --card-border: rgba(47, 123, 255, .16);
            --card-gradient: #ffffff;
            --shadow: 0 16px 40px rgba(16, 85, 245, .12);
        }
        * { box-sizing: border-box; }
        html {
            -webkit-text-size-adjust: 100%;
            scroll-behavior: smooth;
        }
        body {
            position: relative;
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(47, 123, 255, .1), transparent 34rem),
                radial-gradient(circle at top right, rgba(0, 212, 255, .14), transparent 32rem),
                linear-gradient(180deg, #ffffff 0%, #f6f9ff 46%, #ffffff 100%);
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
                radial-gradient(circle at 8% 18%, rgba(96, 165, 250, .12), transparent 13rem),
                radial-gradient(circle at 88% 34%, rgba(47, 123, 255, .1), transparent 15rem),
                radial-gradient(circle at 72% 82%, rgba(34, 197, 94, .08), transparent 14rem);
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
                radial-gradient(circle at 20% 30%, rgba(49, 87, 220, .08), transparent 18rem),
                radial-gradient(circle at 78% 22%, rgba(0, 212, 255, .14), transparent 16rem),
                radial-gradient(circle at 62% 78%, rgba(34, 197, 94, .08), transparent 17rem);
            filter: blur(10px);
            opacity: .72;
            animation: glowDrift 16s ease-in-out infinite alternate;
        }
        a { color: inherit; text-decoration: none; }
        img, video, iframe {
            max-width: 100%;
        }
        .shell { position: relative; min-height: 100vh; }
        .topbar {
            position: sticky;
            top: 0;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            min-height: 92px;
            padding: 14px clamp(18px, 4vw, 58px);
            background: rgba(255, 255, 255, .96);
            border-bottom: 1px solid var(--line);
            box-shadow: 0 10px 28px rgba(16, 85, 245, .04);
            backdrop-filter: blur(16px);
        }
        .brand { display: flex; align-items: center; gap: 12px; font-weight: 800; }
        .brand-logo {
            width: 218px;
            height: 64px;
            display: block;
            object-fit: contain;
            object-position: left center;
        }
        .nav { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; color: var(--muted); font-size: 14px; }
        .nav-left {
            flex: 1;
            justify-content: flex-start;
            gap: clamp(16px, 3vw, 34px);
            color: #111827;
            font-size: 18px;
            font-weight: 900;
        }
        .nav-actions {
            justify-content: flex-end;
            gap: 16px;
            font-size: 18px;
            font-weight: 900;
        }
        .nav a, .nav .link-button {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 44px;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid transparent;
            background: transparent;
        }
        .nav a:hover, .nav .link-button:hover {
            color: var(--brand);
            background: var(--brand-soft);
            border-color: rgba(47, 123, 255, .16);
            box-shadow: 0 10px 24px rgba(16, 85, 245, .08);
        }
        .nav-action-button {
            min-width: 138px;
            min-height: 58px;
            justify-content: center;
            border-radius: 7px !important;
            border-color: var(--brand-dark) !important;
            color: var(--brand-dark);
            background: #ffffff !important;
        }
        .nav-action-button.primary {
            color: #ffffff;
            background: var(--brand-dark) !important;
            box-shadow: 0 12px 26px rgba(49, 87, 220, .18);
        }
        .nav-action-button:hover {
            background: var(--brand-soft) !important;
        }
        .nav-action-button.primary:hover {
            color: #ffffff;
            background: #1f46bf !important;
        }
        .icon-link::before {
            content: "";
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background: linear-gradient(135deg, var(--brand), var(--accent));
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
            color: var(--ink);
            background:
                linear-gradient(90deg, rgba(255,255,255,1) 0%, rgba(255,255,255,.8) 34%, rgba(255,255,255,.34) 58%, rgba(255,255,255,.08) 100%),
                url('{{ asset('images/techverse-hero-bg.webp') }}');
            background-size: cover;
            background-position: center;
            border-bottom: 1px solid rgba(47, 123, 255, .12);
            box-shadow: 0 16px 40px rgba(16, 85, 245, .1);
        }
        .hero h1 { margin: 0; font-size: clamp(34px, 6vw, 64px); line-height: 1; letter-spacing: 0; }
        .hero p { max-width: 720px; margin: 18px 0 0; color: var(--muted); font-size: 17px; line-height: 1.7; }
        .hero-panel {
            align-self: end;
            background: rgba(255, 255, 255, .88);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px;
            box-shadow: var(--shadow);
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
        .chip { padding: 8px 12px; border: 1px solid var(--line); border-radius: 999px; font-size: 13px; color: var(--muted); background: var(--brand-soft); font-weight: 700; }
        .grid { display: grid; gap: 18px; }
        .metrics { grid-template-columns: repeat(4, minmax(0, 1fr)); margin-top: 24px; }
        .metric, .card {
            position: relative;
            overflow: hidden;
            color: var(--card-ink);
            background: var(--card-gradient);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            padding: 18px;
            box-shadow: var(--shadow);
        }
        .metric::before, .card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            width: 0;
            background: transparent;
        }
        .metric::after {
            content: "";
            position: absolute;
            top: 16px;
            right: 16px;
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background-image:
                var(--metric-icon),
                linear-gradient(145deg, rgba(47, 123, 255, .1), rgba(0, 212, 255, .08));
            background-position: center, center;
            background-repeat: no-repeat;
            background-size: 30px 30px, auto;
            border: 1px solid rgba(47, 123, 255, .22);
            box-shadow: inset 0 0 24px rgba(47, 123, 255, .14);
        }
        .metric.course-icon { --metric-icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233157dc' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 19.5A2.5 2.5 0 0 1 6.5 17H20'/%3E%3Cpath d='M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z'/%3E%3Cpath d='M8 7h8'/%3E%3Cpath d='M8 11h6'/%3E%3C/svg%3E"); }
        .metric.users-icon { --metric-icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23f59e0b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2'/%3E%3Ccircle cx='9' cy='7' r='4'/%3E%3Cpath d='M22 21v-2a4 4 0 0 0-3-3.87'/%3E%3Cpath d='M16 3.13a4 4 0 0 1 0 7.75'/%3E%3C/svg%3E"); }
        .metric.format-icon { --metric-icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232f7bff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='12' rx='2'/%3E%3Cpath d='M8 20h8'/%3E%3Cpath d='M12 16v4'/%3E%3Cpath d='m10 8 5 3-5 3V8z'/%3E%3C/svg%3E"); }
        .metric.help-icon { --metric-icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23f43f5e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 14v-3a8 8 0 0 1 16 0v3'/%3E%3Cpath d='M18 19c0 1.1-.9 2-2 2h-3'/%3E%3Cpath d='M4 14a2 2 0 0 1 2-2h1v6H6a2 2 0 0 1-2-2v-2z'/%3E%3Cpath d='M20 14a2 2 0 0 0-2-2h-1v6h1a2 2 0 0 0 2-2v-2z'/%3E%3C/svg%3E"); }
        .metric.module-icon { --metric-icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233157dc' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='3' width='7' height='7' rx='1'/%3E%3Crect x='14' y='3' width='7' height='7' rx='1'/%3E%3Crect x='14' y='14' width='7' height='7' rx='1'/%3E%3Crect x='3' y='14' width='7' height='7' rx='1'/%3E%3C/svg%3E"); }
        .metric.progress-icon { --metric-icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23f59e0b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 3v18h18'/%3E%3Cpath d='m7 14 4-4 3 3 5-6'/%3E%3Cpath d='M18 7h1v1'/%3E%3C/svg%3E"); }
        .metric.category-icon { --metric-icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232f7bff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m12 2 9 5-9 5-9-5 9-5z'/%3E%3Cpath d='m3 12 9 5 9-5'/%3E%3Cpath d='m3 17 9 5 9-5'/%3E%3C/svg%3E"); }
        .metric span, .eyebrow { color: var(--brand-dark); font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .metric strong { display: block; margin-top: 8px; font-size: 28px; }
        .section { margin-top: 30px; }
        .section-head { display: flex; justify-content: space-between; gap: 16px; align-items: end; margin-bottom: 14px; }
        .section h2 { margin: 0; font-size: 24px; }
        .courses { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .card h3 { margin: 8px 0 8px; font-size: 21px; }
        .card p { color: var(--card-muted); line-height: 1.6; }
        .meta { display: flex; flex-wrap: wrap; gap: 8px; margin: 14px 0; }
        .badge { padding: 8px 12px; border-radius: 999px; background: var(--brand-soft); color: var(--muted); border: 1px solid var(--line); font-size: 13px; font-weight: 700; }
        .button {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 11px 16px;
            border-radius: 6px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.28), rgba(255,255,255,0) 42%),
                linear-gradient(90deg, var(--brand), var(--accent));
            color: #fff;
            font-weight: 800;
            border: 1px solid rgba(255, 255, 255, .28);
            cursor: pointer;
            text-shadow: 0 1px 1px rgba(0,0,0,.34);
            box-shadow:
                0 6px 0 rgba(16, 85, 245, .18),
                0 14px 26px rgba(16, 85, 245, .18),
                inset 0 1px 0 rgba(255, 255, 255, .38),
                inset 0 -1px 0 rgba(0, 0, 0, .22);
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }
        .button:hover {
            transform: translateY(-2px);
            filter: saturate(1.12);
            box-shadow:
                0 8px 0 rgba(83, 16, 121, .78),
                0 18px 32px rgba(16, 85, 245, .22),
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
            color: var(--muted);
            font: inherit;
            padding: 8px 10px;
            border-radius: 6px;
            cursor: pointer;
        }
        .link-button:hover { color: var(--brand); background: var(--brand-soft); }
        .split { grid-template-columns: 1fr 1fr; }
        .list { display: grid; gap: 12px; }
        .list-row {
            padding: 14px;
            border: 1px solid var(--card-border);
            border-radius: 18px;
            color: var(--card-ink);
            background: #ffffff;
            box-shadow: 0 10px 26px rgba(16, 85, 245, .07);
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
            border-radius: 14px;
            min-height: 44px;
            padding: 11px 12px;
            color: var(--ink);
            background: #ffffff;
            font: inherit;
        }
        input:focus, select:focus, textarea:focus {
            outline: 3px solid rgba(83, 232, 212, .32);
            border-color: var(--cyan);
        }
        textarea { resize: vertical; }
        small { color: var(--accent); }
        footer { border-top: 1px solid var(--line); padding: 24px clamp(18px, 4vw, 48px); color: var(--muted); background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%); }
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
            body {
                background-attachment: scroll;
                animation-duration: 28s;
            }
            .topbar {
                position: sticky;
                align-items: stretch;
                flex-direction: column;
                gap: 12px;
                padding: 12px 14px;
                min-height: auto;
            }
            .brand-logo {
                width: min(190px, 62vw);
                height: 52px;
            }
            .nav {
                width: 100%;
                flex-wrap: nowrap;
                overflow-x: auto;
                overscroll-behavior-x: contain;
                padding-bottom: 4px;
                scrollbar-width: thin;
                -webkit-overflow-scrolling: touch;
            }
            .nav a, .nav .link-button {
                min-height: 42px;
                white-space: nowrap;
                flex: 0 0 auto;
            }
            .nav-left,
            .nav-actions {
                justify-content: flex-start;
                font-size: 15px;
                gap: 10px;
            }
            .nav-action-button {
                min-width: 112px;
                min-height: 46px;
            }
            .main {
                width: min(100% - 24px, 1180px);
                padding: 20px 0 42px;
            }
            .hero, .metrics, .courses, .split, .pipeline, .form-grid {
                grid-template-columns: 1fr;
            }
            .hero {
                gap: 20px;
                padding: 24px 18px;
            }
            .hero h1 {
                font-size: clamp(32px, 11vw, 48px);
                line-height: 1.05;
            }
            .hero p {
                font-size: 16px;
                line-height: 1.65;
            }
            .hero-logo {
                max-width: 300px;
            }
            .section {
                margin-top: 22px;
            }
            .section-head {
                align-items: stretch;
                flex-direction: column;
                gap: 10px;
            }
            .section h2 {
                font-size: 22px;
                line-height: 1.25;
            }
            .metric, .card, .list-row {
                padding: 16px;
            }
            .metric {
                min-height: 128px;
                padding-right: 84px;
            }
            .metric::after {
                width: 50px;
                height: 50px;
                top: 14px;
                right: 14px;
                background-size: 28px 28px, auto;
            }
            .card h3 {
                font-size: 19px;
                line-height: 1.3;
            }
            .card p, .muted, .list-row {
                font-size: 15px;
                line-height: 1.65;
            }
            .meta {
                gap: 10px;
            }
            .button {
                width: 100%;
                min-height: 46px;
                text-align: center;
            }
            .badge, .chip {
                line-height: 1.35;
            }
            video, iframe {
                width: 100%;
                border-radius: 8px;
            }
            iframe {
                min-height: 210px;
            }
            footer {
                padding: 20px 14px;
                font-size: 14px;
                line-height: 1.6;
            }
        }
        @media (max-width: 480px) {
            .main {
                width: min(100% - 18px, 1180px);
            }
            .topbar {
                padding: 10px 9px;
            }
            .brand-logo {
                width: min(176px, 68vw);
                height: 48px;
            }
            .nav {
                gap: 8px;
            }
            .nav a, .nav .link-button {
                font-size: 13px;
                padding: 8px 9px;
            }
            .hero {
                padding: 22px 14px;
            }
            .hero-panel {
                padding: 14px;
            }
            .metric strong {
                font-size: 26px;
            }
            .form-grid {
                gap: 13px;
            }
            input, select, textarea {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
<div class="shell">
    <header class="topbar">
        <a href="{{ route('lms.dashboard') }}" class="brand">
            <img class="brand-logo" src="{{ asset('images/techverse-color.png') }}" alt="Techverse Learning">
        </a>
        <nav class="nav nav-left" aria-label="Menu utama">
            <a href="{{ route('lms.dashboard') }}">Home</a>
            <a href="{{ route('lms.dashboard') }}#program">Program</a>
            <a href="{{ route('lms.dashboard') }}#kontak">Contact</a>
        </nav>
        <nav class="nav nav-actions" aria-label="Akses akun">
            @auth
                <a class="icon-link icon-dashboard" href="{{ route('participant.dashboard') }}">Kelas Saya</a>
                @if(in_array(optional(auth()->user()->role)->name, ['super-admin', 'admin-lms'], true))
                    <a class="icon-link icon-shield" href="{{ route('admin.courses.index') }}">Admin</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="link-button icon-link icon-login" type="submit">Logout</button>
                </form>
            @else
                <a class="nav-action-button" href="{{ route('login') }}">Login</a>
                <a class="nav-action-button primary" href="{{ route('lms.dashboard') }}#program">Register</a>
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
