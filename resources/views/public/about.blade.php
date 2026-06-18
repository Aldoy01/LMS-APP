@extends('layouts.lms', ['title' => 'About Trama Verse'])

@section('content')
<main class="main">
    <section class="hero">
        <div>
            <span class="eyebrow">About Trama Verse</span>
            <h1>Learning Space untuk Bertumbuh di Dunia Teknologi</h1>
            <p>Trama Verse menghadirkan pengalaman belajar yang terarah melalui course, praktik, mentor, dan komunitas yang saling mendukung.</p>
        </div>
        <div class="hero-panel">
            <span class="eyebrow">Learn · Explore · Grow</span>
            <h3>Kami percaya skill dibangun lewat proses.</h3>
            <p>Karena itu pembelajaran dirancang agar peserta memahami konsep, mencoba praktik, dan terus mengembangkan kemampuannya.</p>
        </div>
    </section>

    <section class="section">
        <div class="section-head"><div><span class="eyebrow">Our Direction</span><h2>Apa yang Kami Bangun</h2></div></div>
        <div class="grid courses">
            <article class="card"><h3>Pembelajaran Terarah</h3><p>Roadmap, modul, dan lesson disusun agar peserta dapat belajar secara bertahap dan terukur.</p></article>
            <article class="card"><h3>Praktik yang Relevan</h3><p>Materi diarahkan pada pemahaman dan praktik yang dapat diterapkan pada kebutuhan nyata.</p></article>
            <article class="card"><h3>Komunitas Bertumbuh</h3><p>Peserta memperoleh ruang berdiskusi, bertanya, dan berbagi insight bersama.</p></article>
            <article class="card"><h3>Dukungan Berkelanjutan</h3><p>Admin dan mentor membantu peserta menangani kendala akses maupun proses belajar.</p></article>
        </div>
    </section>

    <section class="section">
        <div class="card">
            <span class="eyebrow">Mulai Belajar</span>
            <h2>Siap memilih programmu?</h2>
            <p>Lihat seluruh course, struktur modul, serta agenda webinar dan event yang tersedia.</p>
            <a class="button" href="{{ route('programs.index') }}">Lihat Program</a>
        </div>
    </section>
</main>
@endsection
