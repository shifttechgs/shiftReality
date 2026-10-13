@extends('layouts.app')

@section('title', 'Reality Shift — Zimbabwe\'s Optical Illusion Experience · Harare')
@section('description', 'Your brain is about to be lied to. 4 mind-bending rooms at Westgate Shopping Complex, Harare. Zimbabwe\'s first optical illusion experience. Entry from $15. Book now.')

@push('head')
<script type="application/ld+json">
@php echo json_encode([
    '@context'   => 'https://schema.org',
    '@type'      => 'TouristAttraction',
    'name'       => config('shiftreality.name'),
    'description'=> config('shiftreality.description'),
    'url'        => config('shiftreality.url'),
    'address'    => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => config('shiftreality.location.address'),
        'addressLocality' => config('shiftreality.location.city'),
        'addressCountry'  => config('shiftreality.location.country_code'),
    ],
    'priceRange' => '$15',
    'telephone'  => config('shiftreality.contact.phone'),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); @endphp
</script>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════
     01 — HERO
     ══════════════════════════════════════════════════════════ --}}
<section class="wh-hero" id="hero" aria-label="Hero">

    <div class="wh-hero__inner">

        {{-- LEFT --}}
        <div class="wh-hero__text">

            <p class="wh-hero__tag">Westgate Shopping Complex, Harare</p>

            <h1 class="wh-hero__headline">
                Your brain<br>
                is about to<br>
                <em>be lied to.</em>
            </h1>

            <p class="wh-hero__desc">
                4 rooms of optical illusions that make your eyes
                contradict your mind. Entry from $15.
            </p>

            <div class="wh-hero__actions">
                <a href="{{ route('book') }}" class="pill pill--amber pill--lg" data-magnetic>
                    <svg class="pill__ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2z"/><path d="M13 5v2M13 17v2M13 11v2"/></svg>
                    <span>Book Your Spot</span>
                    <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="#rooms" class="wh-hero__link">Explore the rooms ↓</a>
            </div>

        </div>

        {{-- RIGHT: image --}}
        <div class="wh-hero__media">
            @if(file_exists(public_path('images/hero.jpg')))
                <img
                    src="{{ asset('images/hero.jpg') }}"
                    alt="Inside Reality Shift — optical illusion experience, Harare"
                    class="wh-hero__media-img"
                    fetchpriority="high"
                    decoding="async"
                >
            @elseif(file_exists(public_path('images/hero.webp')))
                <img
                    src="{{ asset('images/hero.webp') }}"
                    alt="Inside Reality Shift — optical illusion experience, Harare"
                    class="wh-hero__media-img"
                    fetchpriority="high"
                    decoding="async"
                >
            @else
                {{-- Placeholder until photo is added --}}
                <div class="wh-hero__placeholder" aria-hidden="true">
                    <div class="wh-hero__placeholder-grid"></div>
                    <p class="wh-hero__placeholder-label">
                        Drop your photo at<br>
                        <code>public/images/hero.jpg</code>
                    </p>
                </div>
            @endif

            <div class="wh-hero__media-tag" aria-label="Currently open">
                <span class="wh-hero__media-tag-dot"></span>
                Open Now · Limited Slots
            </div>
        </div>

    </div>

    {{-- Footer bar --}}
    <div class="wh-hero__footer">
        <div class="wh-hero__footer-facts">
            <span>4 Rooms</span>
            <span class="wh-hero__footer-dot" aria-hidden="true">·</span>
            <span>From $15</span>
            <span class="wh-hero__footer-dot" aria-hidden="true">·</span>
            <span>1.5 – 2.5 hours</span>
            <span class="wh-hero__footer-dot" aria-hidden="true">·</span>
            <span>Harare, Zimbabwe</span>
        </div>
        <div class="wh-hero__footer-rating" aria-label="5 star rated">
            <span style="color:var(--color-rs-blue)">★★★★★</span>
            <span>5-star rated</span>
        </div>
    </div>

</section>

{{-- ══════════════════════════════════════════════════════════
     02 — TRUST STRIP
     Instant credibility below the fold.
     ══════════════════════════════════════════════════════════ --}}
