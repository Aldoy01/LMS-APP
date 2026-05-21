@extends('layouts.lms', ['title' => $user->exists ? 'Edit User' : 'Tambah User'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">User Access</span>
                    <h2>{{ $user->exists ? 'Edit User' : 'Tambah User Resmi' }}</h2>
                </div>
                <a class="button" style="background:#172033" href="{{ route('admin.users.index') }}">Kembali</a>
            </div>

            @if(session('status'))
                <div class="list-row" style="border-color:#0f766e;background:#eef6f5;margin-bottom:14px">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="list-row" style="border-color:#b42318;background:#fff4f2;margin-bottom:14px">
                    Data belum lengkap. Periksa field yang ditandai.
                </div>
            @endif

            <form class="card" method="POST" action="{{ $action }}">
                @csrf
                @if($method === 'PUT')
                    @method('PUT')
                @endif

                <div class="form-grid">
                    <label>
                        <span>Nama User</span>
                        <input name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Email Login</span>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Role Akses</span>
                        <select name="role_id" required>
                            <option value="">Pilih role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected((int) old('role_id', $user->role_id) === $role->id)>
                                    {{ $role->label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>No. HP</span>
                        <input name="phone" value="{{ old('phone', $user->phone) }}">
                        @error('phone') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="wide">
                        <span>Perusahaan</span>
                        <input name="company" value="{{ old('company', $user->company) }}">
                        @error('company') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>{{ $user->exists ? 'Password Baru Opsional' : 'Password' }}</span>
                        <input type="password" name="password" @if(! $user->exists) required @endif>
                        @error('password') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Konfirmasi Password</span>
                        <input type="password" name="password_confirmation" @if(! $user->exists) required @endif>
                    </label>
                </div>

                <div class="meta" style="margin-top:18px">
                    <button class="button" type="submit">Simpan User</button>
                </div>
            </form>

            @if($user->exists)
                <form class="card" method="POST" action="{{ route('admin.users.reset-password', $user) }}" style="margin-top:18px">
                    @csrf
                    @method('PUT')

                    <span class="eyebrow">Reset Password Manual</span>
                    <p class="muted">Admin dapat mengganti password user jika user lupa password. Setelah di-reset, informasikan password baru melalui kanal resmi.</p>

                    <div class="form-grid">
                        <label>
                            <span>Password Baru</span>
                            <input type="password" name="password" required>
                            @error('password') <small>{{ $message }}</small> @enderror
                        </label>

                        <label>
                            <span>Konfirmasi Password Baru</span>
                            <input type="password" name="password_confirmation" required>
                        </label>
                    </div>

                    <div class="meta" style="margin-top:18px">
                        <button class="button" type="submit" style="background:#f2633b">Reset Password</button>
                    </div>
                </form>
            @endif
        </section>
    </main>
@endsection
