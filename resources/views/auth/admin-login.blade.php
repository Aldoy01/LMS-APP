@extends('layouts.lms', ['title' => 'Login Admin LMS'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Management System</span>
                    <h2>Login Admin LMS</h2>
                </div>
            </div>

            <form class="card" method="POST" action="{{ route('admin.login.attempt') }}" style="max-width:520px">
                @csrf

                <div class="form-grid" style="grid-template-columns:1fr">
                    <label>
                        <span>Email Admin</span>
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
                        <span>Ingat login admin</span>
                    </label>
                </div>

                <div class="meta" style="margin-top:18px">
                    <button class="button" type="submit">Masuk Management Course</button>
                </div>

                <p class="muted" style="margin-bottom:0">
                    Akun demo admin: <strong>admin@tramatekid.test</strong> / <strong>password</strong>
                </p>
            </form>
        </section>
    </main>
@endsection
