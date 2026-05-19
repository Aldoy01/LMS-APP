@extends('layouts.lms', ['title' => 'Admin Course'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Admin LMS</span>
                    <h2>Manajemen Course</h2>
                </div>
                <a class="button" href="{{ route('admin.courses.create') }}">Tambah Course</a>
            </div>

            @if(session('status'))
                <div class="list-row" style="border-color:#0f766e;background:#eef6f5;margin-bottom:14px">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid courses">
                @forelse($courses as $course)
                    <article class="card">
                        <span class="eyebrow">{{ $course->level }} · {{ ucfirst($course->status) }}</span>
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->summary }}</p>
                        <div class="meta">
                            <span class="badge">{{ optional($course->mentor)->name ?? 'Mentor belum dipilih' }}</span>
                            <span class="badge">{{ $course->modules->count() }} modul</span>
                            <span class="badge">{{ $course->modules->sum(fn ($module) => $module->lessons->count()) }} lesson</span>
                        </div>
                        <div class="meta">
                            <a class="button" href="{{ route('admin.courses.edit', $course) }}">Edit</a>
                            <a class="button" style="background:#172033" href="{{ route('lms.courses.show', $course) }}">Preview</a>
                        </div>
                    </article>
                @empty
                    <div class="card">Belum ada course.</div>
                @endforelse
            </div>

            <div style="margin-top:18px">
                {{ $courses->links() }}
            </div>
        </section>
    </main>
@endsection
