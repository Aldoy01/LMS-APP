@extends('layouts.lms', ['title' => 'Materi Belum Tersedia'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="card">
                <span class="eyebrow">Materi Belum Tersedia</span>
                <h2>{{ $material->title }}</h2>
                <p class="muted">
                    File materi belum ditemukan di storage LMS. Jika ini materi lama dari data demo, admin perlu
                    upload ulang PDF/video atau mengganti link resource dari menu Kelola Materi.
                </p>
                <div class="meta">
                    <a class="button" href="{{ url()->previous() }}">Kembali</a>
                    @auth
                        @if(in_array(optional(auth()->user()->role)->name, ['super-admin', 'admin-lms'], true) && optional(optional($material->lesson)->module)->course)
                            <a class="button" style="background:var(--night)" href="{{ route('admin.courses.materials.index', $material->lesson->module->course) }}">Kelola Materi</a>
                        @endif
                    @endauth
                </div>
            </div>
        </section>
    </main>
@endsection
