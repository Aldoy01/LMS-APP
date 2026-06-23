@php
    $footerSettings = $siteSettings ?? \App\Models\SiteSetting::DEFAULTS;
    $footerName = $footerSettings['site_name'] ?? 'Trama Verse';
    $footerLogo = $footerSettings['logo_url'] ?: asset('images/trama-verse-logo.png');
    $footerPhone = $footerSettings['contact_whatsapp'] ?? '08513332305';
    $footerPhoneTarget = preg_replace('/\D+/', '', $footerPhone);
    $footerPhoneTarget = str_starts_with($footerPhoneTarget, '0') ? '62' . substr($footerPhoneTarget, 1) : $footerPhoneTarget;
    $footerEmail = $footerSettings['contact_email'] ?? 'admin@tramaverse.test';
    $embeddedFooter = ($footerMode ?? 'public') === 'participant';
@endphp

@once
<style>
    .site-footer-card{width:min(1180px,calc(100% - 32px));margin:30px auto 0;padding:30px 30px 18px;overflow:hidden;border:1px solid rgba(47,123,255,.14);border-radius:18px 18px 0 0;background:radial-gradient(circle at 88% 12%,rgba(66,200,236,.16),transparent 17rem),radial-gradient(circle at 20% 100%,rgba(49,87,220,.13),transparent 18rem),#fff;box-shadow:0 14px 34px rgba(16,85,245,.08)}
    .site-footer-card.embedded{width:100%;margin-top:36px;border-color:rgba(96,165,250,.3);border-radius:22px;background:radial-gradient(circle at 88% 8%,rgba(0,212,255,.24),transparent 20rem),radial-gradient(circle at 8% 100%,rgba(125,22,184,.2),transparent 22rem),linear-gradient(135deg,#07164d 0%,#123b8f 58%,#1d4ed8 100%);box-shadow:0 22px 50px rgba(7,22,77,.22)}
    .site-footer-card.embedded .site-footer-brand p,.site-footer-card.embedded .site-footer-column a,.site-footer-card.embedded .site-footer-handle,.site-footer-card.embedded .site-footer-bottom{color:#dbeafe}
    .site-footer-card.embedded .site-footer-column h2,.site-footer-card.embedded .site-footer-contact strong,.site-footer-card.embedded .site-footer-contact svg,.site-footer-card.embedded .site-footer-motto{color:#67e8f9}
    .site-footer-card.embedded .site-footer-contact{border-bottom-color:rgba(219,234,254,.3)}
    .site-footer-card.embedded .site-footer-column a:hover,.site-footer-card.embedded .site-footer-column a:focus-visible{color:#67e8f9}
    .site-footer-card.embedded .site-footer-socials a{color:#fff;border-color:rgba(255,255,255,.2);background:rgba(255,255,255,.1)}
    .site-footer-card.embedded .site-footer-bottom{border-top-color:rgba(219,234,254,.18)}
    .site-footer-kicker{grid-column:1/-1;display:flex;align-items:center;justify-content:space-between;gap:18px;padding-bottom:18px;border-bottom:1px solid rgba(219,234,254,.18)}
    .site-footer-kicker span{display:inline-flex;align-items:center;gap:8px;color:#67e8f9;font-size:12px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}
    .site-footer-kicker span::before{content:"";width:8px;height:8px;border-radius:50%;background:#34d399;box-shadow:0 0 0 5px rgba(52,211,153,.14)}
    .site-footer-kicker strong{color:#fff;font-size:clamp(17px,2vw,23px)}
    .site-footer-grid{display:grid;grid-template-columns:1fr .7fr .8fr 1.5fr .72fr;gap:28px;align-items:start}
    .site-footer-brand img{width:min(180px,100%);height:62px;display:block;object-fit:contain;object-position:left center}
    .site-footer-brand p{max-width:210px;margin:12px 0 0;color:#4b587c;font-size:12px;font-weight:700;line-height:1.6}
    .site-footer-column h2{margin:0 0 10px;color:#137bb2;font-size:17px}
    .site-footer-column a{display:block;margin:0 0 7px;color:#374151;font-size:12px;font-weight:700;line-height:1.5;text-align:left}
    .site-footer-column a:hover,.site-footer-column a:focus-visible{color:var(--brand-dark,#3157dc)}
    .site-footer-contact{display:flex!important;align-items:center;gap:8px;padding-bottom:9px;border-bottom:1px dashed rgba(55,65,81,.3)}
    .site-footer-contact svg{width:17px;height:17px;flex:0 0 auto;color:#137bb2}
    .site-footer-contact strong{color:#137bb2;font-size:13px}
    .site-footer-socials{display:flex;flex-wrap:wrap;align-items:center;gap:9px;margin-top:10px}
    .site-footer-socials a{width:30px;height:30px;display:grid;place-items:center;margin:0;border:1px solid rgba(47,123,255,.1);border-radius:8px;background:rgba(255,255,255,.76)}
    .site-footer-socials a:hover,.site-footer-socials a:focus-visible{color:#fff;background:linear-gradient(145deg,#42c8ec,#3157dc,#7d16b8)}
    .site-footer-socials svg{width:16px;height:16px}
    .site-footer-handle{color:#4b587c;font-size:12px;font-weight:800}
    .site-footer-motto{display:grid;grid-template-columns:auto 1fr;gap:10px;align-items:center;color:#137bb2}
    .site-footer-brace{font-size:72px;font-weight:300;line-height:.9}
    .site-footer-motto strong{display:block;font-size:20px;line-height:1.45}
    .site-footer-bottom{display:flex;justify-content:space-between;gap:18px;margin-top:28px;padding-top:13px;border-top:1px solid rgba(47,123,255,.1);color:#4b587c;font-size:10px;font-weight:700}
    @media(max-width:900px){.site-footer-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:560px){.site-footer-card{width:min(100% - 18px,1180px);padding:22px 18px 16px}.site-footer-card.embedded{width:100%}.site-footer-grid{grid-template-columns:1fr;gap:20px}.site-footer-kicker{align-items:flex-start;flex-direction:column}.site-footer-motto{max-width:180px}.site-footer-bottom{flex-direction:column;gap:5px}}
</style>
@endonce

<section class="site-footer-card {{ $embeddedFooter ? 'embedded' : '' }}" aria-label="Footer {{ $footerName }}">
    <div class="site-footer-grid">
        @if($embeddedFooter)
            <div class="site-footer-kicker">
                <span>Participant Learning Hub</span>
                <strong>Terus belajar, progresmu sedang bertumbuh.</strong>
            </div>
        @endif
        <div class="site-footer-brand">
            <a href="{{ route('lms.dashboard') }}"><img src="{{ $footerLogo }}" alt="{{ $footerName }}"></a>
            <p>Ruang belajar digital untuk membangun skill, mengeksplorasi teknologi, dan tumbuh bersama komunitas.</p>
        </div>
        <div class="site-footer-column">
            <h2>Program</h2>
            <a href="{{ route('programs.index') }}#course">Course</a>
            <a href="{{ route('programs.index') }}#module">Learning Module</a>
            <a href="{{ route('programs.index') }}#event">Webinar &amp; Event</a>
        </div>
        <div class="site-footer-column">
            <h2>About</h2>
            <a href="{{ route('about') }}">About {{ $footerName }}</a>
            <a href="{{ route('faq') }}">FAQ</a>
            <a href="{{ route('privacy') }}">Privacy Policy</a>
            <a href="{{ route('terms') }}">Terms &amp; Conditions</a>
        </div>
        <div class="site-footer-column">
            <h2>Contact &amp; Follow Us</h2>
            <a class="site-footer-contact" href="https://wa.me/{{ $footerPhoneTarget }}" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 20l1.4-4.2A8 8 0 1 1 8.2 18z"/><path d="M9 9.5c.4 2 2 3.6 4 4l1.4-1.4"/></svg>
                <span>{{ $footerName }} Official: <strong>{{ $footerPhone }}</strong></span>
            </a>
            <a href="mailto:{{ $footerEmail }}">{{ $footerEmail }}</a>
            <div class="site-footer-socials" aria-label="Media sosial {{ $footerName }}">
                <a href="https://instagram.com/tramaverse" target="_blank" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg></a>
                <a href="https://tiktok.com/@tramaverse" target="_blank" rel="noopener" aria-label="TikTok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 4v10a4 4 0 1 1-4-4"/><path d="M14 4c1 3 3 4 6 4"/></svg></a>
                <a href="https://linkedin.com/company/tramaverse" target="_blank" rel="noopener" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="9" width="4" height="12"/><path d="M5 3v.01"/><path d="M11 21V9h4v2c1-2 6-3 6 3v7"/></svg></a>
                <a href="https://youtube.com/@tramaverse" target="_blank" rel="noopener" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12c0 3-.4 5-1 6-1 1-5 1-9 1s-8 0-9-1c-.6-1-1-3-1-6s.4-5 1-6c1-1 5-1 9-1s8 0 9 1c.6 1 1 3 1 6z"/><path d="m10 9 5 3-5 3z"/></svg></a>
                <span class="site-footer-handle">tramaverse</span>
            </div>
        </div>
        <div class="site-footer-motto"><span class="site-footer-brace" aria-hidden="true">{</span><div><strong>Learn.</strong><strong>Explore.</strong><strong>Grow.</strong></div></div>
    </div>
    <div class="site-footer-bottom"><span>&copy; {{ now()->year }} {{ $footerName }}. All Rights Reserved.</span><span>Learn securely. Grow continuously.</span></div>
</section>
