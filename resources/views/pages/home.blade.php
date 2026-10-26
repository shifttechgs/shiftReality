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
    'geo' => [
        '@type'     => 'GeoCoordinates',
        'latitude'  => config('shiftreality.location.coordinates.lat'),
        'longitude' => config('shiftreality.location.coordinates.lng'),
    ],
    'openingHoursSpecification' => $openingHours->schema(),
    'priceRange' => '$15',
    'telephone'  => config('shiftreality.contact.phone'),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); @endphp
</script>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════
     01 — HERO
     ══════════════════════════════════════════════════════════ --}}
{{-- Light, type-led hero (after CargoFlow's layout), in three bands: the intro and
     a small navy visit card; the tagline set giant across the full width; then an
     edge-to-edge marquee of small tiles, one per room, gliding past on its own. Each
     tile holds its room's illusion still and plays it on hover, which also pauses the
     marquee (hero-strip.js); the card's live tunnel is hero-tunnel.js. Fits one screen. --}}
@php
    $rating     = config('shiftreality.google_rating');
    $heroStatus = $openingHours->status();
    $location   = config('shiftreality.location');
@endphp
<section class="wh-hero" id="hero" aria-labelledby="hero-heading">

    <div class="wh-hero__top">
        <div class="wh-hero__intro">
            <p class="wh-hero__desc">
                Your brain is about to be lied to. {{ count($shifts) }} rooms of optical illusions at
                {{ $location['address'] }}, {{ $location['city'] }} — entry from ${{ $visit['price_from'] }}.
            </p>
            <span class="wh-hero__rating" role="img" aria-label="Rated {{ $rating }} out of 5, based on Google Reviews">
                @include('partials.google-g', ['size' => 16, 'class' => 'wh-hero__rating-g'])
                <span class="wh-hero__rating-score">{{ number_format($rating, 1) }}</span>
                @include('partials.stars', ['rating' => $rating, 'size' => 12])
                <span class="wh-hero__rating-label">Google Reviews</span>
            </span>
        </div>

        <a href="{{ route('book') }}" class="wh-hero__card">
            <span class="wh-hero__card-media" data-hero-tunnel aria-hidden="true">
                <canvas class="infinity" data-infinity></canvas>
            </span>
            <span class="wh-hero__card-body">
                <span class="wh-hero__card-kicker">
                    Book your visit · ${{ $visit['price_from'] }}
                    <svg class="wh-hero__card-arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </span>
                <span @class(['wh-hero__card-status', 'is-open' => $heroStatus['open']])>{{ $heroStatus['label'] }}</span>
                <span class="wh-hero__card-meta">{{ $heroStatus['detail'] }}</span>
            </span>
        </a>
    </div>

    <h1 class="wh-hero__headline" id="hero-heading">
        <span class="wh-hero__word"><span>Question</span></span>
        <span class="wh-hero__word"><span>Everything</span></span>
    </h1>

    {{-- Marquee: the rooms three times over, so the loop is seamless even on very
         wide screens (the copies are hidden from screen readers and the tab order).
         Hovering pauses it. --}}
    <div class="wh-hero__strip" data-hero-strip>
        <div class="wh-hero__track" data-hero-track>
            @foreach ([false, true, true] as $copy)
            @foreach ($shifts as $shift)
                @php $photo = $shift['photo'] ?? null; @endphp
                {{-- A room with a photo shows it (crisp: 1x and 2x files); the rest show their illusion --}}
                <a href="#rooms" @class(['wh-hero__tile', 'wh-hero__tile--dark' => $shift['slug'] === 'infinity', 'wh-hero__tile--photo' => $photo])
                   @unless ($photo) data-hero-tile="{{ $shift['slug'] }}" @endunless
                   @if ($copy) aria-hidden="true" tabindex="-1" @else aria-label="{{ $shift['title'] }} — see the rooms" @endif>
                    <span class="wh-hero__stage" @unless ($photo) data-room-stage @endunless aria-hidden="true">
                        @if ($photo)
                            <img
                                src="{{ asset($photo) }}"
                                srcset="{{ asset($photo) }} 800w, {{ asset(str_replace('-800.', '-1600.', $photo)) }} 1600w"
                                sizes="(max-width: 900px) 300px, 380px"
                                alt=""
                                class="wh-hero__photo"
                                decoding="async"
                                @if ($copy) loading="lazy" @endif
                            >
                        @else
                            @include('partials.rooms.' . $shift['slug'])
                        @endif
                    </span>
                    <span class="wh-hero__tile-label"><span>{{ $shift['number'] }}</span>{{ $shift['title'] }}</span>
                </a>
            @endforeach
            @endforeach
        </div>
    </div>

</section>

{{-- ══════════════════════════════════════════════════════════
     02 — WHAT HAPPENS INSIDE
     Make them feel the rooms before they arrive.
     ══════════════════════════════════════════════════════════ --}}
<section class="rooms" id="rooms" aria-labelledby="rooms-heading">
    <div class="container">

        <div class="rooms__intro">
            <h2 class="rooms__heading" id="rooms-heading" data-split>
                Four rooms.<br>Zero explanations.
            </h2>
            <p class="rooms__sub" data-scrub>
                Each room is designed to make you question something you've taken for granted your entire life.
                You'll walk out differently than you walked in.
            </p>
        </div>

    </div>

    {{-- Pinned on desktop: the frame opens like a portal, then the track scrolls sideways
         through every room. Each stage is a live version of that room's illusion. --}}
    <div class="rooms__viewport" data-rooms>
        <div class="rooms__frame" data-rooms-frame>
            <div class="rooms__track" data-rooms-track>

                <div class="rooms__enter" aria-hidden="true">
                    <p class="rooms__enter-kicker">The experience</p>
                    <p class="rooms__enter-title">Step<br><em>inside.</em></p>
                    <p class="rooms__enter-hint">Keep scrolling <span>→</span></p>
                </div>

                @foreach ($shifts as $shift)
                    <article class="room" data-room="{{ $shift['slug'] }}" aria-labelledby="room-{{ $shift['slug'] }}">
                        <span class="room__number" data-room-number aria-hidden="true">{{ $shift['number'] }}</span>

                        <div class="room__copy">
                            <p class="room__kicker">Room {{ $shift['number'] }} <span>/ {{ str_pad(count($shifts), 2, '0', STR_PAD_LEFT) }}</span></p>
                            <h3 class="room__title" id="room-{{ $shift['slug'] }}">
                                @foreach (explode(' ', $shift['title']) as $w => $word)
                                    <span class="room__word"><span style="--i: {{ $w }}">{{ $word }}</span></span>
                                @endforeach
                            </h3>
                            <p class="room__tagline">{{ $shift['tagline'] }}</p>
                            <p class="room__desc">{{ $shift['description'] }}</p>
                            <a href="{{ route('book') }}" class="room__cta">
                                Book to experience this
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                        <div class="room__stage" data-room-stage>
                            @include('partials.rooms.' . $shift['slug'])
                            <p class="room__hint" aria-hidden="true">{{ $shift['hint'] }}</p>
                        </div>
                    </article>
                @endforeach

            </div>

            <div class="rooms__hud" aria-hidden="true">
                <span class="rooms__hud-count">Room <span data-rooms-current>01</span> / {{ str_pad(count($shifts), 2, '0', STR_PAD_LEFT) }}</span>
                <span class="rooms__hud-bar"><span data-rooms-progress></span></span>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     03 — WHO IS IT FOR
     No one should feel left out.
     ══════════════════════════════════════════════════════════ --}}
<section class="audience-section" aria-labelledby="audience-heading">
    <div class="container">

        <div class="audience__intro">
            <h2 class="audience__heading" id="audience-heading" data-split>
                Whoever you bring,<br>they'll be fooled too.
            </h2>
            <p class="audience__sub" data-scrub>
                No experience needed, no age too old. If you have eyes and a brain,
                these rooms were built to trick you — find your reason to come.
            </p>
        </div>

        {{-- Card pile — each card sticks a little lower than the last, so the next one
             slides up over it; audience-stack.js sinks the covered card back.
             Each card leads to booking (or group enquiries). --}}
        @php $total = str_pad(count($audiences), 2, '0', STR_PAD_LEFT); @endphp
        <ol class="audience-stack" data-audience-stack>
            @foreach ($audiences as $i => $audience)
                <li class="audience-stack__item" style="--i: {{ $i }}">
                    <a href="{{ route($audience['route']) }}" class="audience-card audience-card--{{ $i % 3 + 1 }}" data-audience-card>
                        <span class="audience-card__body">
                            <span class="audience-card__count" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }} <span>/ {{ $total }}</span></span>
                            <span class="audience-card__text">
                                <span class="audience-card__name">{{ $audience['name'] }}</span>
                                <span class="audience-card__line">{{ $audience['line'] }}</span>
                            </span>
                            <span class="audience-card__go">
                                {{ $audience['route'] === 'groups' ? 'Enquire for groups' : 'Book your spot' }}
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </span>
                        </span>
                        <span class="audience-card__media">
                            <img src="{{ asset($audience['image']) }}" alt="" class="audience-card__img" loading="lazy" decoding="async">
                        </span>
                    </a>
                </li>
            @endforeach
        </ol>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     04 — SOCIAL PROOF
     Real proof: the Google score, one featured quote, the rest beside it.
     ══════════════════════════════════════════════════════════ --}}
