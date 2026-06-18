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

            <form class="card" method="POST" action="{{ route('admin.site-content.update') }}" enctype="multipart/form-data">
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
                    <label class="wide">
                        <span>Upload Gambar Dashboard Peserta</span>
                        <input type="file" name="participant_dashboard_image" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                        <small>Format JPG, PNG, WEBP, atau SVG. Maksimal 4MB. Gambar ini menggantikan roket di dashboard peserta.</small>
                        @error('participant_dashboard_image') <small>{{ $message }}</small> @enderror
                    </label>
                    <label class="wide">
                        <span>URL / Path Gambar Dashboard Peserta</span>
                        <input name="settings[participant_dashboard_image_url]" value="{{ old('settings.participant_dashboard_image_url', $settings['participant_dashboard_image_url'] ?? '') }}" placeholder="Kosongkan untuk ilustrasi roket default">
                        @error('settings.participant_dashboard_image_url') <small>{{ $message }}</small> @enderror
                        @if(! empty($settings['participant_dashboard_image_url']))
                            @php
                                $dashboardImagePreview = Str::startsWith($settings['participant_dashboard_image_url'], ['http://', 'https://'])
                                    ? $settings['participant_dashboard_image_url']
                                    : asset($settings['participant_dashboard_image_url']);
                            @endphp
                            <img src="{{ $dashboardImagePreview }}" alt="Preview dashboard peserta" style="max-width:260px;margin-top:10px;border-radius:12px;border:1px solid rgba(47,123,255,.18)">
                        @endif
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
                        <span>Daftar Header Hero</span>
                        <textarea name="settings[hero_slides]" rows="7" placeholder="Judul utama | Judul aksen | Deskripsi">{{ old('settings.hero_slides', $settings['hero_slides'] ?? '') }}</textarea>
                        <small>Tulis satu header per baris dengan format: Judul utama | Judul aksen | Deskripsi. Header akan tampil bergantian di halaman Home.</small>
                        @error('settings.hero_slides') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Upload Gambar Header Hero</span>
                        <input type="file" name="hero_slide_images[]" accept="image/png,image/jpeg,image/webp" multiple>
                        <input type="hidden" name="settings[hero_slide_images]" value="{{ old('settings.hero_slide_images', $settings['hero_slide_images'] ?? '') }}">
                        <small>Pilih beberapa gambar sekaligus sesuai urutan daftar header. Maksimal 10 gambar, masing-masing 4MB.</small>
                        @error('hero_slide_images') <small>{{ $message }}</small> @enderror
                        @error('hero_slide_images.*') <small>{{ $message }}</small> @enderror
                        @php
                            $currentHeroImages = collect(preg_split('/\r\n|\r|\n/', $settings['hero_slide_images'] ?? ''))->filter();
                        @endphp
                        @if($currentHeroImages->isNotEmpty())
                            <span style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:10px">
                                @foreach($currentHeroImages as $image)
                                    <img src="{{ Str::startsWith($image, ['http://', 'https://']) ? $image : asset($image) }}" alt="Gambar header {{ $loop->iteration }}" style="width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:9px;border:1px solid rgba(47,123,255,.18)">
                                @endforeach
                            </span>
                        @endif
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
