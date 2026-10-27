@extends('layouts.app')

{{-- The hero is light (after Artora), so the nav keeps its dark-on-light look,
     under the same top bar as every page. --}}

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
{{-- Hero after Artora (artora.framer.website): a centred headline and one action,
     over a ring of photo cards seen from inside a turning cylinder — the cards at
     the edges come closer and lean in (hero-ring.js). Flat light background,
     our type, our navy pill. As on Artora, the ring runs off the bottom of the
     first screen and the big edge cards rise beside the headline.
     ⚠ Cards are stand-ins except the kaleidoscope room (ours, from socials,
     AI-upscaled from a 387px copy, shown upside down as a wink) — replace them
     with real venue photos, and confirm the visitors' consent. --}}
@php
    $rating = config('shiftreality.google_rating');
    // [src, srcset, upside down?] — our photo first, then each room's stand-in, then the extras
    $ours = collect($shifts)
        ->filter(fn ($s) => ! empty($s['hero_photo']))
        ->map(fn ($s) => [$s['hero_photo'], asset(str_replace('-2064.', '-1000.', $s['hero_photo'])) . ' 750w, ' . asset($s['hero_photo']) . ' 1548w', true])
        ->values();
    $standIns = collect($shifts)->pluck('photo')->merge(config('shiftreality.hero_extras'))->filter()
        ->map(fn ($p) => [$p, asset($p) . ' 800w, ' . asset(str_replace('-800.', '-1600.', $p)) . ' 1600w', false])
        ->values();
    // Twelve cards fill the ring. Our photos appear once each; the stand-ins
    // repeat to fill the rest (short screens show up to ten cards at once)
    $ringCards = $ours->merge(collect(range(0, 11 - $ours->count()))->map(fn ($i) => $standIns[$i % $standIns->count()]));
@endphp
<section class="wh-hero" id="hero" aria-labelledby="hero-heading">

    <div class="wh-hero__intro">
        <p class="wh-hero__eyebrow">Zimbabwe&rsquo;s first optical illusion experience</p>

        <h1 class="wh-hero__title" id="hero-heading">
            <span class="wh-hero__line"><span>Your brain is about</span></span>
            <span class="wh-hero__line"><span>to be <span class="wh-hero__accent">lied to.</span></span></span>
        </h1>

        <div class="wh-hero__actions">
            <a href="{{ route('book') }}" class="pill pill--amber pill--lg">
                <span>Book Your Spot</span>
                <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>

            <span class="wh-hero__rating" role="img" aria-label="Rated {{ $rating }} out of 5, based on Google Reviews">
                @include('partials.google-g', ['size' => 12, 'class' => 'wh-hero__rating-g'])
                <span class="wh-hero__rating-score">{{ number_format($rating, 1) }}</span>
                @include('partials.stars', ['rating' => $rating, 'size' => 10])
                <span>Google Reviews</span>
            </span>
        </div>
    </div>

    <div class="wh-hero__ring" data-hero-ring>
        <div class="wh-hero__ring-stage" aria-hidden="true">
            @foreach ($ringCards as $i => [$src, $srcset, $flip])
                <div @class(['wh-hero__card', 'wh-hero__card--flip' => $flip]) style="--i: {{ $i }}">
                    <img
                        src="{{ asset($src) }}"
                        srcset="{{ $srcset }}"
                        sizes="(max-width: 900px) 220px, 360px"
                        alt=""
                        decoding="async"
                        @if ($i > 5) loading="lazy" @endif
                    >
                </div>
            @endforeach
        </div>
    </div>

    {{-- Scroll down: floats over the cards just above the foot of the first
         screen, so it's seen on arrival (not only once you've scrolled) --}}
    <a href="#rooms" class="wh-hero__down" aria-label="Scroll down to the rooms">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
    </a>

</section>

{{-- ══════════════════════════════════════════════════════════
     02 — THE ROOMS
     After Artora's featured exhibitions, on the hero's off-white so the
     two run together: eyebrow + heading left, a line and Book right, then
     a 2×2 grid of room cards. Each card's stage is that room's live
     illusion (rooms.js plays it as the card scrolls up, or runs it while
     it's on screen), with "• Room 01 · tagline" and the room's name.
     ══════════════════════════════════════════════════════════ --}}
<section class="rooms" id="rooms" aria-labelledby="rooms-heading">
    <div class="container">

        <div class="rooms__intro">
            <div>
                <p class="rooms__eyebrow">The rooms</p>
                <h2 class="rooms__heading" id="rooms-heading" data-split>
                    Four rooms.<br>Zero explanations.
                </h2>
            </div>
            <div class="rooms__aside">
                <p class="rooms__sub">
                    Each room is designed to make you question something you've taken for granted your entire life.
                    You'll walk out differently than you walked in.
                </p>
                <a href="{{ route('book') }}" class="pill pill--amber pill--lg">
                    <span>Book Your Spot</span>
                    <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <div class="rooms__grid" data-rooms>
            @foreach ($shifts as $shift)
                <article class="room-card" data-room="{{ $shift['slug'] }}" aria-labelledby="room-{{ $shift['slug'] }}" data-scrub>
                    <div @class(['room-card__stage', 'room-card__stage--dark' => $shift['slug'] === 'infinity']) data-room-stage>
                        @include('partials.rooms.' . $shift['slug'])
                        <p class="room-card__hint" aria-hidden="true">{{ $shift['hint'] }}</p>
                    </div>
                    <div class="room-card__body">
                        <p class="room-card__meta">
                            <span class="room-card__num">Room {{ $shift['number'] }}</span>
                            <span>{{ $shift['tagline'] }}</span>
                        </p>
                        <h3 class="room-card__title" id="room-{{ $shift['slug'] }}">
                            <a href="{{ route('experience') }}">{{ $shift['title'] }}</a>
                        </h3>
                    </div>
                </article>
            @endforeach
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     03 — VISITOR MOMENTS
     After Artora's "Permanent Collections": eyebrow + heading centred, then
     a stacked carousel — one big photo in the middle, the next ones each
     side smaller and behind it — with prev/next round the caption
     ("• Room 02" + the room). Moves only when asked (moments.js).
     ⚠ Only the kaleidoscope photo is ours (visitors, from socials); the rest
     are the Unsplash stand-ins — replace with real visitor photos.
     ══════════════════════════════════════════════════════════ --}}
