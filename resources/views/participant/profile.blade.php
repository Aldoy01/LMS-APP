@extends('layouts.lms', ['title' => 'Account Peserta'])

@section('content')
    @php
        $initials = collect(explode(' ', $user->name))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('');
    @endphp

    <style>
        .account-page {
            min-height: 100vh;
            padding: 38px clamp(18px, 5vw, 72px) 60px;
            background:
                radial-gradient(circle at 88% 4%, rgba(66, 200, 236, .13), transparent 24rem),
                radial-gradient(circle at 8% 24%, rgba(125, 22, 184, .08), transparent 22rem),
                #f5f8fd;
        }
        .account-shell { max-width: 1160px; margin: 0 auto; }
        .account-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            color: #4b587c;
            font-size: 12px;
            font-weight: 800;
        }
        .account-back svg { width: 17px; height: 17px; }
        .account-hero {
            position: relative;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 20px;
            min-height: 190px;
            padding: 30px;
            overflow: hidden;
            border-radius: 20px;
            color: #ffffff;
            background:
                radial-gradient(circle at 78% 10%, rgba(83, 224, 212, .3), transparent 15rem),
                linear-gradient(128deg, #17358d 0%, #3157dc 48%, #681ba9 100%);
            box-shadow: 0 24px 54px rgba(49, 87, 220, .2);
        }
        .account-hero::after {
            content: "";
            position: absolute;
            right: -56px;
            bottom: -84px;
            width: 240px;
            height: 240px;
            border: 34px solid rgba(255,255,255,.08);
            border-radius: 50%;
        }
        .account-avatar {
            position: relative;
            z-index: 1;
            width: 102px;
            height: 102px;
            display: grid;
            place-items: center;
            border: 5px solid rgba(255,255,255,.34);
            border-radius: 28px;
            color: #17358d;
            background: linear-gradient(145deg, #ffffff, #dff6ff);
            box-shadow: 0 18px 34px rgba(7, 22, 77, .22);
            font-size: 30px;
            font-weight: 900;
            overflow: hidden;
        }
        .account-avatar img { width: 100%; height: 100%; display: block; object-fit: cover; }
        .account-identity { position: relative; z-index: 1; min-width: 0; }
        .account-kicker {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 8px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .account-kicker::before { content: ""; width: 22px; height: 2px; background: #53e0d4; }
        .account-identity h1 { margin: 0; font-size: clamp(25px, 3vw, 36px); line-height: 1.12; }
        .account-identity p { margin: 8px 0 0; color: rgba(255,255,255,.82); font-size: 13px; text-align: left; }
        .account-hero-stat {
            position: relative;
            z-index: 1;
            min-width: 126px;
            padding: 14px 16px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 14px;
            background: rgba(255,255,255,.11);
            backdrop-filter: blur(10px);
        }
        .account-hero-stat span { display: block; color: rgba(255,255,255,.72); font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .account-hero-stat strong { display: block; margin-top: 4px; font-size: 22px; }
        .account-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 9px;
            margin: 18px 0;
        }
        .account-toolbar a {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid rgba(47,123,255,.13);
            border-radius: 9px;
            color: #4b587c;
            background: #ffffff;
            font-size: 12px;
            font-weight: 800;
            box-shadow: 0 8px 20px rgba(16,85,245,.06);
        }
        .account-toolbar a:hover { color: #3157dc; border-color: rgba(47,123,255,.3); }
        .account-toolbar svg { width: 17px; height: 17px; }
        .account-grid {
            display: grid;
            grid-template-columns: minmax(250px, .72fr) minmax(0, 1.28fr);
            gap: 20px;
            align-items: start;
        }
        .account-panel {
            border: 1px solid rgba(47, 123, 255, .12);
            border-radius: 16px;
            background: rgba(255,255,255,.94);
            box-shadow: 0 16px 38px rgba(16, 85, 245, .08);
        }
        .account-overview { padding: 20px; }
        .panel-heading { display: flex; align-items: center; gap: 11px; margin-bottom: 16px; }
        .panel-icon {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            color: #ffffff;
            background: linear-gradient(145deg, #42c8ec, #3157dc, #6f1daf);
        }
        .panel-icon svg { width: 20px; height: 20px; }
        .panel-heading h2 { margin: 0; color: #07164d; font-size: 17px; }
        .panel-heading p { margin: 3px 0 0; color: #73809f; font-size: 11px; text-align: left; }
        .account-data { display: grid; gap: 8px; }
        .account-data div {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 10px;
            background: #f7faff;
        }
        .data-icon { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 9px; color: #3157dc; background: #eaf2ff; }
        .data-icon svg { width: 17px; height: 17px; }
        .account-data span { display: block; color: #73809f; font-size: 9px; font-weight: 900; text-transform: uppercase; }
        .account-data strong { display: block; margin-top: 2px; color: #07164d; font-size: 12px; overflow-wrap: anywhere; }
        .account-forms { display: grid; gap: 18px; }
        .account-form { padding: 21px; scroll-margin-top: 24px; }
        .account-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 13px; }
        .account-form-grid .wide { grid-column: 1 / -1; }
        .account-form-grid label { display: grid; gap: 6px; color: #4b587c; font-size: 11px; font-weight: 800; }
        .account-form-grid input {
            width: 100%;
            min-height: 43px;
            padding: 9px 11px;
            border: 1px solid rgba(47, 123, 255, .16);
            border-radius: 9px;
            color: #07164d;
            background: #fafdff;
            font: inherit;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }
        .avatar-field {
            display: grid;
            grid-template-columns: 76px minmax(0, 1fr);
            align-items: center;
            gap: 14px;
            padding: 12px;
            border: 1px dashed rgba(47,123,255,.28);
            border-radius: 12px;
            background: #f8fbff;
        }
        .avatar-preview {
            width: 76px;
            height: 76px;
            display: grid;
            place-items: center;
            overflow: hidden;
            border-radius: 18px;
            color: #ffffff;
            background: linear-gradient(145deg, #42c8ec, #3157dc, #7d16b8);
            font-size: 21px;
            font-weight: 900;
        }
        .avatar-preview img { width: 100%; height: 100%; display: block; object-fit: cover; }
        .avatar-field input[type="file"] {
            min-height: auto;
            padding: 8px;
            background: #ffffff;
        }
        .avatar-help { margin: 5px 0 0; color: #73809f; font-size: 10px; }
        .account-form-grid input:focus {
            outline: none;
            border-color: #4b3db8;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(75,61,184,.1);
        }
        .account-status {
            margin-bottom: 14px;
            padding: 10px 12px;
            border: 1px solid rgba(34, 197, 94, .22);
            border-radius: 10px;
            color: #15803d;
            background: rgba(34, 197, 94, .09);
            font-size: 11px;
            font-weight: 800;
        }
        .account-error { color: #b42318; font-size: 10px; font-weight: 700; }
        .account-submit { min-width: 150px; }
        .password-button { background: linear-gradient(135deg, #2f7bff, #4b3db8 48%, #7d16b8); }
        @media (max-width: 800px) {
            .account-page { padding-top: 22px; }
            .account-hero { grid-template-columns: 1fr; padding: 24px 20px; text-align: center; }
            .account-avatar { margin: 0 auto; }
            .account-identity p { text-align: center; }
            .account-kicker { justify-content: center; }
            .account-hero-stat { width: 100%; }
            .account-grid, .account-form-grid { grid-template-columns: 1fr; }
            .account-form-grid .wide { grid-column: auto; }
            .account-submit { width: 100%; }
        }
    </style>

    <main class="account-page">
        <div class="account-shell">
            <a class="account-back" href="{{ route('participant.dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                Kembali ke Dashboard
            </a>

            <section class="account-hero">
                <div class="account-avatar">
                    @if($user->avatar_path)
                        <img src="{{ route('participant.avatar') }}?v={{ $user->updated_at?->timestamp }}" alt="Foto account {{ $user->name }}">
                    @else
                        {{ $initials ?: 'TV' }}
                    @endif
                </div>
                <div class="account-identity">
                    <span class="account-kicker">Participant Account</span>
                    <h1>{{ $user->name }}</h1>
                    <p>{{ optional($user->role)->label ?? 'Peserta' }} · Kelola identitas dan keamanan akun dari satu tempat.</p>
                </div>
                <div class="account-hero-stat">
                    <span>Kelas Aktif</span>
                    <strong>{{ $enrollmentCount }}</strong>
                </div>
            </section>

            <nav class="account-toolbar" aria-label="Navigasi account">
                <a href="#profile">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    Edit Profile
                </a>
                <a href="#password">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                    Keamanan Account
                </a>
            </nav>

            <div class="account-grid">
                <aside class="account-panel account-overview">
                    <div class="panel-heading">
                        <span class="panel-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h10"/></svg>
                        </span>
                        <div><h2>Ringkasan Account</h2><p>Informasi peserta saat ini</p></div>
                    </div>
                    <div class="account-data">
                        <div>
                            <span class="data-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span>
                            <section><span>Email</span><strong>{{ $user->email }}</strong></section>
                        </div>
                        <div>
                            <span class="data-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2h4l2 5-3 2a15 15 0 0 0 6 6l2-3 5 2v4a4 4 0 0 1-4 4C9 21 3 15 2 6a4 4 0 0 1 4-4z"/></svg></span>
                            <section><span>Nomor Telepon</span><strong>{{ $user->phone ?: 'Belum diisi' }}</strong></section>
                        </div>
                        <div>
                            <span class="data-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M6 21V5h9v16"/><path d="M15 9h3v12"/><path d="M9 9h3"/><path d="M9 13h3"/></svg></span>
                            <section><span>Instansi / Perusahaan</span><strong>{{ $user->company ?: 'Belum diisi' }}</strong></section>
                        </div>
                        <div>
                            <span class="data-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"/></svg></span>
                            <section><span>Kelas Aktif</span><strong>{{ $enrollmentCount }} kelas</strong></section>
                        </div>
                    </div>
                </aside>

                <div class="account-forms">
                    <form class="account-panel account-form" id="profile" method="POST" action="{{ route('participant.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="panel-heading">
                            <span class="panel-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/></svg></span>
                            <div><h2>Edit Profile</h2><p>Perbarui informasi yang digunakan di LMS</p></div>
                        </div>
                        @if(session('profile_status')) <div class="account-status">{{ session('profile_status') }}</div> @endif
                        <div class="account-form-grid">
                            <label class="wide">
                                <span>Foto Account</span>
                                <div class="avatar-field">
                                    <div class="avatar-preview" id="avatarPreview">
                                        @if($user->avatar_path)
                                            <img src="{{ route('participant.avatar') }}?v={{ $user->updated_at?->timestamp }}" alt="Preview foto account">
                                        @else
                                            {{ $initials ?: 'TV' }}
                                        @endif
                                    </div>
                                    <div>
                                        <input id="avatarInput" type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
                                        <p class="avatar-help">JPG, PNG, atau WEBP. Maksimal 2MB.</p>
                                        @error('avatar') <small class="account-error">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </label>
                            <label><span>Nama Lengkap</span><input name="name" value="{{ old('name', $user->name) }}" required>@error('name') <small class="account-error">{{ $message }}</small> @enderror</label>
                            <label><span>Email</span><input type="email" name="email" value="{{ old('email', $user->email) }}" required>@error('email') <small class="account-error">{{ $message }}</small> @enderror</label>
                            <label><span>Nomor Telepon</span><input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">@error('phone') <small class="account-error">{{ $message }}</small> @enderror</label>
                            <label><span>Instansi / Perusahaan</span><input name="company" value="{{ old('company', $user->company) }}" placeholder="Opsional">@error('company') <small class="account-error">{{ $message }}</small> @enderror</label>
                            <div class="wide"><button class="button account-submit" type="submit">Simpan Profile</button></div>
                        </div>
                    </form>

                    <form class="account-panel account-form" id="password" method="POST" action="{{ route('participant.password.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="panel-heading">
                            <span class="panel-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/><path d="M12 14v3"/></svg></span>
                            <div><h2>Keamanan Account</h2><p>Gunakan password kuat minimal 8 karakter</p></div>
                        </div>
                        @if(session('password_status')) <div class="account-status">{{ session('password_status') }}</div> @endif
                        <div class="account-form-grid">
                            <label class="wide"><span>Password Lama</span><input type="password" name="current_password" autocomplete="current-password" required>@error('current_password') <small class="account-error">{{ $message }}</small> @enderror</label>
                            <label><span>Password Baru</span><input type="password" name="password" autocomplete="new-password" required>@error('password') <small class="account-error">{{ $message }}</small> @enderror</label>
                            <label><span>Konfirmasi Password</span><input type="password" name="password_confirmation" autocomplete="new-password" required></label>
                            <div class="wide"><button class="button password-button account-submit" type="submit">Update Password</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        (() => {
            const input = document.getElementById('avatarInput');
            const preview = document.getElementById('avatarPreview');
            input?.addEventListener('change', () => {
                const file = input.files?.[0];
                if (!file || !preview) return;
                const image = document.createElement('img');
                image.src = URL.createObjectURL(file);
                image.alt = 'Preview foto account baru';
                image.onload = () => URL.revokeObjectURL(image.src);
                preview.replaceChildren(image);
            });
        })();
    </script>
@endsection
