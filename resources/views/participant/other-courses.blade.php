@extends('layouts.lms', ['title' => 'Other Course - Trama Verse'])

@section('content')
<style>
    .other-course-page {
        --catalog-blue: #204ecf;
        --catalog-night: #07164d;
        width: min(1240px, calc(100% - 32px));
        margin: 0 auto;
        padding: 32px 0 64px;
    }
    .course-back {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        margin-bottom: 18px;
        color: var(--catalog-blue);
        font-size: 14px;
        font-weight: 900;
    }
    .catalog-hero {
        display: grid;
        grid-template-columns: minmax(0, .95fr) minmax(0, 1.05fr);
        gap: clamp(28px, 5vw, 68px);
        align-items: center;
        padding: clamp(28px, 5vw, 58px);
        overflow: hidden;
        border: 1px solid rgba(47, 123, 255, .16);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 22px 52px rgba(25, 73, 190, .11);
    }
    .catalog-copy .eyebrow {
        color: var(--catalog-blue);
    }
    .catalog-copy h1 {
        margin: 10px 0 0;
        color: var(--catalog-night);
        font-size: clamp(36px, 5vw, 64px);
        line-height: 1.04;
    }
    .catalog-copy p {
        margin: 20px 0 0;
        color: #405174;
        font-size: 16px;
        line-height: 1.75;
    }
    .catalog-visual {
        min-height: 390px;
        display: flex;
        align-items: end;
        overflow: hidden;
        border-radius: 16px;
        background:
            linear-gradient(180deg, rgba(7, 22, 77, .03), rgba(7, 22, 77, .25)),
            url('{{ asset('images/techverse-hero-bg.webp') }}') center / cover;
    }
    .catalog-visual-caption {
        width: 100%;
        padding: 24px;
        color: #fff;
        background: linear-gradient(180deg, transparent, rgba(7, 22, 77, .9));
    }
    .catalog-visual-caption strong {
        display: block;
        font-size: 25px;
    }
    .catalog-visual-caption span {
        display: block;
        margin-top: 5px;
        color: #dbeafe;
    }
    .catalog-section {
        margin-top: 54px;
    }
    .catalog-section-head {
        max-width: 820px;
        margin-bottom: 24px;
    }
    .catalog-section-head span {
        color: var(--catalog-blue);
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .catalog-section-head h2 {
        margin: 7px 0 0;
        color: var(--catalog-night);
        font-size: clamp(30px, 4vw, 46px);
        line-height: 1.12;
    }
    .catalog-section-head p {
        margin: 12px 0 0;
        color: #5a6782;
        font-size: 15px;
        line-height: 1.65;
    }
    .catalog-filters {
        display: grid;
        grid-template-columns: repeat(2, minmax(180px, 280px)) auto;
        gap: 14px;
        align-items: end;
        margin-bottom: 28px;
        padding: 20px;
        border: 1px solid rgba(47, 123, 255, .16);
        border-radius: 14px;
        background: linear-gradient(135deg, #fff, #f5f8ff);
        box-shadow: 0 12px 28px rgba(7, 22, 77, .06);
    }
    .filter-heading {
        grid-column: 1 / -1;
        padding-bottom: 12px;
        border-bottom: 1px solid #dce5f5;
        color: var(--catalog-night);
        font-size: 16px;
        font-weight: 900;
    }
    .catalog-filters label {
        display: grid;
        gap: 7px;
        color: var(--catalog-night);
        font-size: 13px;
        font-weight: 800;
    }
    .catalog-filters select {
        width: 100%;
        min-height: 45px;
        padding: 0 12px;
        border: 1px solid #cdd8ee;
        border-radius: 9px;
        color: var(--catalog-night);
        background: #fff;
        font: inherit;
    }
    .filter-button {
        min-height: 45px;
        padding: 0 22px;
        border: 0;
        border-radius: 9px;
        color: #fff;
        background: linear-gradient(135deg, #204ecf, #6236d5);
        font-weight: 900;
        cursor: pointer;
    }
    .course-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }
    .course-card {
        min-width: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #dce4f2;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 14px 34px rgba(7, 22, 77, .09);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 22px 46px rgba(7, 22, 77, .14);
    }
    .course-cover {
        aspect-ratio: 16 / 8;
        overflow: hidden;
        background: linear-gradient(135deg, #0b64dd, #3427b7);
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
        font-size: 22px;
        line-height: 1.2;
    }
    .course-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 20px;
    }
    .course-body h3 {
        margin: 0 0 8px;
        color: var(--catalog-night);
        font-size: 21px;
        line-height: 1.25;
    }
    .course-summary {
        margin: 0;
        color: #5a6782;
        font-size: 14px;
        line-height: 1.55;
    }
    .course-pricing {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: 18px;
    }
    .course-original-price {
        color: #8b96ad;
        font-size: 13px;
        font-weight: 700;
        text-decoration: line-through;
    }
    .course-price {
        color: var(--catalog-blue);
        font-size: 22px;
        font-weight: 900;
    }
    .course-price.free {
        color: #0f9d64;
    }
    .course-features {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin: 17px 0;
    }
    .course-feature {
        min-height: 24px;
        display: inline-flex;
        align-items: center;
        padding: 5px 8px;
        border: 1px solid rgba(47, 123, 255, .12);
        border-radius: 999px;
        color: #3157dc;
        background: rgba(47, 123, 255, .08);
        font-size: 10px;
        font-weight: 900;
        line-height: 1;
    }
    .course-actions {
        margin-top: auto;
    }
    .course-actions form {
        margin: 0;
    }
    .course-action,
    .course-actions button {
        width: 100%;
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 9px;
        color: #fff;
        background: linear-gradient(135deg, #204ecf, #6236d5);
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
        padding: 36px;
        border: 1px dashed #bdcae1;
        border-radius: 14px;
        color: #536383;
        text-align: center;
        background: #fff;
    }
    @media (max-width: 980px) {
        .catalog-hero { grid-template-columns: 1fr; }
        .catalog-visual { min-height: 300px; }
        .course-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 680px) {
        .other-course-page { width: min(100% - 20px, 1240px); padding-top: 20px; }
        .catalog-hero { padding: 22px; }
        .catalog-copy h1 { font-size: 36px; }
        .catalog-visual { min-height: 230px; }
        .catalog-filters { grid-template-columns: 1fr; }
        .course-grid { grid-template-columns: 1fr; }
    }
</style>

<main class="other-course-page">
    <a class="course-back" href="{{ route('participant.dashboard') }}">← Kembali ke Dashboard Peserta</a>

    <section class="catalog-hero">
        <div class="catalog-copy">
            <span class="eyebrow">Trama Verse Course Program</span>
            <h1>Upgrade Skill Digital dan Teknologi</h1>
            <p>
                Pilih kelas teknologi yang sesuai dengan tujuan belajar, pekerjaan, bisnis, dan perkembangan karier.
                Setiap course disusun agar mudah dipahami dan tetap berorientasi pada praktik.
            </p>
        </div>
        <div class="catalog-visual" role="img" aria-label="Pembelajaran teknologi bersama Trama Verse">
            <div class="catalog-visual-caption">
                <strong>Learn, Practice, Grow</strong>
                <span>Bangun skill digital melalui materi yang terarah dan praktis.</span>
            </div>
        </div>
    </section>

    <section class="catalog-section">
        <div class="catalog-section-head">
            <span>Course Catalogue</span>
            <h2>Temukan Course yang Cocok untuk Kamu</h2>
            <p>Tingkatkan skill Anda melalui course praktis yang mudah dipelajari dan relevan untuk dunia kerja.</p>
        </div>

        <form class="catalog-filters" method="GET" action="{{ route('participant.other-courses') }}">
            <div class="filter-heading">Tabel Pilihan Course</div>
            <label>
                Kategori Kelas
                <select name="category">
                    <option value="">Semua</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
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
                    $moduleCount = $course->modules->count();
                    $lessonCount = $course->modules->sum(fn ($module) => $module->lessons->count());
                    $labCount = $course->modules->sum(fn ($module) => $module->lessons->where('content_type', 'lab')->count());
                    $quizCount = $course->modules->sum(fn ($module) => $module->lessons->where('content_type', 'quiz')->count());
                    $showOriginalPrice = $course->original_price && $course->original_price > $course->price;
                @endphp
                <article class="course-card">
                    <div class="course-cover">
                        @if($course->cover_image)
                            <img src="{{ $course->cover_image }}" alt="{{ $course->title }}">
                        @else
                            <div class="course-cover-fallback"><strong>{{ $course->title }}</strong></div>
                        @endif
                    </div>
                    <div class="course-body">
                        <h3>{{ $course->title }}</h3>
                        <p class="course-summary">{{ $course->summary }}</p>

                        <div class="course-pricing">
                            @if($showOriginalPrice)
                                <span class="course-original-price">Rp{{ number_format($course->original_price, 0, ',', '.') }}</span>
                            @endif
                            <span class="course-price {{ (float) $course->price === 0.0 ? 'free' : '' }}">
                                {{ (float) $course->price === 0.0 ? 'Gratis' : 'Rp' . number_format($course->price, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="course-features">
                            <span class="course-feature">{{ $moduleCount }} Modul</span>
                            <span class="course-feature">{{ $lessonCount }} Lesson</span>
                            <span class="course-feature">{{ $labCount }} Lab Practice</span>
                            <span class="course-feature">{{ $quizCount }} Quiz</span>
                        </div>

                        <div class="course-actions">
                            @if($isEnrolled)
                                <a class="course-action enrolled" href="{{ route('lms.courses.show', $course) }}">Lanjut Belajar</a>
                            @else
                                <form method="POST" action="{{ route('purchase.order', $course) }}">
                                    @csrf
                                    <button type="submit">Order</button>
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

    @include('partials.faq-section', [
        'faqMode' => 'embedded',
        'faqTitle' => "FAQ's",
        'faqOpenFirst' => false,
    ])
</main>
@endsection
