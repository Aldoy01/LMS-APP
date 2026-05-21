@extends('layouts.lms', ['title' => 'Preview Materi Bermasalah'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="card">
                <span class="eyebrow">Preview Materi Bermasalah</span>
                <h2>{{ $material->title }}</h2>
                <p class="muted">
                    LMS belum bisa membuka file ini. Biasanya karena file belum terupload sempurna,
                    migration storage belum jalan, atau file tersimpan di server lama setelah redeploy.
                </p>
                <div class="meta">
                    <a class="button" href="{{ url()->previous() }}">Kembali</a>
                    @auth
                        @if(in_array(optional(auth()->user()->role)->name, ['super-admin', 'admin-lms'], true) && optional(optional($material->lesson)->module)->course)
                            <a class="button" style="background:#172033" href="{{ route('admin.courses.materials.index', $material->lesson->module->course) }}">Upload Ulang Materi</a>
                        @endif
                    @endauth
                </div>
            </div>
        </section>
    </main>
@endsection