<div class="proof-strip" aria-label="Why visitors love it">
    <div class="proof-strip__item">
        <span class="proof-strip__val" style="color:var(--color-rs-gold)">★★★★★</span>
        <span class="proof-strip__label">5-Star Rated</span>
    </div>
    <div class="proof-strip__item">
        <span class="proof-strip__val">Zimbabwe's First</span>
        <span class="proof-strip__label">Optical Illusion Experience</span>
    </div>
    <div class="proof-strip__item">
        <span class="proof-strip__val" style="color:var(--color-rs-blue)">100+</span>
        <span class="proof-strip__label">Visitors Every Weekend</span>
    </div>
    <div class="proof-strip__item">
        <span class="proof-strip__val">Ages 5+</span>
        <span class="proof-strip__label">Perfect for Everyone</span>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     03 — MARQUEE
     ══════════════════════════════════════════════════════════ --}}
<div class="marquee-strip" aria-hidden="true">
    <div class="marquee-strip__track">
        <span class="marquee-strip__item">Your Eyes Will Lie to You</span>
        <span class="marquee-strip__sep">◈</span>
        <span class="marquee-strip__item marquee-strip__item--accent">Book Before the Weekend Fills</span>
        <span class="marquee-strip__sep">◈</span>
        <span class="marquee-strip__item">Nothing Looks Real In Here</span>
        <span class="marquee-strip__sep">◈</span>
        <span class="marquee-strip__item marquee-strip__item--accent">From $15 · Harare</span>
        <span class="marquee-strip__sep">◈</span>
        <span class="marquee-strip__item">4 Rooms · Zero Explanations</span>
        <span class="marquee-strip__sep">◈</span>
        <span class="marquee-strip__item">Your Eyes Will Lie to You</span>
        <span class="marquee-strip__sep">◈</span>
        <span class="marquee-strip__item marquee-strip__item--accent">Book Before the Weekend Fills</span>
        <span class="marquee-strip__sep">◈</span>
        <span class="marquee-strip__item">Nothing Looks Real In Here</span>
        <span class="marquee-strip__sep">◈</span>
        <span class="marquee-strip__item marquee-strip__item--accent">From $15 · Harare</span>
        <span class="marquee-strip__sep">◈</span>
        <span class="marquee-strip__item">4 Rooms · Zero Explanations</span>
        <span class="marquee-strip__sep">◈</span>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     04 — WHAT HAPPENS INSIDE
     Make them feel the rooms before they arrive.
     ══════════════════════════════════════════════════════════ --}}
<section class="tease-section" id="rooms" aria-labelledby="tease-heading">
    <div class="container">

        <div class="editorial-header" data-reveal aria-hidden="true">
            <span class="editorial-header__col">Reality Shift · Harare</span>
            <span class="editorial-header__col">4 Installations</span>
            <span class="editorial-header__col">All Active Now</span>
        </div>

        <div class="tease-section__header" data-reveal>
            <h2 class="tease-section__title" id="tease-heading">
                Four rooms.<br>Zero explanations.
            </h2>
            <p class="tease-section__sub">
                Each room is designed to make you question something you've taken for granted your entire life.
                You'll walk out differently than you walked in.
            </p>
        </div>

        <div class="tease-grid">
            @foreach($shifts as $i => $shift)
            <article class="tease-card" data-reveal data-reveal-delay="{{ $i + 1 }}">
                <div class="tease-card__number" aria-hidden="true">{{ $shift['number'] }}</div>
                <div class="tease-card__live">
                    <span class="tease-card__live-dot"></span>
                    Open Now
                </div>
                <h3 class="tease-card__title">{{ $shift['title'] }}</h3>
                <p class="tease-card__tagline">{{ $shift['tagline'] }}</p>
                <p class="tease-card__desc">{{ $shift['description'] }}</p>
                <a href="{{ route('book') }}" class="tease-card__cta">
                    Book to experience this →
                </a>
            </article>
            @endforeach
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     05 — SOCIAL PROOF
     Other people loved it. You will too. Don't miss out.
     ══════════════════════════════════════════════════════════ --}}
