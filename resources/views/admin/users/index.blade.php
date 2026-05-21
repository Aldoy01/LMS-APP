@extends('layouts.lms', ['title' => 'User & Akses'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Management System</span>
                    <h2>User & Akses Akun</h2>
                </div>
                <a class="button" href="{{ route('admin.users.create') }}">Tambah User</a>
            </div>

            @if(session('status'))
                <div class="list-row" style="border-color:var(--teal);background:var(--teal-soft);margin-bottom:14px">
                    {{ session('status') }}
                </div>
            @endif

            <div class="list">
                @forelse($users as $user)
                    <article class="list-row">
                        <div class="section-head" style="margin-bottom:0;align-items:center">
                            <div>
                                <strong>{{ $user->name }}</strong>
                                <div class="muted">{{ $user->email }}</div>
                                <div class="meta">
                                    <span class="badge">{{ optional($user->role)->label ?? 'Role belum dipilih' }}</span>
                                    @if($user->company)
                                        <span class="badge">{{ $user->company }}</span>
                                    @endif
                                </div>
                            </div>
                            <a class="button" href="{{ route('admin.users.edit', $user) }}">Kelola Akun</a>
                        </div>
                    </article>
                @empty
                    <div class="card">Belum ada user.</div>
                @endforelse
            </div>

            <div style="margin-top:18px">
                {{ $users->links() }}
            </div>
        </section>
    </main>
@endsection
