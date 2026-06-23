@extends('layouts.lms', ['title' => 'FAQ - Trama Verse'])

@section('content')
<style>
    .faq-page {
        --faq-blue: #5f91ef;
        --faq-blue-deep: #3157dc;
        --faq-white: #ffffff;
        position: relative;
        overflow: hidden;
        min-height: 760px;
        padding: clamp(54px, 8vw, 96px) 0 clamp(68px, 9vw, 110px);
        color: var(--faq-white);
        background:
            radial-gradient(circle at 85% 8%, rgba(255,255,255,.16), transparent 22rem),
            radial-gradient(circle at 10% 95%, rgba(0,212,255,.14), transparent 24rem),
            linear-gradient(145deg, #709df0 0%, var(--faq-blue) 48%, #4d7ddd 100%);
    }
    .faq-page::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: .2;
        background-image:
            linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px);
        background-size: 52px 52px;
        mask-image: linear-gradient(180deg, rgba(0,0,0,.45), transparent 84%);
    }
    .faq-container {
        position: relative;
        z-index: 1;
        width: min(1120px, calc(100% - 36px));
        margin: 0 auto;
    }
    .faq-heading {
        max-width: 900px;
        margin: 0 auto clamp(42px, 7vw, 72px);
        text-align: center;
    }
    .faq-kicker {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 12px;
        color: #eaf2ff;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
    }
    .faq-kicker::before,
    .faq-kicker::after {
        width: 28px;
        height: 1px;
        content: "";
        background: rgba(255,255,255,.64);
    }
    .faq-heading h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(54px, 9vw, 112px);
        line-height: .94;
        letter-spacing: -.055em;
        text-shadow: 0 12px 32px rgba(24, 67, 160, .16);
    }
    .faq-heading p {
        max-width: 780px;
        margin: 24px auto 0;
        color: #f4f8ff;
        font-size: clamp(17px, 2.2vw, 25px);
        font-weight: 700;
        line-height: 1.45;
    }
    .faq-list {
        display: grid;
        border-top: 1px solid rgba(255,255,255,.32);
    }
    .faq-item {
        border-bottom: 1px solid rgba(255,255,255,.32);
    }
    .faq-item summary {
        position: relative;
        min-height: 88px;
        display: flex;
        align-items: center;
        padding: 22px 72px 22px 4px;
        color: #fff;
        cursor: pointer;
        list-style: none;
        font-size: clamp(17px, 2vw, 24px);
        font-weight: 900;
        line-height: 1.35;
        transition: color .2s ease, padding-left .2s ease;
    }
    .faq-item summary::-webkit-details-marker {
        display: none;
    }
    .faq-item summary::after {
        position: absolute;
        right: 8px;
        width: 32px;
        height: 32px;
        display: grid;
        place-items: center;
        border: 2px solid rgba(255,255,255,.9);
        border-radius: 50%;
        content: "+";
        color: #fff;
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
        transition: transform .22s ease, background .22s ease, color .22s ease;
    }
    .faq-item summary:hover,
    .faq-item summary:focus-visible {
        padding-left: 12px;
        color: #e7f8ff;
        outline: none;
    }
    .faq-item summary:hover::after,
    .faq-item summary:focus-visible::after {
        color: var(--faq-blue-deep);
        background: #fff;
    }
    .faq-item[open] summary::after {
        content: "−";
        color: var(--faq-blue-deep);
        background: #fff;
        transform: rotate(180deg);
    }
    .faq-answer {
        max-width: 940px;
        padding: 0 72px 28px 4px;
        color: #edf5ff;
        font-size: clamp(15px, 1.5vw, 18px);
        line-height: 1.75;
    }
    .faq-answer p {
        margin: 0;
    }
    @media (max-width: 680px) {
        .faq-page {
            padding-top: 44px;
        }
        .faq-container {
            width: min(100% - 24px, 1120px);
        }
        .faq-heading {
            margin-bottom: 38px;
        }
        .faq-heading h1 {
            font-size: clamp(50px, 19vw, 78px);
        }
        .faq-heading p {
            font-size: 16px;
        }
        .faq-item summary {
            min-height: 76px;
            padding: 18px 52px 18px 0;
            font-size: 16px;
        }
        .faq-item summary::after {
            right: 0;
            width: 28px;
            height: 28px;
            font-size: 20px;
        }
        .faq-item summary:hover,
        .faq-item summary:focus-visible {
            padding-left: 0;
        }
        .faq-answer {
            padding: 0 44px 24px 0;
            font-size: 15px;
        }
    }
</style>

<main class="faq-page">
    <div class="faq-container">
        <header class="faq-heading">
            <span class="faq-kicker">Pusat Bantuan</span>
            <h1>FAQ’s</h1>
            <p>Punya pertanyaan tentang Trama Verse? Tenang, kami sudah merangkum jawaban dari pertanyaan yang paling sering ditanyakan.</p>
        </header>

        <section class="faq-list" aria-label="Pertanyaan yang sering ditanyakan">
            @foreach($faqs as $index => $faq)
                <details class="faq-item" @if($index === 0) open @endif>
                    <summary>{{ $faq['question'] }}</summary>
                    <div class="faq-answer">
                        <p>{{ $faq['answer'] }}</p>
                    </div>
                </details>
            @endforeach
        </section>
    </div>
</main>
@endsection