<section class="reviews-section section" id="reviews" aria-labelledby="reviews-heading">
    <div class="container">

        <div class="editorial-header" data-reveal aria-hidden="true">
            <span class="editorial-header__col">Visitor Reviews</span>
            <span class="editorial-header__col">★★★★★ Rated</span>
            <span class="editorial-header__col">Google · Facebook</span>
        </div>

        <div class="reviews-section__intro" data-reveal>
            <h2 class="reviews-section__title" id="reviews-heading">
                Harare can't stop<br>talking about it.
            </h2>
            <p class="reviews-section__tagline">
                Every weekend, hundreds of people walk in sceptical and walk out amazed.
                Here's what they said.
            </p>
        </div>

        <div class="reviews-grid">
            @foreach($testimonials as $i => $t)
            <article class="review-card" data-reveal data-reveal-delay="{{ $i + 1 }}">
                <div class="review-card__stars" aria-label="{{ $t['rating'] }} out of 5 stars">
                    @for($s = 0; $s < $t['rating']; $s++)<span aria-hidden="true">★</span>@endfor
                </div>
                <blockquote class="review-card__quote">{{ $t['quote'] }}</blockquote>
                <div class="review-card__meta">
                    <cite class="review-card__name">{{ $t['name'] }}</cite>
                    <span class="review-card__detail">{{ $t['detail'] }}</span>
                </div>
            </article>
            @endforeach
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     06 — INLINE CTA
     Momentum break — catch them at peak desire.
     ══════════════════════════════════════════════════════════ --}}
<div class="inline-cta" data-reveal>
    <p class="inline-cta__overline">Don't wait until it's sold out</p>
    <h2 class="inline-cta__headline">
        Your spot is one click away.
    </h2>
    <p class="inline-cta__sub">
        Most people who visit say they wish they'd come sooner.
        Walk-ins are welcome but weekends fill up — secure yours now.
    </p>
    <a href="{{ route('book') }}" class="pill pill--amber pill--xl" data-magnetic>
        <svg class="pill__ico" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2z"/><path d="M13 5v2M13 17v2M13 11v2"/></svg>
        <span>Book Now — From $15</span>
        <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
    <p class="inline-cta__note">
        Westgate Shopping Complex, Harare · Tue–Fri 10:00–19:00 · Sat–Sun 09:00–20:00
    </p>
</div>

{{-- ══════════════════════════════════════════════════════════
     07 — WHO IS IT FOR
     No one should feel left out.
     ══════════════════════════════════════════════════════════ --}}
<section class="audience-section" aria-labelledby="audience-heading">
    <div class="container">

        <div class="editorial-header" data-reveal aria-hidden="true">
            <span class="editorial-header__col">Who It's For</span>
            <span class="editorial-header__col">Everyone</span>
            <span class="editorial-header__col">Ages 5+</span>
        </div>

        <h2 class="audience-section__title" id="audience-heading" data-reveal>
            Built for everyone.<br>Forget nobody.
        </h2>

        <div class="audience-cloud" data-reveal>
            @foreach($audiences as $audience)
            <span class="audience-pill">{{ $audience }}</span>
            @endforeach
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     08 — GROUPS + EVENTS
     ══════════════════════════════════════════════════════════ --}}
<section class="split-section" id="groups" aria-labelledby="groups-heading">
    <div class="split-section__media">
        <div class="split-section__media-placeholder" aria-hidden="true"></div>
    </div>
    <div class="split-section__content" data-reveal="right">
        <p class="eyebrow">Schools · Corporates · Birthdays</p>
        <h2 class="split-section__title" id="groups-heading">
            Make it a group<br>they'll never forget.
        </h2>
        <p class="split-section__body">
            Group bookings get priority entry and exclusive rates.
            Perfect for team-building, school trips, birthday celebrations, and corporate events.
            Spaces fill fast — enquire early.
        </p>
        <a href="{{ route('groups') }}" class="pill pill--amber">
            <svg class="pill__ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Enquire for Groups</span>
            <svg class="pill__arr" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     09 — VISIT ESSENTIALS
     Remove every last booking objection.
     ══════════════════════════════════════════════════════════ --}}
