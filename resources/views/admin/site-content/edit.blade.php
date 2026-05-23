@extends('layouts.lms', ['title' => 'Kelola Konten Halaman'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Admin LMS</span>
                    <h2>Konten & Tampilan Halaman</h2>
                </div>
                <a class="button" style="background:var(--night)" href="{{ route('lms.dashboard') }}">Preview Home</a>
            </div>

            @if(session('status'))
                <div class="list-row" style="border-color:var(--teal);background:var(--teal-soft);margin-bottom:14px">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="list-row" style="border-color:var(--danger);background:var(--accent-soft);margin-bottom:14px">
                    Data belum lengkap. Periksa field yang ditandai.
                </div>
            @endif

            <form class="card" method="POST" action="{{ route('admin.site-content.update') }}">
                @csrf
                @method('PUT')

                <div class="section-head" style="margin-top:0">
                    <div>
                        <span class="eyebrow">Branding</span>
                        <h2>Identitas Website</h2>
                    </div>
                </div>
                <div class="form-grid">
                    <label>
                        <span>Nama Platform</span>
                        <input name="settings[site_name]" value="{{ old('settings.site_name', $settings['site_name']) }}" required>
                        @error('settings.site_name') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>URL Logo Opsional</span>
                        <input name="settings[logo_url]" value="{{ old('settings.logo_url', $settings['logo_url']) }}" placeholder="Kosongkan untuk logo default">
                        @error('settings.logo_url') <small>{{ $message }}</small> @enderror
                    </label>
                    <label class="wide">
                        <span>URL Gambar Hero Opsional</span>
                        <input name="settings[hero_image_url]" value="{{ old('settings.hero_image_url', $settings['hero_image_url']) }}" placeholder="Kosongkan untuk gambar default">
                        @error('settings.hero_image_url') <small>{{ $message }}</small> @enderror
                    </label>
                </div>

                <div class="section-head">
                    <div>
                        <span class="eyebrow">Navigasi</span>
                        <h2>Menu & Tombol</h2>
                    </div>
                </div>
                <div class="form-grid">
                    <label>
                        <span>Label Home</span>
                        <input name="settings[nav_home_label]" value="{{ old('settings.nav_home_label', $settings['nav_home_label']) }}" required>
                        @error('settings.nav_home_label') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Label Program</span>
                        <input name="settings[nav_program_label]" value="{{ old('settings.nav_program_label', $settings['nav_program_label']) }}" required>
                        @error('settings.nav_program_label') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Label Contact</span>
                        <input name="settings[nav_contact_label]" value="{{ old('settings.nav_contact_label', $settings['nav_contact_label']) }}" required>
                        @error('settings.nav_contact_label') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Label Login</span>
                        <input name="settings[login_label]" value="{{ old('settings.login_label', $settings['login_label']) }}" required>
                        @error('settings.login_label') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Label Register</span>
                        <input name="settings[register_label]" value="{{ old('settings.register_label', $settings['register_label']) }}" required>
                        @error('settings.register_label') <small>{{ $message }}</small> @enderror
                    </label>
                </div>

                <div class="section-head">
                    <div>
                        <span class="eyebrow">Home Page</span>
                        <h2>Hero & Intro</h2>
                    </div>
                </div>
                <div class="form-grid">
                    <label class="wide">
                        <span>Judul Hero</span>
                        <textarea name="settings[hero_title]" rows="3" required>{{ old('settings.hero_title', $settings['hero_title']) }}</textarea>
                        @error('settings.hero_title') <small>{{ $message }}</small> @enderror
                    </label>
                    <label class="wide">
                        <span>Subjudul Hero</span>
                        <textarea name="settings[hero_subtitle]" rows="4" required>{{ old('settings.hero_subtitle', $settings['hero_subtitle']) }}</textarea>
                        @error('settings.hero_subtitle') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Label CTA Hero</span>
                        <input name="settings[hero_cta_label]" value="{{ old('settings.hero_cta_label', $settings['hero_cta_label']) }}" required>
                        @error('settings.hero_cta_label') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Badge Gambar Hero</span>
                        <input name="settings[hero_visual_badge]" value="{{ old('settings.hero_visual_badge', $settings['hero_visual_badge']) }}" required>
                        @error('settings.hero_visual_badge') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Eyebrow Intro</span>
                        <input name="settings[intro_eyebrow]" value="{{ old('settings.intro_eyebrow', $settings['intro_eyebrow']) }}" required>
                        @error('settings.intro_eyebrow') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Judul Intro</span>
                        <input name="settings[intro_title]" value="{{ old('settings.intro_title', $settings['intro_title']) }}" required>
                        @error('settings.intro_title') <small>{{ $message }}</small> @enderror
                    </label>
                </div>

                <div class="section-head">
                    <div>
                        <span class="eyebrow">Kontak & Warna</span>
                        <h2>Support dan Tampilan</h2>
                    </div>
                </div>
                <div class="form-grid">
                    <label>
                        <span>WhatsApp Admin</span>
                        <input name="settings[contact_whatsapp]" value="{{ old('settings.contact_whatsapp', $settings['contact_whatsapp']) }}" required>
                        @error('settings.contact_whatsapp') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Email Admin</span>
                        <input type="email" name="settings[contact_email]" value="{{ old('settings.contact_email', $settings['contact_email']) }}" required>
                        @error('settings.contact_email') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Warna Utama</span>
                        <input type="color" name="settings[primary_color]" value="{{ old('settings.primary_color', $settings['primary_color']) }}" required>
                        @error('settings.primary_color') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Warna Aksen</span>
                        <input type="color" name="settings[accent_color]" value="{{ old('settings.accent_color', $settings['accent_color']) }}" required>
                        @error('settings.accent_color') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Background Home</span>
                        <input type="color" name="settings[home_background]" value="{{ old('settings.home_background', $settings['home_background']) }}" required>
                        @error('settings.home_background') <small>{{ $message }}</small> @enderror
                    </label>
                </div>

                <div class="meta" style="margin-top:22px">
                    <button class="button" type="submit">Simpan Konten Halaman</button>
                    <a class="button" style="background:var(--night)" href="{{ route('lms.dashboard') }}">Lihat Hasil</a>
                </div>
            </form>
        </section>
    </main>
@endsection