<section class="reviews-section section" id="reviews" aria-labelledby="reviews-heading">
    <div class="container">

        <div class="reviews-section__intro">
            <h2 class="reviews-section__title" id="reviews-heading" data-split>
                Harare can't stop<br>talking about it.
            </h2>
            <p class="reviews-section__tagline" data-scrub>
                Walk in sceptical, walk out arguing about what you saw.
                Here's what visitors said.
            </p>
        </div>

        @php
            $googleRating = config('shiftreality.google_rating');
            $reviewsUrl   = config('shiftreality.google_reviews_url');
            [$lead, $others] = [$testimonials[0] ?? null, array_slice($testimonials, 1)];
        @endphp

        <div class="reviews">
            {{-- Score: the real proof, then the ask --}}
            <div class="reviews__score" data-scrub>
                <p class="reviews__score-value" aria-hidden="true">
                    <span data-count-to="{{ $googleRating }}">{{ number_format($googleRating, 1) }}</span><span class="reviews__score-max">/5</span>
                </p>
                @include('partials.stars', ['rating' => $googleRating, 'size' => 22])
                <p class="reviews__score-source">
                    @include('partials.google-g', ['size' => 18])
                    <span>Rated {{ number_format($googleRating, 1) }} out of 5 on Google Reviews</span>
                </p>
                @if ($reviewsUrl)
                    <a href="{{ $reviewsUrl }}" class="reviews__all" target="_blank" rel="noopener">Read all reviews on Google ↗</a>
                @endif

                <a href="{{ route('book') }}" class="pill pill--amber pill--lg reviews__cta">
                    <span>Book Your Spot</span>
                    <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Quotes: one featured, the rest beside it; each appears exactly once --}}
            <div class="reviews__quotes">
                @if ($lead)
                    <figure class="quote quote--lead" data-scrub>
                        @include('partials.stars', ['rating' => $lead['rating'], 'size' => 16])
                        <span class="sr-only">Rated {{ $lead['rating'] }} out of 5.</span>
                        <blockquote class="quote__text">“{{ $lead['quote'] }}”</blockquote>
                        <figcaption class="quote__who">{{ $lead['name'] }} <span>· {{ $lead['detail'] }}</span></figcaption>
                    </figure>
                @endif

                @if ($others)
                    <div class="reviews__more">
                        @foreach ($others as $t)
                            <figure class="quote" data-scrub>
                                @include('partials.stars', ['rating' => $t['rating'], 'size' => 13])
                                <span class="sr-only">Rated {{ $t['rating'] }} out of 5.</span>
                                <blockquote class="quote__text">“{{ $t['quote'] }}”</blockquote>
                                <figcaption class="quote__who">{{ $t['name'] }} <span>· {{ $t['detail'] }}</span></figcaption>
                            </figure>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     05 — INLINE CTA
     Momentum break — catch them at peak desire.
     ══════════════════════════════════════════════════════════ --}}
