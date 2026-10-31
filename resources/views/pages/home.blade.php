@extends('layouts.app')

{{-- The hero is the Infinity Room, deep navy: the header rests light-on-dark. --}}
@section('body_class', 'has-hero-photo')

@section('title', 'Reality Shift · Zimbabwe\'s Optical Illusion Experience · Harare')
@section('description', 'Your brain is about to be lied to. Mind-bending illusion rooms at Westgate Shopping Complex, Harare. Zimbabwe\'s first optical illusion experience. Entry from $15. Book now.')

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
     You land on a stairwell that never ends, drifting slowly down, with
     the rating, title and line bottom left, a booking card on the right
     (partials/hero-book: day, time, guests, name → WhatsApp), and under a
     faint line a link to every date (on phones, a short day + guests form).
     Built as slides after arlink28.com: add a room to $heroSlides and they
     take turns every 7s, switching through 30 horizontal blinds opening
     from the bottom up, with the count, thumbnails with progress lines
     and pause in the bar.
     Scrolling pins the hero (walk.js): the copy lifts away, you go down
     floor after floor, and the frame splits into a slice per room card; the slices fly
     to the cards' places and flip over into them (walk.js pulls the rooms
     up under the hero and holds them), so the hero hands straight on to
     the row.
     Without motion it's just the landing, then the rooms.
     ══════════════════════════════════════════════════════════ --}}