<section class="essentials-section section" id="visit" aria-labelledby="essentials-heading">
    <div class="container">

        <div class="editorial-header" data-reveal aria-hidden="true">
            <span class="editorial-header__col">Before You Arrive</span>
            <span class="editorial-header__col">Everything You Need</span>
            <span class="editorial-header__col">Reality Shift · HRE</span>
        </div>

        <div class="section-header" data-reveal>
            <h2 class="section-header__title" id="essentials-heading">Plan your visit</h2>
            <a href="{{ route('visit') }}" class="pill pill--outline" style="flex-shrink:0">Full Details</a>
        </div>

        <div class="essentials-grid" data-reveal>
            <div class="essential-cell">
                <p class="essential-cell__label">Location</p>
                <p class="essential-cell__value">Westgate Shopping Complex</p>
                <p class="essential-cell__sub">Harare, Zimbabwe</p>
            </div>
            <div class="essential-cell">
                <p class="essential-cell__label">Opening Hours</p>
                <p class="essential-cell__value">Tue – Fri</p>
                <p class="essential-cell__sub">10:00 – 19:00</p>
                <p class="essential-cell__sub" style="margin-top:0.3rem">Sat–Sun: 09:00 – 20:00</p>
            </div>
            <div class="essential-cell">
                <p class="essential-cell__label">Duration</p>
                <p class="essential-cell__value">{{ $visit['duration'] }}</p>
                <p class="essential-cell__sub">Go at your own pace</p>
            </div>
            <div class="essential-cell">
                <p class="essential-cell__label">Tickets</p>
                <p class="essential-cell__value" style="color:var(--color-rs-gold)">From $15</p>
                <p class="essential-cell__sub">Book online to guarantee your slot</p>
            </div>
        </div>

        <div class="faq" role="list" style="margin-top:clamp(2.5rem,5vw,4rem);" data-reveal>
            @foreach(array_slice($faq, 0, 4) as $i => $item)
            <div class="faq__item" role="listitem">
                <button class="faq__question" aria-expanded="false" aria-controls="faq-a-{{ $i }}" id="faq-q-{{ $i }}">
                    {{ $item['question'] }}
                    <span class="faq__icon" aria-hidden="true">+</span>
                </button>
                <div class="faq__answer" id="faq-a-{{ $i }}" role="region" aria-labelledby="faq-q-{{ $i }}">
                    <p class="faq__answer-inner">{{ $item['answer'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div style="margin-top:1.5rem;" data-reveal>
            <a href="{{ route('faq') }}" class="pill pill--outline">More Questions Answered</a>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     10 — BRAND BANNER
     ══════════════════════════════════════════════════════════ --}}
<div class="brand-banner" aria-hidden="true">
    <div class="brand-banner__track">
        <span class="brand-banner__word brand-banner__word--filled">Reality</span>
        <span class="brand-banner__sep">◈</span>
        <span class="brand-banner__word brand-banner__word--gold">Shift</span>
        <span class="brand-banner__sep">◈</span>
        <span class="brand-banner__word">Zimbabwe</span>
        <span class="brand-banner__sep">◈</span>
        <span class="brand-banner__word brand-banner__word--filled">Reality</span>
        <span class="brand-banner__sep">◈</span>
        <span class="brand-banner__word brand-banner__word--gold">Shift</span>
        <span class="brand-banner__sep">◈</span>
        <span class="brand-banner__word">Zimbabwe</span>
        <span class="brand-banner__sep">◈</span>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     11 — FINAL CTA
     The urgency close. Make them feel the cost of waiting.
     ══════════════════════════════════════════════════════════ --}}
<section class="final-booking" id="book" aria-label="Book your experience">
    <div class="container">

        <p class="final-booking__overline" data-reveal>Everyone in Harare is talking about this.</p>

        <h2 class="final-booking__title" data-reveal>
            Don't be the last<br><em>one to find out.</em>
        </h2>

        <p class="final-booking__sub" data-reveal data-reveal-delay="2">
            Weekends book out. Walk-ins are welcome but a spot isn't guaranteed.<br>
            From $15 per person · Westgate Shopping Complex, Harare.
        </p>

        <div class="final-booking__actions" data-reveal data-reveal-delay="3">
            <a href="{{ route('book') }}" class="pill pill--amber pill--xl" data-magnetic>
                <svg class="pill__ico" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2z"/><path d="M13 5v2M13 17v2M13 11v2"/></svg>
                <span>Secure Your Spot Now</span>
                <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <a href="https://wa.me/{{ config('shiftreality.contact.whatsapp') }}" class="pill pill--outline pill--xl" target="_blank" rel="noopener">
                WhatsApp Us
            </a>
        </div>

        <p class="final-booking__reassurance" data-reveal data-reveal-delay="4">
            ★★★★★ &nbsp;·&nbsp; 5-star rated &nbsp;·&nbsp; Ages 5 and up &nbsp;·&nbsp; Photography welcome
        </p>

    </div>
</section>

@endsection
