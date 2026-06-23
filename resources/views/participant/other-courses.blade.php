@extends('layouts.lms', ['title' => 'Other Course - Trama Verse'])

@section('content')
<style>
    .other-course-page {
        --oc-blue: #204ecf;
        --oc-blue-dark: #07164d;
        --oc-cyan: #19bde8;
        --oc-purple: #6f34d7;
        width: min(1240px, calc(100% - 32px));
        margin: 0 auto;
        padding: 32px 0 64px;
    }
    .course-back {
        display: inline-flex;
        align-items: center;
        min-height: 40px;
        margin-bottom: 18px;
        color: var(--oc-blue);
        font-size: 14px;
        font-weight: 900;
    }
    .other-course-hero {
        display: grid;
        grid-template-columns: minmax(0, .92fr) minmax(0, 1.08fr);
        gap: clamp(28px, 5vw, 70px);
        align-items: center;
        padding: clamp(28px, 5vw, 58px);
        overflow: hidden;
        border: 1px solid rgba(47, 123, 255, .16);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 22px 52px rgba(25, 73, 190, .11);
    }
    .other-course-copy .welcome {
        display: inline-flex;
        margin-bottom: 12px;
        color: var(--oc-blue);
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .other-course-copy h1 {
        max-width: 720px;
        margin: 0;
        color: var(--oc-blue-dark);
        font-size: clamp(36px, 5vw, 66px);
        line-height: 1.03;
    }
    .other-course-copy .lead {
        margin: 22px 0 0;
        color: #33456d;
        font-size: 17px;
        line-height: 1.8;
    }
    .course-hero-visual {
        min-height: 390px;
        display: flex;
        align-items: end;
        overflow: hidden;
        border-radius: 14px;
        background:
            linear-gradient(180deg, rgba(7, 22, 77, .02), rgba(7, 22, 77, .18)),
            url('{{ asset('images/techverse-hero-bg.webp') }}') center / cover;
    }
    .visual-caption {
        width: 100%;
        padding: 22px;
        color: #fff;
        background: linear-gradient(180deg, transparent, rgba(7, 22, 77, .88));
    }
    .visual-caption strong {
        display: block;
        font-size: 24px;
    }
    .visual-caption span {
        display: block;
        margin-top: 5px;
        color: #dbeafe;
    }
    .course-catalog {
        margin-top: 56px;
    }
    .course-section-head {
        max-width: 820px;
        margin-bottom: 24px;
    }
    .course-section-head span {
        color: var(--oc-blue);
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .course-section-head h2 {
        margin: 7px 0 0;
        color: var(--oc-blue-dark);
        font-size: clamp(30px, 4vw, 48px);
        line-height: 1.12;
    }
    .course-filters {
        display: grid;
        grid-template-columns: repeat(2, minmax(180px, 250px)) auto;
        gap: 14px;
        align-items: end;
        margin-bottom: 26px;
        padding: 18px;
        border: 1px solid rgba(47, 123, 255, .16);
        border-radius: 12px;
        background: #fff;
    }
    .course-filters label {
        display: grid;
        gap: 7px;
        color: var(--oc-blue-dark);
        font-size: 13px;
        font-weight: 800;
    }
    .course-filters select {
        width: 100%;
        min-height: 44px;
        padding: 0 12px;
        border: 1px solid #cdd8ee;
        border-radius: 8px;
        color: var(--oc-blue-dark);
        background: #fff;
        font: inherit;
    }
    .filter-button {
        min-height: 44px;
        padding: 0 20px;
        border: 0;
        border-radius: 8px;
        color: #fff;
        background: var(--oc-blue);
        font-weight: 900;
        cursor: pointer;
    }
    .course-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
    }
    .course-card {
        display: flex;
        min-width: 0;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #dce4f2;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 14px 34px rgba(7, 22, 77, .09);
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .course-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 42px rgba(7, 22, 77, .14);
    }
    .course-cover {
        aspect-ratio: 16 / 8;
        overflow: hidden;
        background: linear-gradient(135deg, #0b64dd, #2838cb);
    }
    .course-cover img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }
    .course-cover-fallback {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        padding: 28px;
        color: #fff;
        text-align: center;
        background:
            linear-gradient(135deg, rgba(0, 212, 255, .24), transparent 55%),
            linear-gradient(135deg, #0b4fc8, #3427b7);
    }
    .course-cover-fallback strong {
        font-size: 21px;
        line-height: 1.2;
    }
    .course-card-body {
        display: flex;
        flex: 1;
        flex-direction: column;
        padding: 18px;
    }
    .course-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-bottom: 10px;
        border-bottom: 2px solid #5d82ef;
        color: #536383;
        font-size: 12px;
        font-weight: 800;
    }
    .course-card h3 {
        margin: 12px 0 8px;
        color: var(--oc-blue-dark);
        font-size: 18px;
        line-height: 1.3;
    }
    .course-summary {
        margin: 0;
        color: #5a6782;
        font-size: 14px;
        line-height: 1.55;
    }
    .course-price {
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-top: auto;
        padding-top: 18px;
        color: var(--oc-blue);
        font-size: 18px;
        font-weight: 900;
    }
    .course-price.free {
        color: #0f9d64;
    }
    .course-rating {
        margin: 8px 0 14px;
        color: #f59e0b;
        font-size: 15px;
        letter-spacing: 1px;
    }
    .course-card-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
    }
    .course-action,
    .course-card-actions button {
        width: 100%;
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 8px;
        color: #fff;
        background: var(--oc-blue);
        font: inherit;
        font-size: 13px;
        font-weight: 900;
        cursor: pointer;
    }
    .course-action.enrolled {
        background: #0f9d64;
    }
    .empty-course {
        grid-column: 1 / -1;
        padding: 34px;
        border: 1px dashed #bdcae1;
        border-radius: 12px;
        color: #536383;
        text-align: center;
        background: #fff;
    }
    @media (max-width: 980px) {
        .other-course-hero { grid-template-columns: 1fr; }
        .course-hero-visual { min-height: 300px; }
        .course-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 981px) and (max-width: 1180px) {
        .course-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 680px) {
        .other-course-page { width: min(100% - 20px, 1240px); padding-top: 20px; }
        .other-course-hero { padding: 22px; }
        .other-course-copy h1 { font-size: 36px; }
        .course-hero-visual { min-height: 230px; }
        .course-filters { grid-template-columns: 1fr; }
        .course-grid { grid-template-columns: 1fr; }
    }