@php $rating = config('shiftreality.google_rating'); @endphp
<section class="walk" id="hero" aria-labelledby="hero-heading" data-walk>
    <div class="walk__stage">

        {{-- The landing's slides. One for now: the stairwell (slide 1, whose copy
             holds the h1, is written out below). Each further slide is a room,
             added here once there are real venue photos — 'rows' is its
             two-line title (the last row's 'accent' in blue), 'line' its line,
             'bg' its full-screen photo. With two or more, the bar shows the
             count, thumbnails with progress lines and pause, and they take turns. --}}
        @php
            // The picture drawn inside itself (stairwell.js), with what was measured on
            // it: its centre ('well'), its step in ('f'), its outer loop ('edge'); and its
            // opacity on the navy, wide / narrow ('glow': the white copy must read).
            // Testing the tunnel; ?bg=stairwell shows the stairwell to compare.
            $heroBgs = [
                'tunnel' => [
                    // baked darker (multiplied, so it stays crisp) and enlarged once, offline
                    'src' => 'images/walk/tunnel-2560.webp', 'thumb' => 'images/walk/tunnel-thumb.webp',
                    'label' => 'The endless tunnel', 'blurb' => 'A mirrored tunnel that keeps going, however far you go.',
                    'well' => ['well' => [0.5, 0.569], 'f' => 0.85, 'mode' => 'zoom'], 'glow' => [1, 1],
                ],
                'stairwell' => [
                    'src' => 'images/walk/stairwell-2560.webp', 'thumb' => 'images/walk/stairwell-thumb.webp',
                    'label' => 'The endless stair', 'blurb' => 'A stairwell that keeps going down, however far you go.',
                    'well' => [], 'glow' => [0.44, 0.4],
                ],
            ];
            $heroBg = $heroBgs[request('bg')] ?? $heroBgs['tunnel'];
            $heroSlides = [
                ['label' => $heroBg['label'], 'blurb' => $heroBg['blurb'], 'thumb' => $heroBg['thumb']],
                // ['label' => 'Infinity Room', 'blurb' => '…', 'thumb' => 'images/…-thumb.webp', 'bg' => 'images/…-2000.webp',
                //  'rows' => ['…', '…'], 'accent' => '…', 'line' => '…'],
            ];
            $multiSlide = count($heroSlides) > 1;
            $slideCount = str_pad(count($heroSlides), 2, '0', STR_PAD_LEFT);
        @endphp
        <div class="walk__intro" data-walk-intro>
            <div class="walk__lead">
                <span class="walk__rating" role="img" aria-label="Rated {{ $rating }} out of 5, based on Google Reviews">
                    @include('partials.google-g', ['size' => 18, 'class' => 'walk__rating-g'])
                    <span class="walk__rating-score">{{ number_format($rating, 1) }}</span>
                    @include('partials.stars', ['rating' => $rating, 'size' => 14])
                    <span>Google Reviews</span>
                </span>

                <div class="walk__slides">
                    <div class="walk__slide is-on" data-walk-slide>
                        <h1 class="walk__intro-title" id="hero-heading">
                            <span class="walk__row"><span>Your brain is about</span></span>
                            <span class="walk__row"><span>to be <span class="walk__accent">lied to.</span></span></span>
                        </h1>
                        <p class="walk__intro-sub">Zimbabwe&rsquo;s first optical illusion experience, at {{ config('shiftreality.location.address') }}, {{ config('shiftreality.location.city') }}.</p>
                    </div>
                    @foreach (array_slice($heroSlides, 1) as $s)
                        <div class="walk__slide walk__slide--live" data-walk-slide aria-hidden="true">
                            <p class="walk__intro-title">
                                <span class="walk__row"><span>{{ $s['rows'][0] }}</span></span>
                                <span class="walk__row"><span>{{ $s['rows'][1] }} <span class="walk__accent">{{ $s['accent'] }}</span></span></span>
                            </p>
                            <p class="walk__intro-sub">{{ $s['line'] }}</p>
                        </div>
                    @endforeach
                </div>

            </div>

            {{-- Book right here: the card on the right (partials/hero-book) --}}
            @include('partials.hero-book')

            {{-- Under the faint line (with two or more slides the line is the
                 bar's): every date and groups on /book; on phones, where the card
                 doesn't fit, a short form instead — day and guests, then /book
                 opens with them filled in --}}
            @php $openDays = collect($days)->filter(fn ($d) => $d['slots'])->values(); @endphp
            <div class="walk__cta">
                <a href="{{ route('book') }}" class="walk__all">See all dates &amp; group visits <span aria-hidden="true">&rarr;</span></a>
                @if ($openDays->isNotEmpty())
                    <form class="walk__quick" method="GET" action="{{ route('book') }}" aria-label="Start a booking">
                        <label class="walk__field">
                            <span class="walk__field-label">Day</span>
                            <select name="date">
                                @foreach ($openDays as $day)
                                    <option value="{{ $day['date'] }}">{{ $day['today'] ? 'Today' : \Carbon\Carbon::parse($day['date'])->format('D j M') }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="walk__field walk__field--guests">
                            <span class="walk__field-label">Guests</span>
                            <select name="people">
                                @for ($n = 1; $n <= $booking['max_people']; $n++)
                                    <option value="{{ $n }}">{{ $n }}</option>
                                @endfor
                            </select>
                        </label>
                        <button type="submit" class="pill pill--amber pill--lg walk__book"><span>Book My Spot</span><svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
                    </form>
                @endif
            </div>

            {{-- The slider bar, after arlink28.com's: under a faint line, the slide's
                 caption and count (rolling in) and blurb on the left; thumbnails
                 with a filling progress line, and pause, on the right. Only with
                 two or more slides --}}
            @if ($multiSlide)
            <div class="walk__bar" data-walk-bar>
                <div class="walk__bar-info" aria-live="polite">
                    <p class="walk__bar-line">
                        <span class="walk__roll" data-walk-roll>
                            @foreach ($heroSlides as $i => $s)
                                <span @class(['is-on' => $i === 0])>Illusion <i>|</i> {{ $s['label'] }}</span>
                            @endforeach
                        </span>
                        <span class="walk__bar-count">
                            <span class="walk__roll" data-walk-roll>
                                @foreach ($heroSlides as $i => $s)
                                    <span @class(['is-on' => $i === 0])>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                @endforeach
                            </span>
                            <i>/</i> {{ $slideCount }}
                        </span>
                    </p>
                    <p class="walk__bar-blurb walk__roll" data-walk-roll>
                        @foreach ($heroSlides as $i => $s)
                            <span @class(['is-on' => $i === 0])>{{ $s['blurb'] }}</span>
                        @endforeach
                    </p>
                </div>
                <div class="walk__bar-nav">
                    <ul class="walk__thumbs">
                        @foreach ($heroSlides as $i => $s)
                            <li>
                                <button type="button" @class(['is-on' => $i === 0]) data-walk-thumb="{{ $i }}" aria-label="Show slide {{ $i + 1 }}: {{ $s['label'] }}">
                                    <span class="walk__thumb-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}.</span>
                                    <span class="walk__thumb-frame"><img src="{{ asset($s['thumb']) }}" alt="" width="240" height="160" loading="lazy"></span>
                                    <span class="walk__thumb-bar"><span data-walk-progress></span></span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <button type="button" class="walk__pause" aria-label="Pause slides" aria-pressed="false" data-walk-pause>
                        <svg class="walk__pause-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14M16 5v14"/></svg>
                        <svg class="walk__play-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4.5v15l12-7.5z"/></svg>
                    </button>
                </div>
            </div>
            @endif
        </div>

        {{-- The well that never ends (stairwell.js): $heroBg, drawn inside itself,
             dimmed on the navy (walk.js) --}}
        <canvas class="walk__well is-on" aria-hidden="true" data-walk-well data-walk-bg
                data-src="{{ asset($heroBg['src']) }}"
                data-well='@json((object) $heroBg['well'])' data-glow='@json($heroBg['glow'])'></canvas>

        {{-- Each further slide's photo, under the copy (see $heroSlides) --}}
        @foreach (array_slice($heroSlides, 1) as $s)
            <img class="walk__slide-bg" data-walk-bg src="{{ asset($s['bg']) }}" alt="" aria-hidden="true" decoding="async" loading="lazy">
        @endforeach

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     02 — THE ROOMS
     On the off-white: eyebrow + heading left, a line and a quiet link to
     the rooms right, then the rooms as one row of upright cards, the
     whole set in one screen (2 × 2 on tablets, a swipe row on phones).
     Each card's stage is that room's live illusion: rooms.js turns it as
     the card scrolls up, and the looping ones play on hover (on screen,
     on touch), with the others dimming; then "• tagline" and the name.
     ══════════════════════════════════════════════════════════ --}}
<section class="rooms" id="rooms" aria-labelledby="rooms-heading">
    <div class="container">

        <div class="rooms__intro">
            <div>
                <p class="rooms__eyebrow">The rooms</p>
                <h2 class="rooms__heading" id="rooms-heading" data-split>
                    Rooms that lie.<br>Zero explanations.
                </h2>
            </div>
            <div class="rooms__aside">
                <p class="rooms__sub">
                    Each room is designed to make you question something you've taken for granted your entire life.
                    You'll walk out differently than you walked in.
                </p>
                <a href="{{ route('experience') }}" class="rooms__more">Explore the rooms <span aria-hidden="true">&rarr;</span></a>
            </div>
        </div>

        <div class="rooms__grid" data-rooms>
            @foreach ($shifts as $shift)
                <article class="room-card" data-room="{{ $shift['slug'] }}" aria-labelledby="room-{{ $shift['slug'] }}" data-walk-card
                         @if (! empty($shift['photo_real'])) data-room-photo @endif>
                    <div @class(['room-card__stage', 'room-card__stage--dark' => $shift['slug'] === 'infinity']) data-room-stage>
                        @if (! empty($shift['photo_real']))
                            {{-- A real photo of the room, in place of its drawing --}}
                            <img class="room-card__photo" src="{{ asset($shift['photo']) }}"
                                 srcset="{{ asset($shift['photo']) }} 800w, {{ asset(str_replace('-800.', '-1600.', $shift['photo'])) }} 1086w"
                                 sizes="(max-width: 600px) 80vw, (max-width: 1100px) 45vw, 25vw"
                                 alt="{{ $shift['photo_alt'] ?? $shift['title'] }}" loading="lazy" decoding="async">
                        @else
                            @include('partials.rooms.' . $shift['slug'])
                            <p class="room-card__hint" aria-hidden="true">{{ $shift['hint'] }}</p>
                        @endif
                    </div>
                    <div class="room-card__body">
                        <p class="room-card__meta">
                            <span class="room-card__tag">{{ $shift['tagline'] }}</span>
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
     ("• In the room" + the room). Moves only when asked (moments.js).
     Ours only: it opens on the front desk (config moments_lead), then the
     kaleidoscope, our visitors' photos (visitor_photos), the rooms' photos
     and the extra ones (hero_extras); no stand-ins.
     ══════════════════════════════════════════════════════════ --}}
