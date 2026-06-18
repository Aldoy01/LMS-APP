@extends('layouts.lms', ['title' => $pageTitle])

@section('content')
<main class="main">
    <section class="hero">
        <div>
            <span class="eyebrow">{{ $eyebrow }}</span>
            <h1>{{ $pageTitle }}</h1>
            <p>{{ $intro }}</p>
        </div>
        <div class="hero-panel">
            <span class="eyebrow">Trama Verse</span>
            <h3>Informasi layanan yang transparan.</h3>
            <p>Jika ada pertanyaan mengenai halaman ini, hubungi tim Trama Verse melalui informasi kontak pada footer.</p>
        </div>
    </section>

    <section class="section">
        <div class="grid courses">
            @foreach($sections as [$heading, $body])
                <article class="card">
                    <h3>{{ $heading }}</h3>
                    <p>{{ $body }}</p>
                </article>
            @endforeach
        </div>
    </section>
</main>
@endsection