@php $ctaStatus = $openingHours->status(); @endphp
<section class="inline-cta" aria-labelledby="inline-cta-heading">

    {{-- Copy on solid navy, left-aligned like every other section --}}
    <div class="inline-cta__panel">
        <div class="inline-cta__inner">
            <p class="inline-cta__overline" data-scrub>Don't wait until it's sold out</p>
            <h2 class="inline-cta__headline" id="inline-cta-heading" data-split>
                Your spot is one click away.
            </h2>
            <p class="inline-cta__sub" data-scrub>
                Most people who visit say they wish they'd come sooner.
                {{-- non-breaking hyphen keeps "Walk-ins" whole --}}
                Walk&#8209;ins are welcome but weekends fill up — secure yours now.
            </p>
            <a href="{{ route('book') }}" class="pill pill--amber pill--xl" data-scrub>
                <span>Book Now — From ${{ $visit['price_from'] }}</span>
                <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <p @class(['inline-cta__note', 'is-open' => $ctaStatus['open']]) data-scrub>
                <span class="inline-cta__dot" aria-hidden="true"></span>
                {{-- &nbsp; keeps each "·" on the line it follows --}}
                {{ $ctaStatus['label'] }}&nbsp;· {{ $ctaStatus['detail'] }}&nbsp;· {{ config('shiftreality.location.address') }}, {{ config('shiftreality.location.city') }}
            </p>
        </div>
    </div>

    {{-- The clocks ("don't wait"), clean: no overlay --}}
    <figure class="inline-cta__media" aria-hidden="true">
        <img
            src="{{ asset('images/hero/hero-3-2400.webp') }}"
            srcset="{{ asset('images/hero/hero-3-1200.webp') }} 1200w,
                    {{ asset('images/hero/hero-3-2400.webp') }} 2400w"
            sizes="(min-width: 900px) 50vw, 100vw"
            alt=""
            loading="lazy"
            decoding="async"
        >
    </figure>