@php
    // [image, srcset, upside down?, meta, title]
    $momentLead = config('shiftreality.moments_lead');
    $momentSlides = collect($momentLead ? [[$momentLead[0], asset($momentLead[0]) . ' 800w, ' . asset(str_replace('-800.', '-1600.', $momentLead[0])) . ' 1001w', false, $momentLead[1], $momentLead[2]]] : [])
        ->merge(collect($shifts)->filter(fn ($s) => ! empty($s['hero_photo']))
            ->map(fn ($s) => [str_replace('-2064.', '-1000.', $s['hero_photo']), asset(str_replace('-2064.', '-1000.', $s['hero_photo'])) . ' 750w, ' . asset($s['hero_photo']) . ' 1548w', true, 'In the room', $s['title']]))
        ->merge(collect(config('shiftreality.visitor_photos'))
            ->map(fn ($v) => [$v[0], asset($v[0]) . ' 800w, ' . asset(str_replace('-800.', '-1600.', $v[0])) . ' 1600w', false, $v[1], $v[2]]))
        ->merge(collect($shifts)->filter(fn ($s) => ! empty($s['photo']))
            ->map(fn ($s) => [$s['photo'], asset($s['photo']) . ' 800w, ' . asset(str_replace('-800.', '-1600.', $s['photo'])) . ' 1600w', false, 'In the room', $s['title']]))
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
     The visit band (partials/visit-band), mid-page, as a card: the picture
     (config band_bg) dimmed behind it, the rooms listed beside the headline,
     the ask and the pill in a bar along its foot. The final band matches.
     Plain facts only (no countdowns, no claims about other visitors).
     ══════════════════════════════════════════════════════════ --}}
