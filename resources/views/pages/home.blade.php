@extends('layouts.app')

@section('title', config('shiftreality.name') . ' — Your eyes are lying to you.')
@section('description', 'ShiftReality is an immersive optical illusion and perception experience in Zimbabwe. Challenge what you see. Book your experience today.')

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
        'addressLocality' => config('shiftreality.location.city'),
        'addressRegion'   => config('shiftreality.location.region'),
        'addressCountry'  => config('shiftreality.location.country_code'),
    ],
    'openingHoursSpecification' => [
        ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Tuesday','Wednesday','Thursday','Friday'], 'opens' => '10:00', 'closes' => '19:00'],
        ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Saturday','Sunday'], 'opens' => '09:00', 'closes' => '20:00'],
    ],
    'touristType'       => ['Family','Groups','Couples','Friends'],
    'isAccessibleForFree' => false,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); @endphp
</script>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════
     01 — HERO
     The first perception shift. The website begins here.
     ══════════════════════════════════════════════════════════ --}}
<section class="hero" id="hero" aria-label="Hero">

    {{-- Perspective grid canvas — cursor-reactive depth illusion --}}
    <canvas id="hero-canvas" class="hero-canvas" aria-hidden="true"></canvas>

    {{-- Film grain texture --}}
    <div class="hero__grain" aria-hidden="true"></div>

    {{-- Editorial index strip --}}
    <div class="hero__eyebrow" aria-hidden="true">
        <span class="hero__eyebrow-index">01</span>
        <span class="hero__eyebrow-rule"></span>
        <span class="hero__eyebrow-tag">ShiftReality &middot; Harare, ZW</span>
    </div>

    {{-- The primary message. Short. Direct. Provocative. --}}
    <div class="hero__content">
        <div class="hero__headline" aria-label="Your eyes are lying to you">
            <div class="hero__line-wrap">
                <span class="hero__line">YOUR EYES</span>
            </div>
            <div class="hero__line-wrap">
                <span class="hero__line hero__line--accent hero__line--right">ARE LYING</span>
            </div>
            <div class="hero__line-wrap hero__line-wrap--indent">
                <span class="hero__line">TO YOU.</span>
            </div>
        </div>
    </div>

    {{-- Hero footer: description · scroll · rotating badge --}}
    <div class="hero__footer">

        <div class="hero__footer-info">
            <div class="hero__progress" aria-hidden="true">
                <span class="hero__progress-value">00</span>
                <span class="hero__progress-sep"></span>
                <span>Scroll to discover</span>
            </div>
            <p class="hero__footer-desc">
                An immersive optical illusion<br>
                experience in Zimbabwe.
            </p>
            <a
                href="{{ route('book') }}"
                class="btn btn-primary btn-arrow hero__cta"
                data-magnetic
                aria-label="Book your ShiftReality experience"
            >
                Book Your Experience
                <span class="arrow" aria-hidden="true">→</span>
            </a>
        </div>

        <div class="hero__scroll" aria-hidden="true">
            <div class="hero__scroll-line"></div>
            <span class="hero__scroll-label">SCROLL</span>
        </div>

        {{-- Rotating circular badge --}}
        <div class="hero__badge" aria-hidden="true">
            <svg viewBox="0 0 120 120" class="hero__badge-svg">
                <defs>
                    <path id="hero-badge-path" d="M60,60 m-42,0 a42,42 0 1,1 84,0 a42,42 0 1,1 -84,0" />
                </defs>
                <text class="hero__badge-text" font-size="8">
                    <textPath href="#hero-badge-path">SHIFTREALITY · HARARE · ZW · EST.2025 · </textPath>
                </text>
            </svg>
            <span class="hero__badge-icon">◈</span>
        </div>

    </div>

    {{-- Marquee ticker --}}
    <div class="hero__marquee" aria-hidden="true">
        <div class="hero__marquee-track">
            <span class="hero__marquee-item">Shift Your Reality</span>
            <span class="hero__marquee-sep">◈</span>
            <span class="hero__marquee-item">Optical Illusion Experience</span>
            <span class="hero__marquee-sep">◈</span>
            <span class="hero__marquee-item">Harare, Zimbabwe</span>
            <span class="hero__marquee-sep">◈</span>
            <span class="hero__marquee-item">Est. 2025</span>
            <span class="hero__marquee-sep">◈</span>
            <span class="hero__marquee-item">Shift Your Reality</span>
            <span class="hero__marquee-sep">◈</span>
            <span class="hero__marquee-item">Optical Illusion Experience</span>
            <span class="hero__marquee-sep">◈</span>
            <span class="hero__marquee-item">Harare, Zimbabwe</span>
            <span class="hero__marquee-sep">◈</span>
            <span class="hero__marquee-item">Est. 2025</span>
            <span class="hero__marquee-sep">◈</span>
        </div>
    </div>

