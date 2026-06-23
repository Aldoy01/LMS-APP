@php
    $faqItems = $faqs ?? config('faq', []);
    $faqDisplayMode = $faqMode ?? 'embedded';
    $faqHeading = $faqTitle ?? "FAQ's";
    $faqDescription = $faqSubtitle ?? 'Punya pertanyaan tentang Trama Verse? Tenang, kami sudah merangkum jawaban dari pertanyaan yang paling sering ditanyakan.';
    $visibleFaqItems = $faqDisplayMode === 'embedded'
        ? array_slice($faqItems, 0, 4)
        : $faqItems;
@endphp

@once
<style>
    .tv-faq {
        --tv-faq-blue: #4f7fe0;
        --tv-faq-deep: #234ec4;
        position: relative;
        overflow: hidden;
        color: #fff;
        background:
            radial-gradient(circle at 90% 0%, rgba(255,255,255,.18), transparent 18rem),
            radial-gradient(circle at 5% 100%, rgba(0,212,255,.13), transparent 20rem),
            linear-gradient(145deg, #6793ed 0%, var(--tv-faq-blue) 52%, #426fd1 100%);
    }
    .tv-faq.embedded {
        width: 100%;
        margin-top: 28px;
        padding: clamp(28px, 4vw, 44px);
        border: 1px solid rgba(255,255,255,.22);
        border-radius: 20px;
        box-shadow: 0 18px 38px rgba(30, 64, 175, .16);
    }
    .tv-faq.page {
        min-height: 640px;
        padding: clamp(44px, 6vw, 68px) 0 clamp(54px, 7vw, 82px);
    }
    .tv-faq::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: .12;
        background-image:
            linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px);
        background-size: 60px 60px;
        mask-image: linear-gradient(180deg, rgba(0,0,0,.45), transparent 84%);
    }
    .tv-faq-container {
        position: relative;
        z-index: 1;
        width: min(980px, calc(100% - 36px));
        margin: 0 auto;
    }
    .tv-faq.embedded .tv-faq-container {
        width: 100%;
    }
    .tv-faq-heading {
        max-width: 720px;
        margin: 0 auto clamp(24px, 4vw, 38px);
        text-align: center;
    }
    .tv-faq-heading h2 {
        margin: 0;
        color: #fff;
        font-size: clamp(36px, 5vw, 58px);
        line-height: 1;
        letter-spacing: -.035em;
        text-shadow: 0 10px 26px rgba(24, 67, 160, .14);
    }
    .tv-faq.embedded .tv-faq-heading h2 {
        font-size: clamp(32px, 4vw, 46px);
    }
    .tv-faq-heading p {
        max-width: 650px;
        margin: 12px auto 0;
        color: #edf4ff;
        font-size: clamp(14px, 1.5vw, 16px);
        font-weight: 600;
        line-height: 1.55;
    }
    .tv-faq-list {
        display: grid;
        gap: 8px;
    }
    .tv-faq-item {
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 12px;
        background: rgba(255,255,255,.09);
        backdrop-filter: blur(8px);
        transition: background .2s ease, border-color .2s ease, transform .2s ease;
    }
    .tv-faq-item:hover,
    .tv-faq-item[open] {
        border-color: rgba(255,255,255,.34);
        background: rgba(255,255,255,.14);
    }
    .tv-faq-item:hover {
        transform: translateY(-1px);
    }
    .tv-faq-item summary {
        position: relative;
        min-height: 58px;
        display: flex;
        align-items: center;
        padding: 14px 54px 14px 16px;
        color: #fff;
        cursor: pointer;
        list-style: none;
        font-size: clamp(14px, 1.4vw, 17px);
        font-weight: 800;
        line-height: 1.4;
    }
    .tv-faq-item summary::-webkit-details-marker {
        display: none;
    }
    .tv-faq-item summary::after {
        position: absolute;
        right: 14px;
        width: 26px;
        height: 26px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(255,255,255,.72);
        border-radius: 50%;
        content: "+";
        color: #fff;
        font-size: 18px;
        line-height: 1;
        transition: transform .2s ease, background .2s ease, color .2s ease;
    }
    .tv-faq-item summary:hover::after,
    .tv-faq-item summary:focus-visible::after,
    .tv-faq-item[open] summary::after {
        color: var(--tv-faq-deep);
        background: #fff;
    }
    .tv-faq-item[open] summary::after {
        content: "-";
        transform: rotate(180deg);
    }
    .tv-faq-answer {
        max-width: 860px;
        padding: 0 54px 16px 16px;
        color: #edf5ff;
        font-size: clamp(13px, 1.2vw, 15px);
        line-height: 1.65;
    }
    .tv-faq-answer p {
        margin: 0;
    }
    .tv-faq-more {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    .tv-faq-more a {
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 15px;
        border: 1px solid rgba(255,255,255,.7);
        border-radius: 999px;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        background: rgba(255,255,255,.1);
    }
    @media (max-width: 680px) {
        .tv-faq.embedded {
            padding: 26px 13px;
            border-radius: 16px;
        }
        .tv-faq.page {
            padding-top: 36px;
        }
        .tv-faq-container {
            width: min(100% - 20px, 980px);
        }
        .tv-faq-heading {
            margin-bottom: 24px;
        }
        .tv-faq-heading p {
            font-size: 13px;
        }
        .tv-faq-item summary {
            min-height: 56px;
            padding: 13px 44px 13px 13px;
            font-size: 14px;
        }
        .tv-faq-item summary::after {
            right: 12px;
            width: 24px;
            height: 24px;
            font-size: 17px;
        }
        .tv-faq-answer {
            padding: 0 42px 14px 13px;
            font-size: 13px;
        }
    }
</style>
@endonce

<section class="tv-faq {{ $faqDisplayMode }}" aria-label="Pertanyaan yang sering ditanyakan">
    <div class="tv-faq-container">
        <header class="tv-faq-heading">
            <h2>{{ $faqHeading }}</h2>
            <p>{{ $faqDescription }}</p>
        </header>

        <div class="tv-faq-list">
            @foreach($visibleFaqItems as $index => $faq)
                <details class="tv-faq-item" @if($index === 0 && ($faqOpenFirst ?? true)) open @endif>
                    <summary>{{ $faq['question'] }}</summary>
                    <div class="tv-faq-answer">
                        <p>{{ $faq['answer'] }}</p>
                    </div>
                </details>
            @endforeach
        </div>

        @if($faqDisplayMode === 'embedded')
            <div class="tv-faq-more">
                <a href="{{ route('faq') }}">Lihat Semua FAQ</a>
            </div>
        @endif
    </div>
</section>