@include('partials.visit-band', [
    'headingId' => 'inline-cta-heading',
    'eyebrow'   => 'The rooms',
    {{-- each phrase kept whole (no lone "like.") --}}
    'title'     => '<span class="nowrap">Book a time.</span> <span class="nowrap">Walk in.</span><br><span class="nowrap">Stay as long as you like.</span>',
    {{-- non-breaking hyphen keeps "Walk-ins" whole --}}
    'sub'       => 'Walk&#8209;ins are welcome when there\'s space. Weekends and public holidays are the busiest, so booking ahead holds your time.',
    'pill'      => 'Book My Spot',
    'bg'        => config('shiftreality.band_bg'),
    'ask'       => 'Ready to question everything?',
])

{{-- ══════════════════════════════════════════════════════════
     05 — SOCIAL PROOF
     Real proof: the Google score, one featured quote, the rest under it.
     No Book button: the visit band just above is the ask.
     ══════════════════════════════════════════════════════════ --}}
<section class="reviews-section section" id="reviews" aria-labelledby="reviews-heading">
    <div class="container">

        {{-- Eyebrow and heading left, the line right, as the other sections --}}
        <div class="reviews-section__intro">
            <div>
                <p class="reviews-section__eyebrow">Visitor reviews</p>
                <h2 class="reviews-section__title" id="reviews-heading" data-split>
                    Harare can't stop<br>talking about it.
                </h2>
            </div>
            <p class="reviews-section__tagline" data-scrub>
                Walk in sceptical, walk out arguing about what you saw.
                Here's what visitors said.
            </p>
        </div>

        @php
            $googleRating = config('shiftreality.google_rating');
            $reviewsUrl   = config('shiftreality.google_reviews_url');
            [$lead, $others] = [$testimonials[0] ?? null, array_slice($testimonials, 1)];
            // initials for each reviewer's mark: "Tatenda M." → "TM"
            $initials = fn ($name) => collect(preg_split('/\s+/', trim($name)))
                ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
        @endphp

        <div class="reviews">
            {{-- The score first: the proof the quotes stand on, and where to read more --}}
            <div class="reviews__score" data-scrub>
                <p class="reviews__score-value" aria-hidden="true">
                    <span data-count-to="{{ $googleRating }}">{{ number_format($googleRating, 1) }}</span><span class="reviews__score-max">/5</span>
                </p>
                @include('partials.stars', ['rating' => $googleRating, 'size' => 20])
                <p class="reviews__score-source">
                    @include('partials.google-g', ['size' => 18])
                    <span>Rated {{ number_format($googleRating, 1) }} out of 5 on Google</span>
                </p>
                @if ($reviewsUrl)
                    <a href="{{ $reviewsUrl }}" class="reviews__all" target="_blank" rel="noopener">
                        Read them all on Google
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
                    </a>
                @endif
            </div>

            {{-- Quotes: one featured, the rest under it; each appears exactly once.
                 Each: stars, the words, then who (their initials, name, how they came) --}}
            <div class="reviews__quotes">
                @if ($lead)
                    <figure class="quote quote--lead" data-scrub>
                        @include('partials.stars', ['rating' => $lead['rating'], 'size' => 16])
                        <span class="sr-only">Rated {{ $lead['rating'] }} out of 5.</span>
                        <blockquote class="quote__text">“{{ $lead['quote'] }}”</blockquote>
                        <figcaption class="quote__who">
                            <span class="quote__mark" aria-hidden="true">{{ $initials($lead['name']) }}</span>
                            <span class="quote__name">{{ $lead['name'] }}</span>
                            <span class="quote__tag">{{ $lead['detail'] }}</span>
                        </figcaption>
                    </figure>
                @endif

                @if ($others)
                    <div class="reviews__more">
                        @foreach ($others as $t)
                            <figure class="quote" data-scrub>
                                @include('partials.stars', ['rating' => $t['rating'], 'size' => 13])
                                <span class="sr-only">Rated {{ $t['rating'] }} out of 5.</span>
                                <blockquote class="quote__text">“{{ $t['quote'] }}”</blockquote>
                                <figcaption class="quote__who">
                                    <span class="quote__mark" aria-hidden="true">{{ $initials($t['name']) }}</span>
                                    <span class="quote__name">{{ $t['name'] }}</span>
                                    <span class="quote__tag">{{ $t['detail'] }}</span>
                                </figcaption>
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
     config (days open a week, the Google rating) — no invented history.
     ⚠ The photo is ours (visitors, from socials, AI-upscaled).
     ══════════════════════════════════════════════════════════ --}}
