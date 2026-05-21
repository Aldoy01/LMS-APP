@extends('layouts.lms', ['title' => $lesson->title])

@section('content')
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">{{ $lesson->module->category }} / {{ $course->title }}</span>
                    <h2>{{ $lesson->title }}</h2>
                </div>
                <a class="button" style="background:#172033" href="{{ route('participant.dashboard') }}">Kembali ke Dashboard</a>
            </div>

            <div class="grid split">
                <div class="card">
                    <span class="eyebrow">Materi Utama</span>
                    <p>{{ $lesson->summary }}</p>
                    <div class="meta">
                        <span class="badge">{{ ucfirst($lesson->content_type) }}</span>
                        <span class="badge">{{ $lesson->duration_minutes }} menit</span>
                    </div>

                    @foreach($lesson->materials as $material)
                        @if(in_array($material->type, ['pdf', 'pdf-slide'], true))
                            <div class="list-row" style="margin-top:12px">
                                <strong>{{ $material->title }}</strong>
                                <p class="muted">PDF dapat dibuka langsung atau diunduh sesuai kebutuhan belajar.</p>
                                <div class="meta">
                                    <a class="button" href="{{ route('materials.show', $material) }}" target="_blank" rel="noopener">Buka PDF</a>
                                </div>
                            </div>
                        @elseif($material->type === 'video-upload')
                            <div class="list-row" style="margin-top:12px">
                                <strong>{{ $material->title }}</strong>
                                <video controls style="width:100%;margin-top:10px;border-radius:8px;background:#000">
                                    <source src="{{ route('materials.show', $material) }}">
                                </video>
                            </div>
                        @elseif($material->type === 'video-embed')
                            <div class="list-row" style="margin-top:12px">
                                <strong>{{ $material->title }}</strong>
                                <iframe src="{{ $material->url }}" style="width:100%;aspect-ratio:16/9;border:0;border-radius:8px;margin-top:10px" allowfullscreen></iframe>
                            </div>
                        @endif
                    @endforeach
                </div>

                <aside class="card">
                    <span class="eyebrow">Tools & Resource</span>
                    <div class="list" style="margin-top:12px">
                        @forelse($lesson->materials->whereIn('type', ['tool', 'resource']) as $material)
                            <div class="list-row">
                                <strong>{{ $material->title }}</strong>
                                <div class="meta">
                                    <a class="button" href="{{ route('materials.show', $material) }}" target="_blank" rel="noopener">Buka Link</a>
                                </div>
                            </div>
                        @empty
                            <div class="list-row">
                                <strong>Belum ada tools tambahan</strong>
                                <span class="muted">Admin dapat menambahkan tools list dan resource dari dashboard admin.</span>
                            </div>
                        @endforelse
                    </div>
                </aside>
            </div>

            <div class="meta" style="margin-top:18px;justify-content:space-between">
                @if($previousLesson)
                    <a class="button" style="background:#172033" href="{{ route('lms.lessons.show', [$course, $previousLesson]) }}">Previous</a>
                @else
                    <span></span>
                @endif

                @if($nextLesson)
                    <a class="button" href="{{ route('lms.lessons.show', [$course, $nextLesson]) }}">Next</a>
                @else
                    <a class="button" href="{{ route('participant.dashboard') }}">Selesai & Kembali</a>
                @endif
            </div>
        </section>
    </main>
@endsection