</style>

<main class="other-course-page">
    <a class="course-back" href="{{ route('participant.dashboard') }}">Kembali ke Dashboard Peserta</a>

    <section class="other-course-hero">
        <div class="other-course-copy">
            <span class="welcome">Selamat datang di Trama Verse Course Program</span>
            <h1>Program Course Online untuk Meningkatkan Skill Digital dan Teknologi</h1>
            <p class="lead">
                Trama Verse Course adalah platform pembelajaran online yang menyediakan berbagai course teknologi
                untuk membantu kamu upgrade skill digital secara lebih mudah, terarah, dan praktis.
            </p>
            <p class="lead">
                Setiap course dirancang dengan materi yang sederhana, relevan, dan mudah diikuti, sehingga cocok
                untuk pemula, mahasiswa, profesional, maupun siapa saja yang ingin meningkatkan skill untuk
                kebutuhan kuliah, pekerjaan, bisnis, dan karir digital.
            </p>
        </div>
        <div class="course-hero-visual" role="img" aria-label="Pembelajaran teknologi bersama Trama Verse">
            <div class="visual-caption">
                <strong>Learn, Practice, Grow</strong>
                <span>Bangun skill digital melalui materi yang terarah dan praktis.</span>
            </div>
        </div>
    </section>

    <section class="course-catalog">
        <div class="course-section-head">
            <span>Course Catalogue</span>
            <h2>Temukan Course yang Cocok untuk Mulai Belajar di Trama Verse</h2>
        </div>

        <form class="course-filters" method="GET" action="{{ route('participant.other-courses') }}">
            <label>
                Kategori Kelas
                <select name="category">
                    <option value="">Semua</option>
                    @foreach($levels as $level)
                        <option value="{{ $level }}" @selected(request('category') === $level)>{{ $level }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                Jenis Harga
                <select name="price">
                    <option value="">Semua</option>
                    <option value="free" @selected(request('price') === 'free')>Gratis</option>
                    <option value="paid" @selected(request('price') === 'paid')>Berbayar</option>
                </select>
            </label>
            <button class="filter-button" type="submit">Tampilkan Course</button>
        </form>

        <div class="course-grid">
            @forelse($courses as $course)
                @php
                    $isEnrolled = in_array($course->id, $enrolledCourseIds, true);
                    $lessonCount = $course->modules->sum(fn ($module) => $module->lessons->count());
                @endphp
                <article class="course-card">
                    <div class="course-cover">
                        @if($course->cover_image)
                            <img src="{{ $course->cover_image }}" alt="{{ $course->title }}">
                        @else
                            <div class="course-cover-fallback"><strong>{{ $course->title }}</strong></div>
                        @endif
                    </div>
                    <div class="course-card-body">
                        <div class="course-meta">
                            <span>{{ $course->level }}</span>
                            <span>{{ $lessonCount }} lesson</span>
                        </div>
                        <h3>{{ $course->title }}</h3>
                        <p class="course-summary">{{ $course->summary }}</p>
                        <div class="course-price {{ (float) $course->price === 0.0 ? 'free' : '' }}">
                            {{ (float) $course->price === 0.0 ? 'Gratis' : 'Rp' . number_format($course->price, 0, ',', '.') }}
                        </div>
                        <div class="course-rating" aria-label="Course unggulan">★★★★★</div>
                        <div class="course-card-actions">
                            @if($isEnrolled)
                                <a class="course-action enrolled" href="{{ route('lms.courses.show', $course) }}">Lanjut Belajar</a>
                            @else
                                <form method="POST" action="{{ route('purchase.order', $course) }}">
                                    @csrf
                                    <button type="submit">{{ (float) $course->price === 0.0 ? 'Ambil Course' : 'Pilih Course' }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-course">
                    Course dengan kategori atau jenis harga tersebut belum tersedia.
                </div>
            @endforelse
        </div>
    </section>
</main>
@endsection