</section>

{{-- ══════════════════════════════════════════════════════════
     02 — THE INVITATION
     Explain the feeling. Not the facts.
     ══════════════════════════════════════════════════════════ --}}
<section class="section invitation" id="invitation" aria-labelledby="invitation-heading">
    <div class="container">
        <div class="invitation__inner">

            <div>
                <h1
                    id="invitation-heading"
                    class="invitation__headline"
                    data-reveal="left"
                >
                    Reality isn't<br>always what<br>it <em>looks like</em>.
                </h1>
            </div>

            <div class="invitation__body" data-reveal="right" data-reveal-delay="2">
                <p class="invitation__text">
                    ShiftReality is an immersive experience in Zimbabwe built around optical
                    illusions, perception tricks, and environments that challenge everything
                    your brain takes for granted.
                </p>
                <p class="invitation__text">
                    Step inside. Explore rooms that shouldn't work. Question what you think
                    you know. Leave with something you can't quite explain.
                </p>
                <a href="{{ route('experience') }}" class="btn btn-ghost btn-arrow">
                    Explore the experience
                    <span class="arrow" aria-hidden="true">→</span>
                </a>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     03 — THE SHIFTS
     Four experience types. Each its own visual world.
     ══════════════════════════════════════════════════════════ --}}
<section class="section shifts" id="shifts" aria-labelledby="shifts-heading">
    <div class="shifts__header container">
        <div class="section-header" data-reveal>
            <div>
                <p class="section-header__eyebrow">What awaits</p>
                <h2 class="section-header__title" id="shifts-heading">The Shifts</h2>
            </div>
            <a href="{{ route('experience') }}" class="btn btn-ghost btn-arrow" style="flex-shrink:0">
                See all <span class="arrow" aria-hidden="true">→</span>
            </a>
        </div>
    </div>

    <div class="shifts__track">
        <div class="shifts__grid">

            @foreach($shifts as $i => $shift)
            <article class="shift-card" data-reveal data-reveal-delay="{{ $i + 1 }}">

                {{-- Giant background number --}}
                <div class="shift-card__number" aria-hidden="true">{{ $shift['number'] }}</div>

                {{-- CSS optical illusion illustration --}}
                <div class="shift-card__illusion" aria-hidden="true">
                    @if($shift['slug'] === 'perspective')
                        {{-- Ambiguous wireframe cube — appears to flip between two orientations --}}
                        <div class="illusion-cube">
                            <div class="illusion-cube__face illusion-cube__face--back"></div>
                            <div class="illusion-cube__face illusion-cube__face--front"></div>
                        </div>

                    @elseif($shift['slug'] === 'gravity')
                        {{-- Ball that rolls uphill against the gradient slope --}}
                        <div class="illusion-gravity">
                            <div class="illusion-gravity__track"></div>
                            <div class="illusion-gravity__ball"></div>
                        </div>

                    @elseif($shift['slug'] === 'scale')
                        {{-- Ponzo illusion — two identical bars appear different sizes --}}
                        <div class="illusion-ponzo">
                            <div class="illusion-ponzo__rail illusion-ponzo__rail--left"></div>
                            <div class="illusion-ponzo__rail illusion-ponzo__rail--right"></div>
                            <div class="illusion-ponzo__bar illusion-ponzo__bar--top"></div>
                            <div class="illusion-ponzo__bar illusion-ponzo__bar--bottom"></div>
                        </div>

                    @elseif($shift['slug'] === 'perception')
                        {{-- Checker shadow — two squares appear different but are the same shade --}}
                        <div class="illusion-checker" aria-label="Checker shadow illusion: two squares that appear different shades are actually identical">
                            @php
                                $pattern = [
                                    ['a','b','a','b','a','b'],
                                    ['b','a','b','a','b','a'],
                                    ['a','b','a','x','a','b'],
                                    ['b','a','b','a','b','a'],
                                    ['a','b','a','b','a','b'],
                                ];
                            @endphp
                            @foreach($pattern as $row)
                                @foreach($row as $cell)
                                    <div class="illusion-checker__cell illusion-checker__cell--{{ $cell }}"></div>
                                @endforeach
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Text content --}}
                <div class="shift-card__content">
                    <p class="shift-card__label">{{ $shift['number'] }} / {{ $shift['title'] }}</p>
                    <h3 class="shift-card__title">{{ $shift['title'] }}</h3>
                    <p class="shift-card__desc">{{ $shift['tagline'] }}</p>
                </div>

            </article>
            @endforeach

        </div>{{-- .shifts__grid --}}
    </div>{{-- .shifts__track --}}