@php
    // [image, srcset, upside down?, meta, title]
    $momentSlides = collect($shifts)->filter(fn ($s) => ! empty($s['hero_photo']))
        ->map(fn ($s) => [str_replace('-2064.', '-1000.', $s['hero_photo']), asset(str_replace('-2064.', '-1000.', $s['hero_photo'])) . ' 750w, ' . asset($s['hero_photo']) . ' 1548w', true, 'Room ' . $s['number'], $s['title']])
        ->merge(collect($shifts)->filter(fn ($s) => ! empty($s['photo']))
            ->map(fn ($s) => [$s['photo'], asset($s['photo']) . ' 800w, ' . asset(str_replace('-800.', '-1600.', $s['photo'])) . ' 1600w', false, 'Room ' . $s['number'], $s['title']]))
        ->merge(collect(config('shiftreality.hero_extras'))
            ->map(fn ($p) => [$p, asset($p) . ' 800w, ' . asset(str_replace('-800.', '-1600.', $p)) . ' 1600w', false, 'Photos encouraged', 'Every corner is a backdrop']))
        ->values();
@endphp
<section class="moments" aria-labelledby="moments-heading">
    <div class="container">
        <div class="moments__intro">
            <p class="moments__eyebrow">Visitor moments</p>
            <h2 class="moments__heading" id="moments-heading" data-split>
                Come for the illusions.<br>Leave with lifetime memories.
            </h2>
        </div>
    </div>

    <div class="moments__carousel" data-moments>
        <div class="moments__stage">
            @foreach ($momentSlides as $i => [$src, $srcset, $flip, $meta, $title])
                <figure @class(['moments__slide', 'moments__slide--flip' => $flip])
                        data-meta="{{ $meta }}" data-title="{{ $title }}" style="--i: {{ $i }}">
                    <img src="{{ asset($src) }}" srcset="{{ $srcset }}" sizes="(max-width: 700px) 70vw, 500px"
                         alt="" loading="lazy" decoding="async">
                </figure>
            @endforeach
        </div>

        <div class="moments__nav">
            <button type="button" class="moments__arrow" data-moments-prev aria-label="Previous photo">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <div class="moments__caption" aria-live="polite">
                <p class="moments__meta"><span data-moments-meta>{{ $momentSlides[0][3] }}</span></p>
                <p class="moments__title" data-moments-title>{{ $momentSlides[0][4] }}</p>
            </div>
            <button type="button" class="moments__arrow" data-moments-next aria-label="Next photo">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     04 — INLINE CTA
     The photo-collage band (partials/visit-band), mid-page.
     ⚠ Photos are the hero ring's stand-ins, bar ours.
     ══════════════════════════════════════════════════════════ --}}