</section>

{{-- ══════════════════════════════════════════════════════════
     06 — VISIT ESSENTIALS
     Remove every last booking objection.
     ══════════════════════════════════════════════════════════ --}}
<section class="visit" id="visit" aria-labelledby="essentials-heading">
    <div class="container">

        @php
            $status   = $openingHours->status();
            $location = config('shiftreality.location');
            $directions = 'https://www.google.com/maps/dir/?api=1&destination='
                . $location['coordinates']['lat'] . ',' . $location['coordinates']['lng'];
        @endphp

        <div class="visit__intro">
            <h2 class="visit__title" id="essentials-heading" data-split>Plan your visit.</h2>
            <p class="visit__sub" data-scrub>Everything you need before you arrive — then just bring your curiosity.</p>
        </div>

        <div class="visit__grid">
            {{-- Live status + hours --}}
            <div class="visit-card visit-card--hours" data-scrub>
                <p @class(['visit-status', 'is-open' => $status['open']])>
                    <span class="visit-status__dot" aria-hidden="true"></span>
                    <span class="visit-status__label">{{ $status['label'] }}</span>
                    <span class="visit-status__detail">{{ $status['detail'] }}</span>
                </p>

                <dl class="visit-hours">
                    @foreach ($openingHours->rows() as $row)
                        <div @class(['visit-hours__row', 'is-today' => $row['today'], 'is-closed' => $row['closed']])>
                            <dt>
                                {{ $row['days'] }}
                                @if ($row['today'])<span class="visit-hours__today">Today</span>@endif
                            </dt>
                            <dd>{{ $row['time'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Location: a real map of the venue (the iframe only loads as it nears the
                 screen), with the address and directions on the card below it --}}
            @php
                $mapEmbed = 'https://www.google.com/maps?q='
                    . $location['coordinates']['lat'] . ',' . $location['coordinates']['lng'] . '&z=16&output=embed';
            @endphp
            <div class="visit-card visit-card--map" data-scrub>
                <div class="visit-map">
                    {{-- src is set by lazy-embeds.js as the section nears the screen,
                         so Google's map isn't loaded for visitors who never get here --}}
                    <iframe
                        class="visit-map__frame"
                        data-lazy-src="{{ $mapEmbed }}"
                        title="Map showing {{ config('shiftreality.name') }} at {{ $location['address'] }}, {{ $location['city'] }}"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                    <noscript>
                        <iframe class="visit-map__frame" src="{{ $mapEmbed }}" title="Map showing {{ config('shiftreality.name') }}" loading="lazy"></iframe>
                    </noscript>
                </div>
                <div class="visit-card__foot">
                    <div>
                        <p class="visit-card__eyebrow">Find us</p>
                        <p class="visit-card__place">{{ $location['address'] }}</p>
                        <p class="visit-card__city">{{ $location['city'] }}, {{ $location['country'] }}</p>
                    </div>
                    <a href="{{ $directions }}" class="visit-card__go" target="_blank" rel="noopener">
                        Get directions
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Quick facts --}}
        <div class="visit-facts">
            <div class="visit-fact" data-scrub>
                <p class="visit-fact__label">Time inside</p>
                <p class="visit-fact__value">{{ $visit['duration'] }}</p>
                <p class="visit-fact__sub">Go at your own pace</p>
            </div>
            <div class="visit-fact" data-scrub>
                <p class="visit-fact__label">Tickets</p>
                <p class="visit-fact__value">From ${{ $visit['price_from'] }}</p>
                <a href="#pricing" class="visit-fact__sub visit-fact__link">See pricing ↓</a>
            </div>
            <div class="visit-fact" data-scrub>
                <p class="visit-fact__label">Ages</p>
                <p class="visit-fact__value">{{ $visit['min_age'] }}+</p>
                <p class="visit-fact__sub">Fun for kids and grown-ups alike</p>
            </div>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     07 — PRICING
     The ticket: one clear price, what's included, and a pointer to groups.
     ══════════════════════════════════════════════════════════ --}}
