@php
    $forumGroups = collect($discussionGroups ?? []);
    $telegramGroup = $forumGroups->firstWhere('name', 'Telegram Community')
        ?? $forumGroups->firstWhere('class', 'telegram');
    $discordGroup = $forumGroups->firstWhere('name', 'Discord Lab Room')
        ?? $forumGroups->firstWhere('class', 'discord');
@endphp

@once
<style>
    .discussion-split{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;align-items:stretch}
    .discussion-banner{position:relative;display:grid;grid-template-columns:160px minmax(0,1fr);align-items:center;min-height:190px;overflow:hidden;border-radius:16px;color:#fff;box-shadow:0 18px 42px rgba(49,87,220,.16)}
    .discussion-banner.telegram{background:radial-gradient(circle at 12% 30%,rgba(83,224,212,.28),transparent 11rem),linear-gradient(125deg,#3157dc 0%,#7040ea 62%,#843ff0 100%)}
    .discussion-banner.discord{background:radial-gradient(circle at 12% 30%,rgba(255,255,255,.2),transparent 10rem),linear-gradient(125deg,#d92d8f 0%,#ec4899 52%,#8b3ee8 100%)}
    .discussion-visual{position:relative;height:100%;display:grid;place-items:center}
    .discussion-visual::before{content:"";position:absolute;width:126px;height:126px;border:17px solid rgba(255,255,255,.14);border-radius:38px;transform:rotate(12deg)}
    .discussion-logo{position:relative;z-index:1;width:92px;height:92px;display:grid;place-items:center;border-radius:28px;color:#fff;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.2);box-shadow:0 18px 34px rgba(7,22,77,.18);backdrop-filter:blur(10px)}
    .discussion-logo svg{width:58px;height:58px}
    .discussion-copy{position:relative;z-index:1;padding:26px 26px 26px 4px}
    .discussion-copy h3{margin:0;color:#fff;font-size:clamp(18px,2vw,24px);line-height:1.18}
    .discussion-copy p{max-width:430px;margin:9px 0 16px;color:rgba(255,255,255,.82);font-size:12px;line-height:1.55;text-align:left}
    .discussion-join{min-height:38px;display:inline-flex;align-items:center;gap:8px;padding:8px 13px;border-radius:8px;color:#4b3db8;background:#fff;font-size:11px;font-weight:900;box-shadow:0 10px 24px rgba(7,22,77,.16);transition:transform .18s ease,box-shadow .18s ease}
    .discussion-join:hover{color:#4b3db8;transform:translateY(-2px);box-shadow:0 14px 30px rgba(7,22,77,.22)}
    .discussion-join svg{width:15px;height:15px}
    @media(max-width:760px){.discussion-split{grid-template-columns:1fr}}
    @media(max-width:620px){.discussion-banner{grid-template-columns:112px minmax(0,1fr);min-height:170px}.discussion-logo{width:72px;height:72px;border-radius:22px}.discussion-logo svg{width:44px;height:44px}.discussion-visual::before{width:94px;height:94px;border-width:13px}.discussion-copy{padding:20px 16px 20px 0}.discussion-copy h3{font-size:17px}}
</style>
@endonce

<div class="discussion-split">
    <article class="discussion-banner telegram">
        <div class="discussion-visual">
            <span class="discussion-logo" aria-hidden="true">
                <svg viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="32" r="29" fill="rgba(255,255,255,.12)"/>
                    <path d="M14 30.5 49 17c1.8-.7 3.4.7 2.8 3L46 48.5c-.4 2.1-2 2.7-3.8 1.6L31.6 42l-5.2 5c-.6.6-1.1 1.1-2.2 1.1l.8-10.8 19.7-17.8c.9-.8-.2-1.2-1.4-.4L19 34.4l-10.5-3.3c-2.3-.7-2.3-2.3.5-3.4z" fill="currentColor"/>
                </svg>
            </span>
        </div>
        <div class="discussion-copy">
            <h3>Gabung Group Telegram Trama Verse</h3>
            <p>{{ $telegramGroup['description'] ?? 'Diskusi, berbagi insight, dan bangun relasi dengan sesama peserta Trama Verse.' }}</p>
            <a class="discussion-join" href="{{ $telegramGroup['url'] ?? 'https://t.me/tramaverse' }}" target="_blank" rel="noopener">
                Join Group Sekarang
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>
    </article>

    <article class="discussion-banner discord">
        <div class="discussion-visual">
            <span class="discussion-logo" aria-hidden="true">
                <svg viewBox="0 0 64 64" fill="none">
                    <path d="M20 18c8-4 16-4 24 0 6 8 8 17 7 26-5 5-10 7-15 8l-2.3-3.2c2.3-.7 4.5-1.8 6.4-3.1-7.1 3.3-14.7 3.3-22.1 0 1.9 1.3 4.1 2.4 6.4 3.1L22 52c-5-1-10-3-15-8-1-9 1-18 7-26l6 0z" fill="currentColor"/>
                    <circle cx="24" cy="34" r="4" fill="#d92d8f"/>
                    <circle cx="40" cy="34" r="4" fill="#d92d8f"/>
                </svg>
            </span>
        </div>
        <div class="discussion-copy">
            <h3>Gabung Forum Diskusi Discord</h3>
            <p>{{ $discordGroup['description'] ?? 'Tanyakan kendala praktik, troubleshooting tools, dan review workflow bersama komunitas.' }}</p>
            <a class="discussion-join" href="{{ $discordGroup['url'] ?? 'https://discord.gg/tramaverse' }}" target="_blank" rel="noopener">
                Gabung Forum
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>
    </article>
</div>
