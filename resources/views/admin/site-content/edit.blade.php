@extends('layouts.lms', ['title' => 'Konten & Tampilan'])

@section('content')
@php
    $dashboardImageValue = old('settings.participant_dashboard_image_url', $settings['participant_dashboard_image_url'] ?? '');
    $dashboardImagePreview = $dashboardImageValue
        ? (Str::startsWith($dashboardImageValue, ['http://', 'https://']) ? $dashboardImageValue : asset($dashboardImageValue))
        : '';
    $currentHeroSlideLines = collect(preg_split(
        '/\r\n|\r|\n/',
        old('settings.hero_slides', $settings['hero_slides'] ?? '')
    ))->filter(fn ($line) => trim($line) !== '')->values();
    $currentHeroImages = collect(preg_split(
        '/\r\n|\r|\n/',
        old('settings.hero_slide_images', $settings['hero_slide_images'] ?? '')
    ))->map(fn ($image) => trim($image))->values();
@endphp

<style>
    .content-admin {
        --admin-blue: #3157dc;
        --admin-night: #07164d;
        --admin-soft: #f5f8ff;
        --admin-line: #dce6f7;
        width: min(1280px, calc(100% - 32px));
        margin: 0 auto;
        padding: 30px 0 72px;
    }
    .content-admin-hero {
        position: relative;
        overflow: hidden;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 24px;
        align-items: center;
        padding: clamp(26px, 4vw, 42px);
        border: 1px solid rgba(49,87,220,.16);
        border-radius: 24px;
        color: #fff;
        background:
            radial-gradient(circle at 88% 5%, rgba(103,232,249,.25), transparent 18rem),
            linear-gradient(135deg, #07164d 0%, #234ec4 62%, #2f7bff 100%);
        box-shadow: 0 24px 54px rgba(7,22,77,.18);
    }
    .content-admin-hero::after {
        position: absolute;
        right: -40px;
        bottom: -70px;
        width: 240px;
        height: 240px;
        border: 42px solid rgba(255,255,255,.08);
        border-radius: 50%;
        content: "";
    }
    .content-admin-hero-copy,
    .content-admin-actions {
        position: relative;
        z-index: 1;
    }
    .content-admin-kicker {
        color: #67e8f9;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
    }
    .content-admin-hero h1 {
        max-width: 760px;
        margin: 8px 0 8px;
        font-size: clamp(30px, 4vw, 48px);
        line-height: 1.08;
    }
    .content-admin-hero p {
        max-width: 720px;
        margin: 0;
        color: #dbeafe;
        font-size: 15px;
        line-height: 1.65;
    }
    .content-admin-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;
    }
    .content-admin-actions a {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 15px;
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 10px;
        color: #fff;
        background: rgba(255,255,255,.1);
        font-size: 12px;
        font-weight: 900;
    }
    .content-admin-actions a.primary {
        color: var(--admin-blue);
        background: #fff;
    }
    .admin-feedback {
        margin-top: 18px;
        padding: 14px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 800;
    }
    .admin-feedback.success {
        color: #166534;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
    }
    .admin-feedback.error {
        color: #be123c;
        border: 1px solid #fecdd3;
        background: #fff1f2;
    }
    .content-editor-layout {
        display: grid;
        grid-template-columns: 230px minmax(0, 1fr);
        gap: 22px;
        align-items: start;
        margin-top: 22px;
    }
    .content-editor-nav {
        position: sticky;
        top: 112px;
        display: grid;
        gap: 7px;
        padding: 14px;
        border: 1px solid var(--admin-line);
        border-radius: 18px;
        background: rgba(255,255,255,.94);
        box-shadow: 0 14px 34px rgba(16,85,245,.08);
    }
    .content-editor-nav strong {
        padding: 6px 8px 10px;
        color: var(--admin-night);
        font-size: 13px;
    }
    .content-editor-nav a {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 10px;
        border-radius: 10px;
        color: #536383;
        font-size: 12px;
        font-weight: 800;
    }
    .content-editor-nav a::before {
        width: 8px;
        height: 8px;
        flex: 0 0 auto;
        border-radius: 50%;
        content: "";
        background: #bfdbfe;
    }
    .content-editor-nav a:hover {
        color: var(--admin-blue);
        background: #eef4ff;
    }
    .content-editor-nav a:hover::before {
        background: var(--admin-blue);
    }
    .content-editor-form {
        min-width: 0;
        display: grid;
        gap: 18px;
    }
    .editor-panel {
        scroll-margin-top: 112px;
        overflow: hidden;
        border: 1px solid var(--admin-line);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 14px 34px rgba(16,85,245,.07);
    }
    .editor-panel-head {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 22px;
        border-bottom: 1px solid #e8eef9;
        background: linear-gradient(135deg, #fff, #f6f9ff);
    }
    .editor-panel-icon {
        width: 42px;
        height: 42px;
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        border-radius: 12px;
        color: #fff;
        background: linear-gradient(135deg, #3157dc, #2f7bff);
        box-shadow: 0 8px 18px rgba(49,87,220,.2);
        font-size: 17px;
        font-weight: 900;
    }
    .editor-panel-head h2 {
        margin: 0;
        color: var(--admin-night);
        font-size: 19px;
    }
    .editor-panel-head p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }
    .editor-panel-body {
        padding: 22px;
    }
    .editor-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 17px;
    }
    .editor-field {
        min-width: 0;
        display: grid;
        align-content: start;
        gap: 7px;
    }
    .editor-field.wide {
        grid-column: 1 / -1;
    }
    .editor-field > span:first-child {
        color: #27365e;
        font-size: 12px;
        font-weight: 900;
    }
    .editor-field input:not([type="color"]),
    .editor-field textarea,
    .editor-field select {
        width: 100%;
        min-height: 45px;
        padding: 11px 13px;
        border: 1px solid #ccd8ec;
        border-radius: 10px;
        color: var(--admin-night);
        background: #fbfdff;
        font: inherit;
        font-size: 14px;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }
    .editor-field textarea {
        resize: vertical;
        line-height: 1.55;
    }
    .editor-field input:focus,
    .editor-field textarea:focus,
    .editor-field select:focus {
        outline: none;
        border-color: #5f87ef;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(49,87,220,.1);
    }
    .editor-field small {
        color: #71809e;
        font-size: 11px;
        line-height: 1.5;
    }
    .editor-field small.field-error {
        color: #e11d48;
        font-weight: 800;
    }
    .upload-zone {
        display: grid;
        grid-template-columns: 170px minmax(0, 1fr);
        gap: 16px;
        align-items: center;
        padding: 15px;
        border: 1px dashed #a9bde5;
        border-radius: 14px;
        background: #f8fbff;
    }
    .upload-preview {
        width: 100%;
        aspect-ratio: 16 / 10;
        display: grid;
        place-items: center;
        overflow: hidden;
        border-radius: 11px;
        color: #64748b;
        background: #e9effb;
        font-size: 11px;
        font-weight: 900;
    }
    .upload-preview img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }
    .upload-copy {
        display: grid;
        gap: 8px;
    }
    .upload-copy strong {
        color: var(--admin-night);
        font-size: 13px;
    }
    .upload-copy input[type="file"] {
        width: 100%;
        color: #536383;
        font-size: 12px;
    }
    .slide-stack {
        display: grid;
        gap: 10px;
    }
    .slide-upload-card {
        display: grid;
        grid-template-columns: 112px minmax(0, 1fr);
        gap: 13px;
        align-items: center;
        padding: 12px;
        border: 1px solid var(--admin-line);
        border-radius: 13px;
        background: #f8fbff;
    }
    .slide-thumb {
        width: 112px;
        aspect-ratio: 16 / 10;
        display: grid;
        place-items: center;
        overflow: hidden;
        border-radius: 9px;
        color: #64748b;
        background: #e8efff;
        font-size: 10px;
        font-weight: 900;
    }
    .slide-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .slide-upload-copy {
        min-width: 0;
        display: grid;
        gap: 7px;
    }
    .slide-upload-copy strong {
        overflow: hidden;
        color: var(--admin-night);
        font-size: 12px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .slide-upload-copy input {
        font-size: 11px;
    }
    .color-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .color-card {
        display: grid;
        grid-template-columns: 46px minmax(0, 1fr);
        gap: 10px;
        align-items: center;
        padding: 12px;
        border: 1px solid var(--admin-line);
        border-radius: 12px;
        background: #f8fbff;
    }
    .color-card input[type="color"] {
        width: 46px;
        height: 46px;
        padding: 3px;
        border: 1px solid #cad7ed;
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
    }
    .color-card strong {
        display: block;
        color: var(--admin-night);
        font-size: 11px;
    }
    .color-card span {
        display: block;
        margin-top: 3px;
        color: #71809e;
        font-size: 10px;
    }
    .editor-savebar {
        position: sticky;
        bottom: 14px;
        z-index: 4;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 16px;
        border: 1px solid rgba(49,87,220,.18);
        border-radius: 16px;
        background: rgba(255,255,255,.94);
        box-shadow: 0 18px 46px rgba(7,22,77,.16);
        backdrop-filter: blur(14px);
    }
    .editor-savebar p {
        margin: 0;
        color: #64748b;
        font-size: 11px;
        line-height: 1.5;
    }
    .editor-save-actions {
        display: flex;
        gap: 9px;
    }
    .editor-save-actions button,
    .editor-save-actions a {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
        border: 0;
        border-radius: 10px;
        font: inherit;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
    }
    .editor-save-actions button {
        color: #fff;
        background: linear-gradient(135deg, #3157dc, #2f7bff);
        box-shadow: 0 9px 20px rgba(49,87,220,.22);
    }
    .editor-save-actions a {
        color: #3157dc;
        border: 1px solid #cbd8ef;
        background: #f4f7ff;
    }
    @media (max-width: 940px) {
        .content-admin-hero { grid-template-columns: 1fr; }
        .content-admin-actions { justify-content: flex-start; }
        .content-editor-layout { grid-template-columns: 1fr; }
        .content-editor-nav {
            position: static;
            display: flex;
            overflow-x: auto;
        }
        .content-editor-nav strong { display: none; }
        .content-editor-nav a { flex: 0 0 auto; }
    }
    @media (max-width: 680px) {
        .content-admin {
            width: min(100% - 18px, 1280px);
            padding-top: 18px;
        }
        .content-admin-hero {
            padding: 23px 18px;
            border-radius: 18px;
        }
        .content-admin-actions a { flex: 1; }
        .editor-panel { border-radius: 16px; }
        .editor-panel-head,
        .editor-panel-body { padding: 17px 15px; }
        .editor-grid,
        .color-grid { grid-template-columns: 1fr; }
        .upload-zone,
        .slide-upload-card { grid-template-columns: 1fr; }
        .upload-preview { max-width: 240px; }
        .slide-thumb { width: 100%; max-width: 220px; }
        .editor-savebar {
            bottom: 8px;
            align-items: stretch;
            flex-direction: column;
        }
        .editor-save-actions { display: grid; grid-template-columns: 1fr 1fr; }
    }
</style>

<main class="content-admin">
    <header class="content-admin-hero">
        <div class="content-admin-hero-copy">
            <span class="content-admin-kicker">Admin LMS · Website Editor</span>
            <h1>Konten & Tampilan Halaman</h1>
            <p>Kelola identitas brand, navigasi, slider Home, gambar dashboard peserta, kontak, dan warna website dari satu tempat.</p>
        </div>
        <div class="content-admin-actions">
            <a href="{{ route('admin.courses.index') }}">Dashboard Admin</a>
            <a class="primary" href="{{ route('lms.dashboard') }}" target="_blank" rel="noopener">Preview Website ↗</a>
        </div>
    </header>

    @if(session('status'))
        <div class="admin-feedback success">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="admin-feedback error">Beberapa data belum valid. Periksa pesan pada field yang ditandai.</div>
    @endif

    <div class="content-editor-layout">
        <nav class="content-editor-nav" aria-label="Navigasi editor konten">
            <strong>Pengaturan Halaman</strong>
            <a href="#identitas">Identitas Website</a>
            <a href="#navigasi">Menu & Tombol</a>
            <a href="#hero">Hero & Slider</a>
            <a href="#dashboard-visual">Visual Peserta</a>
            <a href="#kontak-warna">Kontak & Warna</a>
        </nav>

        <form class="content-editor-form" method="POST" action="{{ route('admin.site-content.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <section class="editor-panel" id="identitas">
                <header class="editor-panel-head">
                    <span class="editor-panel-icon">01</span>
                    <div><h2>Identitas Website</h2><p>Nama platform dan aset utama yang mewakili brand Trama Verse.</p></div>
                </header>
                <div class="editor-panel-body">
                    <div class="editor-grid">
                        <label class="editor-field">
                            <span>Nama Platform</span>
                            <input name="settings[site_name]" value="{{ old('settings.site_name', $settings['site_name']) }}" required>
                            @error('settings.site_name') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="editor-field">
                            <span>URL Logo</span>
                            <input name="settings[logo_url]" value="{{ old('settings.logo_url', $settings['logo_url']) }}" placeholder="Kosongkan untuk logo default">
                            <small>Gunakan URL gambar publik atau biarkan kosong.</small>
                            @error('settings.logo_url') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="editor-field wide">
                            <span>URL Gambar Hero Utama</span>
                            <input name="settings[hero_image_url]" value="{{ old('settings.hero_image_url', $settings['hero_image_url']) }}" placeholder="Kosongkan untuk gambar default">
                            @error('settings.hero_image_url') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                    </div>
                </div>
            </section>

            <section class="editor-panel" id="navigasi">
                <header class="editor-panel-head">
                    <span class="editor-panel-icon">02</span>
                    <div><h2>Menu & Tombol</h2><p>Atur nama menu yang dilihat pengunjung pada header website.</p></div>
                </header>
                <div class="editor-panel-body">
                    <div class="editor-grid">
                        @foreach([
                            'nav_home_label' => ['Menu Home', 'Home'],
                            'nav_program_label' => ['Menu Program', 'Program'],
                            'nav_contact_label' => ['Menu Contact', 'Contact'],
                            'login_label' => ['Tombol Login', 'Login'],
                            'register_label' => ['Tombol Register', 'Register'],
                        ] as $key => [$label, $placeholder])
                            <label class="editor-field">
                                <span>{{ $label }}</span>
                                <input name="settings[{{ $key }}]" value="{{ old('settings.' . $key, $settings[$key]) }}" placeholder="{{ $placeholder }}" required>
                                @error('settings.' . $key) <small class="field-error">{{ $message }}</small> @enderror
                            </label>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="editor-panel" id="hero">
                <header class="editor-panel-head">
                    <span class="editor-panel-icon">03</span>
                    <div><h2>Hero & Slider Home</h2><p>Kelola headline, deskripsi, CTA, dan gambar untuk setiap slide.</p></div>
                </header>
                <div class="editor-panel-body">
                    <div class="editor-grid">
                        <label class="editor-field wide">
                            <span>Judul Hero Default</span>
                            <textarea name="settings[hero_title]" rows="2" required>{{ old('settings.hero_title', $settings['hero_title']) }}</textarea>
                            @error('settings.hero_title') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="editor-field wide">
                            <span>Subjudul Hero Default</span>
                            <textarea name="settings[hero_subtitle]" rows="3" required>{{ old('settings.hero_subtitle', $settings['hero_subtitle']) }}</textarea>
                            @error('settings.hero_subtitle') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="editor-field">
                            <span>Label Tombol Hero</span>
                            <input name="settings[hero_cta_label]" value="{{ old('settings.hero_cta_label', $settings['hero_cta_label']) }}" required>
                            @error('settings.hero_cta_label') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="editor-field">
                            <span>Badge Visual Hero</span>
                            <input name="settings[hero_visual_badge]" value="{{ old('settings.hero_visual_badge', $settings['hero_visual_badge']) }}" required>
                            @error('settings.hero_visual_badge') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="editor-field">
                            <span>Label Intro</span>
                            <input name="settings[intro_eyebrow]" value="{{ old('settings.intro_eyebrow', $settings['intro_eyebrow']) }}" required>
                            @error('settings.intro_eyebrow') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="editor-field">
                            <span>Judul Intro</span>
                            <input name="settings[intro_title]" value="{{ old('settings.intro_title', $settings['intro_title']) }}" required>
                            @error('settings.intro_title') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="editor-field wide">
                            <span>Daftar Header Slider</span>
                            <textarea name="settings[hero_slides]" rows="6" placeholder="Judul utama | Judul aksen | Deskripsi">{{ old('settings.hero_slides', $settings['hero_slides'] ?? '') }}</textarea>
                            <small>Satu slide per baris. Format: Judul utama | Judul aksen | Deskripsi.</small>
                            @error('settings.hero_slides') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                        <div class="editor-field wide">
                            <span>Gambar Setiap Slider</span>
                            <textarea name="settings[hero_slide_images]" hidden>{{ old('settings.hero_slide_images', $settings['hero_slide_images'] ?? '') }}</textarea>
                            <div class="slide-stack">
                                @forelse($currentHeroSlideLines as $slideIndex => $slideLine)
                                    @php
                                        $slideTitle = trim(explode('|', $slideLine, 2)[0] ?? 'Header ' . ($slideIndex + 1));
                                        $currentImage = $currentHeroImages->get($slideIndex, '');
                                        $currentImageUrl = $currentImage
                                            ? (Str::startsWith($currentImage, ['http://', 'https://']) ? $currentImage : asset($currentImage))
                                            : '';
                                    @endphp
                                    <div class="slide-upload-card">
                                        <div class="slide-thumb" data-slide-preview="{{ $slideIndex }}">
                                            @if($currentImageUrl)
                                                <img src="{{ $currentImageUrl }}" alt="Gambar {{ $slideTitle }}">
                                            @else
                                                Belum ada gambar
                                            @endif
                                        </div>
                                        <div class="slide-upload-copy">
                                            <strong>{{ $slideIndex + 1 }}. {{ $slideTitle }}</strong>
                                            <input type="file" name="hero_slide_images[{{ $slideIndex }}]" accept="image/png,image/jpeg,image/webp" data-slide-input="{{ $slideIndex }}">
                                            <small>JPG, PNG, atau WEBP maksimal 4MB.</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="admin-feedback error">Isi dan simpan daftar slider terlebih dahulu, lalu upload gambar masing-masing slide.</div>
                                @endforelse
                            </div>
                            @error('hero_slide_images') <small class="field-error">{{ $message }}</small> @enderror
                            @error('hero_slide_images.*') <small class="field-error">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="editor-panel" id="dashboard-visual">
                <header class="editor-panel-head">
                    <span class="editor-panel-icon">04</span>
                    <div><h2>Visual Dashboard Peserta</h2><p>Ganti ilustrasi utama pada dashboard peserta dengan upload atau URL.</p></div>
                </header>
                <div class="editor-panel-body">
                    <div class="upload-zone">
                        <div class="upload-preview" data-image-preview>
                            @if($dashboardImagePreview)
                                <img src="{{ $dashboardImagePreview }}" alt="Preview dashboard peserta">
                            @else
                                Preview gambar
                            @endif
                        </div>
                        <div class="upload-copy">
                            <strong>Upload Ilustrasi Baru</strong>
                            <input type="file" name="participant_dashboard_image" accept="image/png,image/jpeg,image/webp,image/svg+xml" data-image-input>
                            <small>JPG, PNG, WEBP, atau SVG maksimal 4MB. File baru menggantikan visual sebelumnya.</small>
                            @error('participant_dashboard_image') <small class="field-error">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <label class="editor-field" style="margin-top:16px">
                        <span>URL / Path Gambar Dashboard</span>
                        <input name="settings[participant_dashboard_image_url]" value="{{ $dashboardImageValue }}" placeholder="Kosongkan untuk ilustrasi default">
                        @error('settings.participant_dashboard_image_url') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                </div>
            </section>

            <section class="editor-panel" id="kontak-warna">
                <header class="editor-panel-head">
                    <span class="editor-panel-icon">05</span>
                    <div><h2>Kontak & Warna</h2><p>Atur kanal bantuan peserta dan palet utama website.</p></div>
                </header>
                <div class="editor-panel-body">
                    <div class="editor-grid" style="margin-bottom:18px">
                        <label class="editor-field">
                            <span>WhatsApp Admin</span>
                            <input name="settings[contact_whatsapp]" value="{{ old('settings.contact_whatsapp', $settings['contact_whatsapp']) }}" required>
                            @error('settings.contact_whatsapp') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="editor-field">
                            <span>Email Admin</span>
                            <input type="email" name="settings[contact_email]" value="{{ old('settings.contact_email', $settings['contact_email']) }}" required>
                            @error('settings.contact_email') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                    </div>
                    <div class="color-grid">
                        @foreach([
                            'primary_color' => ['Warna Utama', 'Brand dan tombol'],
                            'accent_color' => ['Warna Aksen', 'Highlight dan dekorasi'],
                            'home_background' => ['Background Home', 'Latar halaman utama'],
                        ] as $key => [$label, $description])
                            <label class="color-card">
                                <input type="color" name="settings[{{ $key }}]" value="{{ old('settings.' . $key, $settings[$key]) }}" required>
                                <span><strong>{{ $label }}</strong><span>{{ $description }}</span></span>
                                @error('settings.' . $key) <small class="field-error">{{ $message }}</small> @enderror
                            </label>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="editor-savebar">
                <p>Perubahan baru tampil setelah tombol simpan ditekan. Gunakan Preview Website untuk memeriksa hasilnya.</p>
                <div class="editor-save-actions">
                    <a href="{{ route('lms.dashboard') }}" target="_blank" rel="noopener">Preview</a>
                    <button type="submit">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
    (function () {
        const input = document.querySelector('[data-image-input]');
        const preview = document.querySelector('[data-image-preview]');
        if (!input || !preview) return;

        input.addEventListener('change', function () {
            const file = input.files && input.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = 'Preview gambar dashboard peserta';
            preview.replaceChildren(image);
        });

        document.querySelectorAll('[data-slide-input]').forEach(function (slideInput) {
            slideInput.addEventListener('change', function () {
                const file = slideInput.files && slideInput.files[0];
                const target = document.querySelector('[data-slide-preview="' + slideInput.dataset.slideInput + '"]');
                if (!file || !target || !file.type.startsWith('image/')) return;

                const image = document.createElement('img');
                image.src = URL.createObjectURL(file);
                image.alt = 'Preview gambar slider';
                target.replaceChildren(image);
            });
        });
    })();
</script>
@endsection