@php
    $fillPrice = fn ($text) => strtr((string) $text, [
        ':price'    => $visit['price_from'],
        ':duration' => str_replace(' ', "\u{00A0}", $visit['duration']),   // never split "1.5 – 2.5 hours" across lines
        ':min_age'  => $visit['min_age'],
    ]);
@endphp
@php $ticket = config('shiftreality.pricing'); @endphp
<section class="pricing" id="pricing" aria-labelledby="pricing-heading">
    <div class="container">

        <div class="pricing__intro">
            <h2 class="pricing__title" id="pricing-heading" data-split>One ticket.<br>Every room.</h2>
            <p class="pricing__sub" data-scrub>Book online to guarantee your slot, or walk in when there's space — then take your time inside.</p>
        </div>

        <div class="pricing__ticket">
            {{-- The price and the one action --}}
            <div class="pricing__main" data-scrub>
                <h3 class="price__name">{{ $ticket['name'] }}</h3>
                <p class="price__amount">
                    @if ($ticket['price'] !== null)
                        <span class="price__figure">${{ $fillPrice($ticket['price']) }}</span>
                    @else
                        <span class="price__figure">On request</span>
                    @endif
                    <span class="price__unit">{{ $ticket['unit'] }}</span>
                </p>
                <a href="{{ route('book') }}" class="pill pill--amber pill--lg">
                    <span>Book Your Spot</span>
                    <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                {{-- Groups get their own pitch right below; this just points there --}}
                <p class="pricing__groups">Coming as a group? <a href="#groups">Rates on request ↓</a></p>
            </div>

            {{-- What's included: each line rises in with the scroll --}}
            <ul class="price__list" aria-label="Included with every ticket">
                @foreach ($ticket['includes'] as $line)
                    <li data-scrub>{{ $fillPrice($line) }}</li>
                @endforeach
            </ul>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     08 — GROUPS + EVENTS
     ══════════════════════════════════════════════════════════ --}}
@php
    $groups = config('shiftreality.groups');
    $groupWhatsApp = 'https://wa.me/' . config('shiftreality.contact.whatsapp')
        . '?text=' . rawurlencode('Hi Reality Shift! I\'d like to enquire about a group booking.');