@php
    $aboutPhoto = config('shiftreality.about_photo');
    $aboutRating = config('shiftreality.google_rating');
    $aboutDaysOpen = collect(config('shiftreality.hours'))->filter()->count();
    $aboutMinAge = config('shiftreality.visit.min_age');
@endphp
<section class="about" id="about" aria-labelledby="about-heading">
    <div class="container about__grid">

        <div class="about__side">
            <p class="about__eyebrow">About Reality Shift</p>
            @if ($aboutPhoto)
                <figure class="about__photo" data-reveal-at="{{ $aboutPhoto[2] ?? '50 40' }}">
                    <img
                        src="{{ asset($aboutPhoto[0]) }}"
                        alt="{{ $aboutPhoto[1] }}"
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
                Reality Shift is Zimbabwe's first optical illusion experience: rooms built to make you
                question something you've taken for granted your entire life. No experience needed, no age too old.
                Bring your eyes, your people and a camera.
            </p>
            <a href="{{ route('experience') }}" class="pill pill--amber pill--lg about__pill">
                <span>Explore the rooms</span>
                <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>

            <dl class="about__stats">
                <div>
                    <dt>Days a week, open</dt>
                    <dd data-count-to="{{ $aboutDaysOpen }}">{{ $aboutDaysOpen }}</dd>
                </div>
                <div>
                    <dt>Google rating</dt>
                    <dd><span data-count-to="{{ number_format($aboutRating, 1) }}">{{ number_format($aboutRating, 1) }}</span><span class="about__unit">/5</span></dd>
                </div>
                <div>
                    <dt>Years and up, welcome</dt>
                    <dd><span data-count-to="{{ $aboutMinAge }}">{{ $aboutMinAge }}</span><span class="about__unit">+</span></dd>
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
        ':rooms'    => collect($shifts)->pluck('title')->implode(', '),
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
            <p class="pricing__sub">Book online to guarantee your slot, or walk in when there's space. Then take your time inside.</p>
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
                    <span>Book My Spot</span>
                    <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <p @class(['ticket__status', 'is-open' => $ticketStatus['open']])>
                    <span class="ticket__dot" aria-hidden="true"></span>
                    {{ $ticketStatus['label'] }}&nbsp;· {{ $ticketStatus['detail'] }}
                </p>
            </div>

            {{-- The tear: a dashed line between two notches --}}
            <span class="ticket__tear" aria-hidden="true"></span>

            {{-- What it buys, as the back of a real ticket: its printed terms,
                 a small label and the plain fact, row by row --}}
            <div class="ticket__body">
                <p class="ticket__label">Every ticket</p>
                <dl class="ticket__terms">
                    @foreach ($ticket['terms'] as [$term, $fact])
                        <div class="ticket__term">
                            <dt>{{ $term }}</dt>
                            <dd>{{ $fillPrice($fact) }}</dd>
                        </div>
                    @endforeach
                </dl>
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
                <p class="group-strip__line">Group rates and priority entry. Tell us your date and we'll handle the rest.</p>
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
     The last doubts, cleared right before the final ask. Centred: the
     eyebrow, heading and line, then the questions as hairline rows. The
     section holds still while you scroll, and the scroll walks down the
     questions: the one you're on opens, the last one closes (faq-pin.js).
     Tapping a question goes to it.
     Rendered all open, so without JS or motion every answer simply shows.
     (Scoped as .faq-home: /pricing keeps its own FAQ look.)
     ══════════════════════════════════════════════════════════ --}}
