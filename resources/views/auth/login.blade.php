@extends('layouts.lms', ['title' => 'Login Peserta'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Participant Access</span>
                    <h2>Login Peserta</h2>
                </div>
            </div>

            @if(session('status'))
                <div class="list-row" style="border-color:var(--brand);background:var(--brand-soft);margin-bottom:14px">
                    {{ session('status') }}
                </div>
            @endif

            <form class="card" method="POST" action="{{ route('login.attempt') }}" style="max-width:520px">
                @csrf

                <div class="form-grid" style="grid-template-columns:1fr">
                    <label>
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Password</span>
                        <input type="password" name="password" required>
                        @error('password') <small>{{ $message }}</small> @enderror
                    </label>

                    <label style="display:flex;align-items:center;gap:8px">
                        <input type="checkbox" name="remember" value="1" style="width:auto">
                        <span>Ingat login</span>
                    </label>
                </div>

                <div class="meta" style="margin-top:18px">
                    <button class="button" type="submit">Masuk ke Kelas Saya</button>
                </div>

                <p class="muted" style="margin-bottom:0">
                    Akun demo peserta: <strong>peserta@example.test</strong> / <strong>password</strong>
                </p>
            </form>
        </section>
    </main>
@endsection
