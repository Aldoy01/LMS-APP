@extends('layouts.lms', ['title' => 'Dashboard LMS Cyber Security Playbook'])

@section('content')
    <section class="hero">
        <div>
            <span class="eyebrow" style="color:var(--gold)">MVP Learning Management System</span>
            <h1>Techverse Learning LMS</h1>
            <p>
                Platform pembelajaran premium yang menggabungkan e-book, video learning, live Q&A,
                case review, checklist SOP, dan CRM upsell layanan cyber security.
            </p>
            <div class="chips">
                <span class="chip">Laravel</span>
                <span class="chip">PostgreSQL</span>
                <span class="chip">Redis Queue</span>
                <span class="chip">Manual Checkout</span>
                <span class="chip">CRM Pipeline</span>
            </div>
        </div>
        <div class="hero-panel">
            <img class="hero-logo" src="{{ asset('images/techverse-learning-logo.jpeg') }}" alt="Techverse Learning">
            <strong>Fokus MVP</strong>
            <div class="list">
                <span>Course management dan materi digital</span>
                <span>Dashboard peserta, admin, mentor, sales/CS</span>
                <span>Live Q&A, case review, reporting dasar</span>
            </div>
        </div>
    </section>

    <main class="main">
        <section class="grid metrics" id="reports">
            <div class="metric"><span>Course Aktif</span><strong>{{ $metrics['courses'] }}</strong></div>
            <div class="metric"><span>Peserta</span><strong>{{ $metrics['participants'] }}</strong></div>
            <div class="metric"><span>Revenue Paid</span><strong>Rp{{ number_format($metrics['revenue'], 0, ',', '.') }}</strong></div>
            <div class="metric"><span>Closed Won</span><strong>{{ $metrics['conversion'] }}</strong></div>
        </section>

        <section class="section" id="courses">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Course Management</span>
                    <h2>Program Pembelajaran</h2>
                </div>
            </div>
            <div class="grid courses">
                @forelse($courses as $course)
                    <article class="card">
                        <span class="eyebrow">{{ $course->level }} · {{ ucfirst($course->status) }}</span>
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->summary }}</p>
                        <div class="meta">
                            <span class="badge">{{ $course->modules->count() }} modul</span>
                            <span class="badge">{{ $course->modules->sum(fn ($module) => $module->lessons->count()) }} lesson</span>
                            <span class="badge">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                        </div>
                        <a class="button" href="{{ route('lms.courses.show', $course) }}">Buka Course</a>
                    </article>
                @empty
                    <div class="card">Belum ada course. Jalankan seeder untuk data demo.</div>
                @endforelse
            </div>
        </section>

        <section class="section grid split" id="sessions">
            <div>
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Mentor / Expert</span>
                        <h2>Live Q&A Terdekat</h2>
                    </div>
                </div>
                <div class="list">
                    @foreach($liveSessions as $session)
                        <div class="list-row">
                            <strong>{{ $session->title }}</strong>
                            <span class="muted">{{ optional($session->starts_at)->format('d M Y H:i') }} · {{ optional($session->course)->title }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div>
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Peserta</span>
                        <h2>Pertanyaan Masuk</h2>
                    </div>
                </div>
                <div class="list">
                    @foreach($questions as $question)
                        <div class="list-row">
                            <strong>{{ $question->subject }}</strong>
                            <span class="muted">{{ ucfirst($question->priority) }} · {{ ucfirst($question->status) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section grid split" id="crm">
            <div>
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Case Review</span>
                        <h2>Risk & Rekomendasi</h2>
                    </div>
                </div>
                <div class="list">
                    @foreach($caseReviews as $case)
                        <div class="list-row">
                            <strong>{{ $case->business_name }} · {{ $case->topic }}</strong>
                            <span class="muted">Risk level: <span class="{{ $case->risk_level === 'high' ? 'risk-high' : '' }}">{{ strtoupper($case->risk_level) }}</span></span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div>
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Sales / CS</span>
                        <h2>Pipeline Upsell</h2>
                    </div>
                </div>
                <div class="pipeline">
                    @foreach($leads as $lead)
                        <div class="list-row stage">
                            <strong>{{ $lead->company }}</strong>
                            <span class="muted">{{ $lead->pipeline_stage }}</span><br>
                            <span class="muted">{{ $lead->service_interest }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>
@endsection