<section class="faq-home" id="faq" aria-labelledby="faq-heading">
    <div class="container">
      <div class="faq-home__pin" data-faq-pin>

        <div class="faq-home__intro">
            <p class="faq-home__eyebrow">FAQ</p>
            <h2 class="faq-home__title" id="faq-heading" data-split>Good to know.</h2>
            <p class="faq-home__sub">The questions people ask most before they book.</p>
        </div>

        <div class="faq faq-home__list" role="list" data-faq-scroll>
            @foreach ($faq as $i => $item)
                <div class="faq__item is-open" role="listitem">
                    <button class="faq__question" aria-expanded="true" aria-controls="faq-a-{{ $i }}" id="faq-q-{{ $i }}">
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

      </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     09 — FINAL CTA
     The visit band again (a card, the same photo): the closing words,
     the visit's facts listed on the right (not the rooms again), and the
     bar with the ask and the pill.
     Hands over to the (light) footer.
     ══════════════════════════════════════════════════════════ --}}
@php
    $finalDays = collect(config('shiftreality.hours'))->filter()->count();
    $finalMap  = 'https://www.google.com/maps/search/?api=1&query='
        . rawurlencode(config('shiftreality.location.address') . ', ' . config('shiftreality.location.city'));
@endphp
@include('partials.visit-band', [
    'id'        => 'visit-today',
    'headingId' => 'final-heading',
    'eyebrow'   => 'Your visit',
    {{-- each phrase kept whole --}}
    'title'     => '<span class="nowrap">Question everything.</span><br><span class="nowrap">Starting today.</span>',
    'sub'       => 'Zimbabwe\'s first optical illusion experience. Come with your people and a camera.',
    'list'      => [
        [config('shiftreality.location.address') . ', ' . config('shiftreality.location.city'), $finalMap],
        ['Entry from $' . $visit['price_from'], route('pricing')],
        ['Ages ' . $visit['min_age'] . ' and up', null],
        [$finalDays === 7 ? 'Open every day' : 'Open ' . $finalDays . ' days a week', null],
    ],
    'ask'       => 'Pick a day and time.',
    'pill'      => 'Book My Spot',
    'bg'        => config('shiftreality.band_bg'),
])

@endsection