</section>

{{-- ══════════════════════════════════════════════════════════
     04 — THE EXPERIENCE
     Photography / video will live here.
     Insert actual ShiftReality imagery into the media-block elements.
     ══════════════════════════════════════════════════════════ --}}
<section class="section experience" id="experience" aria-labelledby="experience-heading">
    <div class="container">
        <div class="section-header" data-reveal>
            <div>
                <p class="section-header__eyebrow">Inside ShiftReality</p>
                <h2 class="section-header__title" id="experience-heading">See for yourself</h2>
            </div>
        </div>

        <p style="font-size: clamp(1rem, 1.75vw, 1.1875rem); color: var(--color-sr-muted); max-width: 50ch; margin-bottom: clamp(3rem, 5vw, 4rem);" data-reveal data-reveal-delay="2">
            Discover rooms designed to challenge everything you think you know about
            space, size, gravity, and perception.
        </p>

        <div class="experience__media-grid" data-reveal="scale" data-reveal-delay="1">

            {{-- Primary (large) media block — replace placeholder with hero photography --}}
            <div class="media-block media-block--primary">
                <div class="media-block__inner">
                    <div class="media-block__placeholder">
                        {{-- Replace: <img src="{{ asset('images/experience-hero.jpg') }}" alt="Visitors experiencing ShiftReality" loading="lazy"> --}}
                        <div class="media-block__placeholder-icon" aria-hidden="true">◻</div>
                        <p class="media-block__placeholder-text">Hero photograph</p>
                    </div>
                </div>
                <span class="media-block__label" aria-hidden="true">Insert main experience photo</span>
            </div>

            {{-- Secondary media — top right --}}
            <div class="media-block media-block--secondary">
                <div class="media-block__inner">
                    <div class="media-block__placeholder">
                        {{-- Replace: <img src="{{ asset('images/experience-2.jpg') }}" alt="..." loading="lazy"> --}}
                        <div class="media-block__placeholder-icon" aria-hidden="true">◻</div>
                        <p class="media-block__placeholder-text">Experience photo 2</p>
                    </div>
                </div>
                <span class="media-block__label" aria-hidden="true">Insert photo</span>
            </div>

            {{-- Secondary media — bottom right --}}
            <div class="media-block media-block--secondary">
                <div class="media-block__inner">
                    <div class="media-block__placeholder">
                        {{-- Replace: <img src="{{ asset('images/experience-3.jpg') }}" alt="..." loading="lazy"> --}}
                        <div class="media-block__placeholder-icon" aria-hidden="true">◻</div>
                        <p class="media-block__placeholder-text">Experience photo 3</p>
                    </div>
                </div>
                <span class="media-block__label" aria-hidden="true">Insert photo</span>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     05 — THE REACTION
     Social proof. Real people. Real reactions.
     Replace mock data with verified reviews when available.
     ══════════════════════════════════════════════════════════ --}}
<section class="section reaction" id="reaction" aria-labelledby="reaction-heading">
    <div class="container">
        <div class="section-header" data-reveal>
            <div>
                <p class="section-header__eyebrow">Visitor reactions</p>
                <h2 class="section-header__title" id="reaction-heading">The Reaction</h2>
            </div>
        </div>

        <div class="reaction__grid">
            @foreach($testimonials as $i => $t)
            <article class="testimonial" data-reveal data-reveal-delay="{{ $i + 1 }}">
                <div class="testimonial__stars" aria-label="{{ $t['rating'] }} out of 5 stars">
                    @for($s = 0; $s < $t['rating']; $s++)
                        <span class="testimonial__star" aria-hidden="true">★</span>
                    @endfor
                </div>
                <blockquote class="testimonial__quote">
                    {{ $t['quote'] }}
                </blockquote>
                <div class="testimonial__meta">
                    <cite class="testimonial__name">{{ $t['name'] }}</cite>
                    <p class="testimonial__detail">{{ $t['detail'] }}</p>
                </div>
            </article>
            @endforeach
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     06 — WHO IS IT FOR
     Typographic audience display. Not an icon grid.
     ══════════════════════════════════════════════════════════ --}}
<section class="section for-you" id="for-you" aria-labelledby="for-you-heading">
    <div class="container">
        <div class="section-header" data-reveal>
            <div>
                <p class="section-header__eyebrow">For everyone who</p>
                <h2 class="section-header__title" id="for-you-heading">Who is it for?</h2>
            </div>
        </div>

        <div class="for-you__grid" role="list" data-reveal>
            @foreach($audiences as $audience)
                <span class="for-you__tag" role="listitem">{{ $audience }}</span>
            @endforeach
        </div>

        <div style="margin-top: clamp(3rem, 6vw, 5rem);" data-reveal>
            <a href="{{ route('groups') }}" class="btn btn-outline btn-arrow">
                Group &amp; corporate bookings
                <span class="arrow" aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     07 — PLAN YOUR VISIT
     Practical information. Left: hours + details. Right: FAQ.
     ══════════════════════════════════════════════════════════ --}}
