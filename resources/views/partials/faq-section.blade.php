@php
    $faqItems = $faqs ?? config('faq', []);
    $faqDisplayMode = $faqMode ?? 'embedded';
    $faqHeading = $faqTitle ?? "FAQ's";
    $faqDescription = $faqSubtitle ?? 'Punya pertanyaan tentang Trama Verse? Tenang, kami sudah merangkum jawaban dari pertanyaan yang paling sering ditanyakan.';
@endphp

@once
<style>
    .tv-faq {
        --tv-faq-blue: #5f91ef;
        --tv-faq-deep: #3157dc;
        position: relative;
        overflow: hidden;
        color: #fff;
        background:
            radial-gradient(circle at 85% 8%, rgba(255,255,255,.16), transparent 22rem),
            radial-gradient(circle at 10% 95%, rgba(0,212,255,.14), transparent 24rem),
            linear-gradient(145deg, #709df0 0%, var(--tv-faq-blue) 48%, #4d7ddd 100%);
    }
    .tv-faq.embedded {
        width: 100%;
        margin-top: 34px;
        padding: clamp(36px, 6vw, 64px) clamp(18px, 5vw, 62px);
        border: 1px solid rgba(255,255,255,.22);
        border-radius: 24px;
        box-shadow: 0 22px 48px rgba(30, 64, 175, .18);
    }
    .tv-faq.page {
        min-height: 760px;
        padding: clamp(54px, 8vw, 96px) 0 clamp(68px, 9vw, 110px);
    }
    .tv-faq::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: .18;
        background-image:
            linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px);
        background-size: 52px 52px;
        mask-image: linear-gradient(180deg, rgba(0,0,0,.45), transparent 84%);
    }
    .tv-faq-container {
        position: relative;
        z-index: 1;
        width: min(1120px, calc(100% - 36px));
        margin: 0 auto;
    }
    .tv-faq.embedded .tv-faq-container {
        width: 100%;
    }
    .tv-faq-heading {
        max-width: 900px;
        margin: 0 auto clamp(32px, 6vw, 60px);
        text-align: center;
    }
    .tv-faq-kicker {
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
    .tv-faq-heading h2 {
        margin: 0;
        color: #fff;
        font-size: clamp(45px, 8vw, 96px);
        line-height: .96;
        letter-spacing: -.05em;
        text-shadow: 0 12px 32px rgba(24, 67, 160, .16);
    }
    .tv-faq.embedded .tv-faq-heading h2 {
        font-size: clamp(38px, 6vw, 72px);
    }
    .tv-faq-heading p {
        max-width: 780px;
        margin: 20px auto 0;
        color: #f4f8ff;
        font-size: clamp(16px, 2vw, 21px);
        font-weight: 700;
        line-height: 1.5;
    }
    .tv-faq-list {
        display: grid;
        border-top: 1px solid rgba(255,255,255,.32);
    }
    .tv-faq-item {
        border-bottom: 1px solid rgba(255,255,255,.32);
    }
    .tv-faq-item summary {
        position: relative;
        min-height: 78px;
        display: flex;
        align-items: center;
        padding: 20px 64px 20px 4px;
        color: #fff;
        cursor: pointer;
        list-style: none;
        font-size: clamp(16px, 1.8vw, 22px);
        font-weight: 900;
        line-height: 1.35;
    }
    .tv-faq-item summary::-webkit-details-marker {
        display: none;
    }
    .tv-faq-item summary::after {
        position: absolute;
        right: 8px;
        width: 30px;
        height: 30px;
        display: grid;
        place-items: center;
        border: 2px solid rgba(255,255,255,.9);
        border-radius: 50%;
        content: "+";
        color: #fff;
        font-size: 22px;
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
        max-width: 940px;
        padding: 0 64px 26px 4px;
        color: #edf5ff;
        font-size: clamp(14px, 1.4vw, 17px);
        line-height: 1.75;
    }
    .tv-faq-answer p {
        margin: 0;
    }
    .tv-faq-more {
        display: flex;
        justify-content: center;
        margin-top: 28px;
    }
    .tv-faq-more a {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px;
        border: 1px solid rgba(255,255,255,.7);
        border-radius: 999px;
        color: #fff;
        font-size: 13px;
        font-weight: 900;
        background: rgba(255,255,255,.1);
    }
    @media (max-width: 680px) {
        .tv-faq.embedded {
            padding: 32px 16px;
            border-radius: 18px;
        }
        .tv-faq.page {
            padding-top: 44px;
        }
        .tv-faq-container {
            width: min(100% - 24px, 1120px);
        }
        .tv-faq-heading {
            margin-bottom: 30px;
        }
        .tv-faq-heading p {
            font-size: 15px;
        }
        .tv-faq-item summary {
            min-height: 70px;
            padding: 16px 46px 16px 0;
            font-size: 15px;
        }
        .tv-faq-item summary::after {
            right: 0;
            width: 27px;
            height: 27px;
            font-size: 19px;
        }
        .tv-faq-answer {
            padding: 0 40px 22px 0;
            font-size: 14px;
        }
    }
</style>
@endonce

<section class="tv-faq {{ $faqDisplayMode }}" aria-label="Pertanyaan yang sering ditanyakan">
    <div class="tv-faq-container">
        <header class="tv-faq-heading">
            <span class="tv-faq-kicker">Pusat Bantuan</span>
            <h2>{{ $faqHeading }}</h2>
            <p>{{ $faqDescription }}</p>
        </header>

        <div class="tv-faq-list">
            @foreach($faqItems as $index => $faq)
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
                <a href="{{ route('faq') }}">Buka Halaman FAQ Lengkap</a>
            </div>
        @endif
    </div>
</section>