@endphp
<section class="groups" id="groups" aria-labelledby="groups-heading">
    <div class="container">

        <div class="groups__grid">

            {{-- Clean photo, nothing laid over it (stand-in until a real group photo).
                 It wipes up into view and settles from a slight zoom. --}}
            <figure class="groups__media" data-reveal>
                <img src="{{ asset($groups['image']) }}" alt="" class="groups__img" loading="lazy" decoding="async">
            </figure>

            {{-- Each piece rises in with the scroll itself (scrub-reveal.js), while the photo stays pinned --}}
            <div class="groups__content">
                <h2 class="groups__heading" id="groups-heading" data-split>
                    Make it a group<br>they'll never forget.
                </h2>
                <p class="groups__sub" data-scrub>
                    Bring the class, the team or the whole party. Groups get exclusive rates
                    and priority entry — tell us your date and we'll handle the rest.
                </p>

                {{-- The two group perks, as plain facts --}}
                <dl class="groups__perks">
                    <div class="groups__perk" data-scrub>
                        <dt>Pricing</dt>
                        <dd>Group rates</dd>
                    </div>
                    <div class="groups__perk" data-scrub>
                        <dt>Arrival</dt>
                        <dd>Priority entry</dd>
                    </div>
                </dl>

                {{-- Occasions: name left, reason right, split by hairlines --}}
                <ul class="groups__occasions">
                    @foreach ($groups['occasions'] as $occasion)
                        <li class="groups__occasion" data-scrub>
                            <h3 class="groups__occasion-title">{{ $occasion['title'] }}</h3>
                            <p class="groups__occasion-line">{{ $occasion['line'] }}</p>
                        </li>
                    @endforeach
                </ul>

                <div class="groups__actions" data-scrub>
                    <a href="{{ route('groups') }}" class="pill pill--amber pill--lg">
                        <span>Enquire for Groups</span>
                        <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ $groupWhatsApp }}" class="groups__wa" target="_blank" rel="noopener">
                        Or ask on WhatsApp →
                    </a>
                </div>
                <p class="groups__note" data-scrub>Weekend slots fill fast — enquire early.</p>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     09 — FAQ
     The last doubts, cleared right before the final ask.
     ══════════════════════════════════════════════════════════ --}}
<section class="faq-section" id="faq" aria-labelledby="faq-heading">
    <div class="container">

        <div class="visit-faq">
            <div class="visit-faq__aside" data-scrub>
                <h2 class="visit-faq__title" id="faq-heading" data-split>Good to know.</h2>
                <p class="visit-faq__sub">The questions people ask most before they book.</p>
                <span class="visit-faq__links">
                    <a href="{{ route('faq') }}" class="visit__more">See all questions →</a>
                    <a href="{{ route('visit') }}" class="visit__more">Full visitor info →</a>
                </span>
            </div>

            <div class="faq" role="list">
                @foreach(array_slice($faq, 0, 4) as $i => $item)
                {{-- each question rises in with the scroll --}}
                <div class="faq__item" role="listitem" data-scrub>
                    <button class="faq__question" aria-expanded="false" aria-controls="faq-a-{{ $i }}" id="faq-q-{{ $i }}">
                        {{ $item['question'] }}
                        <span class="faq__icon" aria-hidden="true"></span>
                    </button>
                    <div class="faq__answer" id="faq-a-{{ $i }}" role="region" aria-labelledby="faq-q-{{ $i }}">
                        <div class="faq__answer-clip">
                            <p class="faq__answer-inner">{{ $item['answer'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     10 — BRAND BANNER
     ══════════════════════════════════════════════════════════ --}}
<div class="brand-banner" aria-hidden="true" data-brand-banner>
    <div class="brand-banner__track">
        {{-- Two identical sets, so the loop is seamless. brand-banner.js ties the drift
             to scrolling; "Everything" turns upside down as it crosses the centre. --}}
        @foreach ([0, 1] as $copy)
            <span class="brand-banner__set">
                <span class="brand-banner__word">Reality</span>
                <span class="brand-banner__sep"></span>
                <em class="brand-banner__word brand-banner__word--accent">Shift</em>
                <span class="brand-banner__sep"></span>
                <span class="brand-banner__word brand-banner__word--outline">Harare</span>
                <span class="brand-banner__sep"></span>
                <span class="brand-banner__word">Question</span>
                <span class="brand-banner__sep"></span>
                <em class="brand-banner__word brand-banner__word--accent brand-banner__word--flip" data-flip>Everything</em>
                <span class="brand-banner__sep"></span>
            </span>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     11 — FINAL CTA
     The urgency close: dark, cinematic, flowing into the footer.
     ══════════════════════════════════════════════════════════ --}}