@include('partials.visit-band', [
    'headingId' => 'inline-cta-heading',
    'eyebrow'   => "Don't wait until it's sold out",
    'title'     => 'Your spot is one click away.',
    {{-- non-breaking hyphen keeps "Walk-ins" whole --}}
    'sub'       => 'Most people who visit say they wish they\'d come sooner. Walk&#8209;ins are welcome but weekends fill up — secure yours now.',
    'pill'      => 'Book Now — From $' . $visit['price_from'],
])

{{-- ══════════════════════════════════════════════════════════
     05 — SOCIAL PROOF
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
     06 — ABOUT REALITY SHIFT
     After Artora's About band: solid deep navy, full bleed. Left: the
     eyebrow, a photo and where we are. Right: the line, a paragraph, one
     white pill, a hairline and two numbers that count up. Facts only from
     config (four rooms, the Google rating) — no invented history.
     ⚠ The photo is ours (visitors, from socials, AI-upscaled).
     ══════════════════════════════════════════════════════════ --}}
@php
    $aboutPhoto = collect($shifts)->firstWhere(fn ($s) => ! empty($s['hero_photo']))['hero_photo'] ?? null;
    $aboutRating = config('shiftreality.google_rating');
@endphp
<section class="about" id="about" aria-labelledby="about-heading">
    <div class="container about__grid">

        <div class="about__side">
            <p class="about__eyebrow">About Reality Shift</p>
            @if ($aboutPhoto)
                <figure class="about__photo">
                    <img
                        src="{{ asset(str_replace('-2064.', '-1000.', $aboutPhoto)) }}"
                        srcset="{{ asset(str_replace('-2064.', '-1000.', $aboutPhoto)) }} 750w, {{ asset($aboutPhoto) }} 1548w"
                        sizes="(max-width: 900px) 90vw, 340px"
                        alt="Visitors in the Reality Shift kaleidoscope room"
                        loading="lazy"
                        decoding="async"
                    >
                </figure>
            @endif
            <p class="about__where">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                {{ config('shiftreality.location.address') }}, {{ config('shiftreality.location.city') }}
            </p>
        </div>

        <div class="about__main">
            <h2 class="about__heading" id="about-heading" data-split>
                More than a museum.<br>Where reality bends.
            </h2>
            <p class="about__text">
                Reality Shift is Zimbabwe's first optical illusion experience: {{ count($shifts) }} rooms built to make you
                question something you've taken for granted your entire life. No experience needed, no age too old —
                bring your eyes, your people and a camera.
            </p>
            <a href="{{ route('experience') }}" class="pill pill--amber pill--lg about__pill">
                <span>Explore the rooms</span>
                <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>

            <dl class="about__stats">
                <div>
                    <dt>Mind-bending rooms</dt>
                    <dd data-count-to="{{ count($shifts) }}">{{ count($shifts) }}</dd>
                </div>
                <div>
                    <dt>Google rating</dt>
                    <dd><span data-count-to="{{ number_format($aboutRating, 1) }}">{{ number_format($aboutRating, 1) }}</span><span class="about__unit">/5</span></dd>
                </div>
            </dl>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     07 — PRICING
     One ticket, drawn as one: a deep-navy stub with the price, the action
     and the live status; a perforated tear; the rest of the ticket lists
     what it buys, with a pointer to group rates. (The old "Plan your visit"
     section is gone — hours and directions live in the footer.)
     ══════════════════════════════════════════════════════════ --}}
@php
    $fillPrice = fn ($text) => strtr((string) $text, [
        ':price'    => $visit['price_from'],
        ':duration' => str_replace(' ', "\u{00A0}", $visit['duration']),   // never split "1.5 – 2.5 hours" across lines
        ':min_age'  => $visit['min_age'],
    ]);
    $ticket = config('shiftreality.pricing');
    $ticketStatus = $openingHours->status();