<section class="section visit-section" id="visit" aria-labelledby="visit-heading">
    <div class="container">
        <div class="section-header" data-reveal>
            <div>
                <p class="section-header__eyebrow">Before you arrive</p>
                <h2 class="section-header__title" id="visit-heading">Plan Your Visit</h2>
            </div>
            <a href="{{ route('visit') }}" class="btn btn-ghost btn-arrow" style="flex-shrink:0">
                Full details <span class="arrow" aria-hidden="true">→</span>
            </a>
        </div>

        <div class="visit-section__inner">

            {{-- Visit info --}}
            <div class="visit-info" data-reveal="left">

                <div class="visit-info__block">
                    <p class="visit-info__label">Location</p>
                    <p class="visit-info__value">
                        {{ config('shiftreality.location.city') }},
                        {{ config('shiftreality.location.country') }}
                    </p>
                    @if(!config('shiftreality.location.address'))
                        <p class="visit-info__sub">Full address to be confirmed</p>
                    @endif
                </div>

                <div class="visit-info__block">
                    <p class="visit-info__label">Opening Hours</p>
                    <div class="hours-list">
                        @foreach($hours as $slot)
                        <div class="hours-row">
                            <span class="hours-row__days">{{ $slot['days'] }}</span>
                            <span class="hours-row__time">{{ $slot['time'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="visit-info__block">
                    <p class="visit-info__label">Duration</p>
                    <p class="visit-info__value">{{ $visit['duration'] }}</p>
                    <p class="visit-info__sub">No rush — explore at your own pace</p>
                </div>

                <div class="visit-info__block">
                    <p class="visit-info__label">Minimum Age</p>
                    <p class="visit-info__value">{{ $visit['min_age'] }}+ years</p>
                    <p class="visit-info__sub">Suitable for the whole family</p>
                </div>

                <div class="visit-info__block">
                    <p class="visit-info__label">Tickets</p>
                    @if($visit['price_from'])
                        <p class="visit-info__value">From ${{ $visit['price_from'] }}</p>
                    @else
                        <p class="visit-info__value">Pricing coming soon</p>
                        <p class="visit-info__sub">Online booking available</p>
                    @endif
                </div>

            </div>

            {{-- FAQ --}}
            <div data-reveal="right" data-reveal-delay="2">
                <div class="faq" role="list">
                    @foreach($faq as $i => $item)
                    <div class="faq__item" role="listitem">
                        <button
                            class="faq__question"
                            aria-expanded="false"
                            aria-controls="faq-answer-{{ $i }}"
                            id="faq-q-{{ $i }}"
                        >
                            {{ $item['question'] }}
                            <span class="faq__icon" aria-hidden="true">+</span>
                        </button>
                        <div
                            class="faq__answer"
                            id="faq-answer-{{ $i }}"
                            role="region"
                            aria-labelledby="faq-q-{{ $i }}"
                        >
                            <p class="faq__answer-inner">{{ $item['answer'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div style="margin-top: 2.5rem;">
                    <a
                        href="{{ route('faq') }}"
                        class="btn btn-ghost btn-arrow"
                    >
                        See all questions
                        <span class="arrow" aria-hidden="true">→</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     08 — FINAL CTA
     The climax. The inevitable booking moment.
     ══════════════════════════════════════════════════════════ --}}
<section class="final-cta" id="book" aria-labelledby="final-cta-heading">

    <div class="final-cta__bg" aria-hidden="true"></div>

    <div class="final-cta__inner">

        <h2 class="final-cta__headline" id="final-cta-heading" data-reveal>
            <span>Ready to shift</span>
            <span class="accent">your reality?</span>
        </h2>

        <p class="final-cta__sub" data-reveal data-reveal-delay="2">
            Tickets available online. Walk-ins subject to availability.<br>
            {{ config('shiftreality.location.city') }}, {{ config('shiftreality.location.country') }}.
        </p>

        <div data-reveal data-reveal-delay="3">
            <a
                href="{{ route('book') }}"
                class="btn btn-primary btn-arrow"
                data-magnetic
                aria-label="Book your ShiftReality experience"
                style="font-size: 1rem; padding: 1.25rem 3rem;"
            >
                Book Your Experience
                <span class="arrow" aria-hidden="true">→</span>
            </a>
        </div>

    </div>

</section>

@endsection
