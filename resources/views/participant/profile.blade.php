@extends('layouts.lms', ['title' => 'Account Peserta'])

@section('content')
    @php
        $initials = collect(explode(' ', $user->name))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('');
    @endphp

    <style>
        .account-page {
            min-height: 100vh;
            padding: 48px clamp(18px, 5vw, 72px);
            background: #f4f7fc;
        }
        .account-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            max-width: 1120px;
            margin: 0 auto 22px;
        }
        .account-head h1 { margin: 4px 0 0; color: #07164d; font-size: clamp(24px, 3vw, 34px); }
        .account-head p { margin: 0; color: #4b587c; font-size: 13px; }
        .account-grid {
            display: grid;
            grid-template-columns: minmax(250px, .72fr) minmax(0, 1.28fr);
            gap: 20px;
            max-width: 1120px;
            margin: 0 auto;
            align-items: start;
        }
        .account-card {
            padding: 22px;
            border: 1px solid rgba(47, 123, 255, .14);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 14px 34px rgba(16, 85, 245, .08);
        }
        .account-summary { text-align: center; }
        .account-avatar {
            width: 88px;
            height: 88px;
            display: grid;
            place-items: center;
            margin: 0 auto 14px;
            border: 5px solid #e5f0ff;
            border-radius: 50%;
            color: #ffffff;
            background: linear-gradient(145deg, #2f7bff, #4b3db8, #7d16b8);
            font-size: 27px;
            font-weight: 900;
        }
        .account-summary h2 { margin: 0; color: #07164d; font-size: 19px; }
        .account-summary > p { margin: 5px 0 18px; color: #4b587c; font-size: 13px; text-align: center; }
        .account-data { display: grid; gap: 10px; text-align: left; }
        .account-data div { padding: 10px 12px; border-radius: 10px; background: #f7faff; }
        .account-data span { display: block; color: #73809f; font-size: 10px; font-weight: 900; text-transform: uppercase; }
        .account-data strong { display: block; margin-top: 3px; color: #07164d; font-size: 13px; overflow-wrap: anywhere; }
        .account-forms { display: grid; gap: 18px; }
        .account-card h2 { margin: 0 0 4px; color: #07164d; font-size: 18px; }
        .account-card > p { margin: 0 0 16px; color: #4b587c; font-size: 12px; text-align: left; }
        .account-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 13px; }
        .account-form-grid .wide { grid-column: 1 / -1; }
        .account-form-grid label { display: grid; gap: 6px; color: #4b587c; font-size: 12px; font-weight: 800; }
        .account-form-grid input {
            width: 100%;
            min-height: 43px;
            padding: 9px 11px;
            border: 1px solid rgba(47, 123, 255, .18);
            border-radius: 9px;
            color: #07164d;
            background: #fafdff;
            font: inherit;
        }
        .account-form-grid input:focus { outline: 2px solid rgba(47,123,255,.18); border-color: #3157dc; }
        .account-status {
            margin-bottom: 14px;
            padding: 10px 12px;
            border: 1px solid rgba(34, 197, 94, .22);
            border-radius: 10px;
            color: #15803d;
            background: rgba(34, 197, 94, .09);
            font-size: 12px;
            font-weight: 800;
        }
        .account-error { color: #b42318; font-size: 11px; font-weight: 700; }
        .password-button { background: linear-gradient(135deg, #2f7bff, #4b3db8 48%, #7d16b8); }
        @media (max-width: 800px) {
            .account-head { align-items: flex-start; flex-direction: column; }
            .account-grid, .account-form-grid { grid-template-columns: 1fr; }
            .account-form-grid .wide { grid-column: auto; }
        }
    </style>

    <main class="account-page">
        <div class="account-head">
            <div>
                <p>My Account</p>
                <h1>Account Peserta</h1>
            </div>
            <a class="button" href="{{ route('participant.dashboard') }}">Kembali ke Dashboard</a>
        </div>

        <div class="account-grid">
            <aside class="account-card account-summary">
                <div class="account-avatar">{{ $initials ?: 'TV' }}</div>
                <h2>{{ $user->name }}</h2>
                <p>{{ optional($user->role)->label ?? 'Peserta' }}</p>
                <div class="account-data">
                    <div><span>Email</span><strong>{{ $user->email }}</strong></div>
                    <div><span>Nomor Telepon</span><strong>{{ $user->phone ?: 'Belum diisi' }}</strong></div>
                    <div><span>Instansi / Perusahaan</span><strong>{{ $user->company ?: 'Belum diisi' }}</strong></div>
                    <div><span>Kelas Aktif</span><strong>{{ $enrollmentCount }} kelas</strong></div>
                </div>
            </aside>

            <div class="account-forms">
                <form class="account-card" method="POST" action="{{ route('participant.profile.update') }}">
                    @csrf
                    @method('PUT')
                    <h2>Edit Data Peserta</h2>
                    <p>Perbarui identitas yang digunakan pada akun LMS.</p>
                    @if(session('profile_status')) <div class="account-status">{{ session('profile_status') }}</div> @endif
                    <div class="account-form-grid">
                        <label><span>Nama Lengkap</span><input name="name" value="{{ old('name', $user->name) }}" required>@error('name') <small class="account-error">{{ $message }}</small> @enderror</label>
                        <label><span>Email</span><input type="email" name="email" value="{{ old('email', $user->email) }}" required>@error('email') <small class="account-error">{{ $message }}</small> @enderror</label>
                        <label><span>Nomor Telepon</span><input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">@error('phone') <small class="account-error">{{ $message }}</small> @enderror</label>
                        <label><span>Instansi / Perusahaan</span><input name="company" value="{{ old('company', $user->company) }}" placeholder="Opsional">@error('company') <small class="account-error">{{ $message }}</small> @enderror</label>
                        <div class="wide"><button class="button" type="submit">Simpan Perubahan</button></div>
                    </div>
                </form>

                <form class="account-card" id="password" method="POST" action="{{ route('participant.password.update') }}">
                    @csrf
                    @method('PUT')
                    <h2>Reset Password</h2>
                    <p>Masukkan password lama dan gunakan password baru minimal 8 karakter.</p>
                    @if(session('password_status')) <div class="account-status">{{ session('password_status') }}</div> @endif
                    <div class="account-form-grid">
                        <label class="wide"><span>Password Lama</span><input type="password" name="current_password" autocomplete="current-password" required>@error('current_password') <small class="account-error">{{ $message }}</small> @enderror</label>
                        <label><span>Password Baru</span><input type="password" name="password" autocomplete="new-password" required>@error('password') <small class="account-error">{{ $message }}</small> @enderror</label>
                        <label><span>Konfirmasi Password</span><input type="password" name="password_confirmation" autocomplete="new-password" required></label>
                        <div class="wide"><button class="button password-button" type="submit">Ubah Password</button></div>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