@endphp
<section class="pricing" id="pricing" aria-labelledby="pricing-heading">
    <div class="container">

        <div class="pricing__intro">
            <div>
                <p class="pricing__eyebrow">Tickets</p>
                <h2 class="pricing__title" id="pricing-heading" data-split>One ticket.<br>Every room.</h2>
            </div>
            <p class="pricing__sub">Book online to guarantee your slot, or walk in when there's space — then take your time inside.</p>
        </div>

        <div class="ticket" data-scrub>
            {{-- The stub: price, the one action, and whether we're open right now --}}
            <div class="ticket__stub">
                <h3 class="ticket__name">{{ $ticket['name'] }}</h3>
                <p class="ticket__price">
                    @if ($ticket['price'] !== null)
                        <span class="ticket__figure">${{ $fillPrice($ticket['price']) }}</span>
                    @else
                        <span class="ticket__figure">On request</span>
                    @endif
                    <span class="ticket__unit">{{ $ticket['unit'] }}</span>
                </p>
                <a href="{{ route('book') }}" class="pill pill--amber pill--lg ticket__pill">
                    <span>Book Your Spot</span>
                    <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <p @class(['ticket__status', 'is-open' => $ticketStatus['open']])>
                    <span class="ticket__dot" aria-hidden="true"></span>
                    {{ $ticketStatus['label'] }}&nbsp;· {{ $ticketStatus['detail'] }}
                </p>
            </div>

            {{-- The tear: a dashed line between two notches --}}
            <span class="ticket__tear" aria-hidden="true"></span>

            {{-- What it buys --}}
            <div class="ticket__body">
                <p class="ticket__label">Included with every ticket</p>
                <ul class="ticket__list">
                    @foreach ($ticket['includes'] as $line)
                        <li>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                            <span>{{ $fillPrice($line) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Groups, in one strip under the ticket (was its own section): the
             occasions from config, the two perks, and the enquiry on WhatsApp --}}
        @php
            $groupWhatsApp = 'https://wa.me/' . config('shiftreality.contact.whatsapp')
                . '?text=' . rawurlencode('Hi Reality Shift! I\'d like to enquire about a group booking.');
        @endphp
        <div class="group-strip" id="groups" data-scrub>
            <div class="group-strip__copy">
                <h3 class="group-strip__title">Coming as a group?</h3>
                <p class="group-strip__line">Group rates and priority entry — tell us your date and we'll handle the rest.</p>
                <ul class="group-strip__tags" aria-label="Popular group occasions">
                    @foreach (config('shiftreality.groups.occasions') as $occasion)
                        <li>{{ $occasion['title'] }}</li>
                    @endforeach
                </ul>
            </div>
            <a href="{{ $groupWhatsApp }}" class="pill pill--amber pill--lg group-strip__pill" target="_blank" rel="noopener">
                <span>Enquire on WhatsApp</span>
                <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     08 — FAQ
     The last doubts, cleared right before the final ask. Centred eyebrow +
     heading, then every question as its own rounded card, numbered; the open
     one turns white with a hairline. Then a way to ask anything else.
     (Scoped as .faq-home: /pricing keeps its own FAQ look.)
     ══════════════════════════════════════════════════════════ --}}
@php
    $faqWhatsApp = 'https://wa.me/' . config('shiftreality.contact.whatsapp')
        . '?text=' . rawurlencode('Hi Reality Shift! I have a question about visiting.');
@endphp
<section class="faq-home" id="faq" aria-labelledby="faq-heading">
    <div class="container">

        <div class="faq-home__intro">
            <p class="faq-home__eyebrow">FAQ</p>
            <h2 class="faq-home__title" id="faq-heading" data-split>Good to know.</h2>
            <p class="faq-home__sub">The questions people ask most before they book.</p>
        </div>

        <div class="faq faq-home__list" role="list">
            @foreach ($faq as $i => $item)
                <div class="faq__item" role="listitem" data-scrub>
                    <button class="faq__question" aria-expanded="false" aria-controls="faq-a-{{ $i }}" id="faq-q-{{ $i }}">
                        <span class="faq-home__num" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="faq-home__q">{{ $item['question'] }}</span>
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

        <div class="faq-home__more">
            <p>Still have a question?</p>
            <a href="{{ $faqWhatsApp }}" class="pill pill--amber pill--lg" target="_blank" rel="noopener">
                <span>Ask us on WhatsApp</span>
                <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     09 — FINAL CTA
     The photo-collage band again, with closing words and the collage
     turned round so it doesn't repeat the one above. Hands over to the
     (light) footer.
     ══════════════════════════════════════════════════════════ --}}
@include('partials.visit-band', [
    'id'        => 'visit-today',
    'headingId' => 'final-heading',
    'eyebrow'   => 'Visit today',
    'title'     => 'Question everything.<br>Starting today.',
    'sub'       => count($shifts) . ' rooms at ' . e(config('shiftreality.location.address')) . ', ' . e(config('shiftreality.location.city'))
                 . '. Entry from $' . $visit['price_from'] . ', ages ' . $visit['min_age'] . '&nbsp;and&nbsp;up.',
    'pill'      => 'Book Your Spot',
    'offset'    => 4,
])

@endsection