@php
    $closeStatus = $openingHours->status();
    $closeRating = config('shiftreality.google_rating');
    $closeGeo    = config('shiftreality.location.coordinates');
    $closeCoords = sprintf('%.4f° %s · %.4f° %s',
        abs($closeGeo['lat']), $closeGeo['lat'] < 0 ? 'S' : 'N',
        abs($closeGeo['lng']), $closeGeo['lng'] < 0 ? 'W' : 'E');
    $closeTz     = config('shiftreality.timezone');
@endphp
<section class="final-booking" id="book" aria-labelledby="final-heading">

    {{-- Atmosphere: two ring fields drifting into a moiré, under a fine grain.
         The layer eases out toward the bottom so the footer takes over without a seam. --}}
    <div class="final-booking__atmos" aria-hidden="true">
    <svg class="final-booking__moire" viewBox="-400 -400 800 800" aria-hidden="true">
        <defs>
            <g id="final-rings">
                @for ($r = 8; $r <= 400; $r += 8)
                    <circle r="{{ $r }}" />
                @endfor
            </g>
        </defs>
        <use href="#final-rings" class="final-booking__rings" />
        <use href="#final-rings" class="final-booking__rings final-booking__rings--drift" />
    </svg>
    <div class="final-booking__grain"></div>
    </div>

    <div class="container final-booking__inner">

        {{-- Where and when, to the minute --}}
        <div class="final-booking__meta" data-scrub>
            <span class="final-booking__coords">{{ $closeCoords }}</span>
            <span @class(['final-booking__status', 'is-open' => $closeStatus['open']])>
                <span class="final-booking__dot" aria-hidden="true"></span>
                {{ $closeStatus['label'] }}&nbsp;· {{ $closeStatus['detail'] }}
            </span>
            <span>{{ config('shiftreality.location.city') }} <time data-local-clock data-tz="{{ $closeTz }}">{{ now($closeTz)->format('H:i') }}</time></span>
        </div>

        {{-- Each line rises out of its own mask --}}
        <h2 class="final-booking__title" id="final-heading" data-reveal>
            <span class="final-booking__mask"><span class="final-booking__line">Don't be the last</span></span>
            <span class="final-booking__mask final-booking__mask--end"><em class="final-booking__line">one to find out.</em></span>
        </h2>

        <div class="final-booking__foot">
            <div class="final-booking__copy" data-scrub>
                <p class="final-booking__sub">
                    Weekends book out. Walk-ins are welcome, but a spot isn't guaranteed.
                </p>
                <ul class="final-booking__proof">
                    <li>
                        @include('partials.google-g', ['size' => 16])
                        <span>{{ number_format($closeRating, 1) }}</span>
                        @include('partials.stars', ['rating' => $closeRating, 'size' => 12])
                        <span class="sr-only">out of 5 on Google Reviews</span>
                    </li>
                    <li>Ages {{ $visit['min_age'] }}+</li>
                    <li>Photos encouraged</li>
                </ul>
                <a href="https://wa.me/{{ config('shiftreality.contact.whatsapp') }}" class="final-booking__wa" target="_blank" rel="noopener">
                    Or ask us on WhatsApp <span aria-hidden="true">→</span>
                </a>
            </div>

            {{-- The button: a disc with its label orbiting it --}}
            <div class="final-booking__orbit-wrap" data-scrub>
                <a href="{{ route('book') }}" class="orbit" aria-label="Book your spot, from ${{ $visit['price_from'] }}">
                    <svg class="orbit__ring" viewBox="0 0 200 200" aria-hidden="true">
                        <defs>
                            <path id="orbit-path" d="M100,100 m-80,0 a80,80 0 1,1 160,0 a80,80 0 1,1 -160,0" />
                        </defs>
                        <text><textPath href="#orbit-path" textLength="500" lengthAdjust="spacing">Book your spot · From ${{ $visit['price_from'] }} · Book your spot · From ${{ $visit['price_from'] }} ·</textPath></text>
                    </svg>
                    <span class="orbit__core" aria-hidden="true">
                        <svg class="arrow" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M8 7h9v9"/></svg>
                    </span>
                </a>
            </div>
        </div>

    </div>
</section>

@endsection
