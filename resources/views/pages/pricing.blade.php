@extends('layouts.app')

@section('title', 'Pricing — ' . config('shiftreality.name') . ' · Tickets from $' . $visit['price_from'])
@section('description', 'One ticket, every room: $' . $visit['price_from'] . ' per person for all ' . count($shifts) . ' optical illusion rooms at ' . config('shiftreality.location.address') . ', ' . config('shiftreality.location.city') . '. Group rates on request.')

@php
    $status   = $openingHours->status();
    $faqItems = array_slice($faq, 0, 4);
    $whatsApp = 'https://wa.me/' . config('shiftreality.contact.whatsapp');
    $groupWhatsApp = $whatsApp . '?text=' . rawurlencode('Hi Reality Shift! I\'d like to enquire about a group booking.');
@endphp

@push('head')
<script type="application/ld+json">
@php echo json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'   => 'TouristAttraction',
            'name'    => config('shiftreality.name'),
            'url'     => config('shiftreality.url'),
            'address' => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => config('shiftreality.location.address'),
                'addressLocality' => config('shiftreality.location.city'),
                'addressCountry'  => config('shiftreality.location.country_code'),
            ],
            'openingHoursSpecification' => $openingHours->schema(),
            'offers' => [
                '@type'         => 'Offer',
                'name'          => $ticket['name'],
                'price'         => $visit['price_from'],
                'priceCurrency' => 'USD',
                'url'           => url(route('pricing')),
            ],
        ],
        [
            '@type'      => 'FAQPage',
            'mainEntity' => array_map(fn ($item) => [
                '@type'          => 'Question',
                'name'           => $item['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['answer']],
            ], $faqItems),
        ],
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); @endphp
</script>
@endpush

{{-- The banner is deep navy, so the nav rests in its light variant --}}
@section('body_class', 'has-hero-photo')

@section('content')

@include('partials.page-banner', [
    'crumb' => 'Pricing',
    'title' => "The only thing in here\nthat isn't an illusion.",
    'intro' => 'One ticket, all ' . count($shifts) . ' rooms. Here\'s exactly what it costs and what you get for it.',
])

{{-- ══════════════════════════════════════════════════════════
     01 — THE PRICE
     The figure on the left; what's included and the one action
     on the right.
     ══════════════════════════════════════════════════════════ --}}
<section class="pp-hero" aria-label="Ticket price">
    <div class="container pp-hero__grid">

        <div class="pp-hero__content">
            <p class="pp-hero__price">
                <span class="pp-hero__figure">{{ $ticket['price'] !== null ? '$' . $ticket['price'] : 'On request' }}</span>
                <span class="pp-hero__unit">{{ $ticket['unit'] }}<br>{{ strtolower($ticket['name']) }}</span>
            </p>
        </div>

        <div class="pp-hero__aside">
            <ul class="pp-hero__list" aria-label="Included with every ticket">
                @foreach ($ticket['includes'] as $line)
                    <li data-scrub>{{ $line }}</li>
                @endforeach
            </ul>

            <div class="pp-hero__actions" data-scrub>
                <a href="{{ route('book') }}" class="pill pill--amber pill--lg">
                    <span>Book Your Spot</span>
                    <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="#pp-groups" class="visit__more">Coming as a group? ↓</a>
            </div>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     02 — WHAT IT BUYS
     The rooms as a receipt with one total, then the sums — all
     worked out from config, never invented.
     ══════════════════════════════════════════════════════════ --}}
<section class="pp-value" aria-labelledby="pp-value-heading">
    <div class="container">

        <div class="pp-value__intro">
            <h2 class="pp-value__title" id="pp-value-heading" data-split>
                What ${{ $visit['price_from'] }}<br>actually buys.
            </h2>

            <p class="pp-value__sub" data-scrub>
                Every ticket covers all {{ count($shifts) }} rooms. Here's how the ${{ $visit['price_from'] }} breaks down.
            </p>
        </div>

        {{-- Each room: number, photo (stand-ins until real room photos), name, tagline --}}
        <ol class="pp-receipt">
            @foreach ($shifts as $shift)
                <li class="pp-receipt__row" data-scrub>
                    <span class="pp-receipt__no">{{ $shift['number'] }}</span>
                    <span class="pp-receipt__thumb">
                        <img src="{{ asset($shift['image']) }}" alt="" loading="lazy" decoding="async">
                    </span>
                    <span class="pp-receipt__name">{{ $shift['title'] }}</span>
                    <span class="pp-receipt__line">{{ $shift['tagline'] }}</span>
                </li>
            @endforeach
        </ol>
        <p class="pp-receipt__total" data-scrub>
            <span>{{ count($shifts) }} rooms, one ticket</span>
            <span class="pp-receipt__sum">${{ $visit['price_from'] }}</span>
        </p>

        {{-- The sums, as plain facts split by hairlines --}}
        <dl class="pp-sums">
            @if ($perRoom)
                <div class="pp-sum" data-scrub>
                    <dt>Per room</dt>
                    <dd>${{ rtrim(rtrim(number_format($perRoom, 2), '0'), '.') }}</dd>
                </div>
            @endif
            @if ($perMinute)
                <div class="pp-sum" data-scrub>
                    <dt>Per minute</dt>
                    <dd>{{ $perMinute[0] }}–{{ $perMinute[1] }}¢</dd>
                </div>
            @endif
            <div class="pp-sum" data-scrub>
                <dt>Time inside</dt>
                <dd>{{ $visit['duration'] }}</dd>
            </div>
        </dl>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     03 — GROUPS
     Photo on one side (stand-in), the group pitch on the other.
     ══════════════════════════════════════════════════════════ --}}
<section class="pp-groups" id="pp-groups" aria-labelledby="pp-groups-heading">
    <div class="container pp-groups__grid">

        <figure class="pp-photo pp-groups__media" data-reveal>
            <img src="{{ asset($groups['image']) }}" alt="" class="pp-photo__img" loading="lazy" decoding="async">
        </figure>

        <div class="pp-groups__content">
            <h2 class="pp-groups__title" id="pp-groups-heading" data-split>Bringing a group?</h2>
            <p class="pp-groups__sub" data-scrub>
                Groups get their own rates and priority entry. Tell us your date and
                how many are coming, and we'll come back with a price.
            </p>

            <p class="pp-groups__price" data-scrub>
                <span class="pp-groups__figure">On request</span>
                <span class="pp-groups__unit">group rates</span>
            </p>

            <ul class="groups__occasions pp-groups__occasions">
                @foreach ($groups['occasions'] as $occasion)
                    <li class="groups__occasion" data-scrub>
                        <h3 class="groups__occasion-title">{{ $occasion['title'] }}</h3>
                        <p class="groups__occasion-line">{{ $occasion['line'] }}</p>
                    </li>
                @endforeach
            </ul>

            <div class="pp-groups__actions" data-scrub>
                <a href="{{ route('groups') }}" class="pill pill--amber pill--lg">
                    <span>Enquire for Groups</span>
                    <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ $groupWhatsApp }}" class="visit__more" target="_blank" rel="noopener">Or ask on WhatsApp →</a>
            </div>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     04 — BOOK AHEAD OR WALK IN
     Deep navy band: the booking rule beside the live hours.
     ══════════════════════════════════════════════════════════ --}}
<section class="pp-when" aria-labelledby="pp-when-heading">
    <div class="container pp-when__grid">

        <div class="pp-when__copy">
            <h2 class="pp-when__title" id="pp-when-heading" data-split>Book ahead,<br>or chance it.</h2>
            <p class="pp-when__sub" data-scrub>
                Same price either way. Booking online guarantees your slot; walk&#8209;ins
                are welcome when there's space — weekends and public holidays are the busiest.
            </p>
            <a href="{{ route('book') }}" class="pill pill--amber pill--lg" data-scrub>
                <span>Guarantee Your Slot</span>
                <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="pp-when__hours" data-scrub>
            <p @class(['pp-when__status', 'is-open' => $status['open']])>
                <span class="pp-when__dot" aria-hidden="true"></span>
                {{ $status['label'] }}&nbsp;· {{ $status['detail'] }}
            </p>
            <dl class="pp-hours">
                @foreach ($openingHours->rows() as $row)
                    <div @class(['pp-hours__row', 'is-today' => $row['today'], 'is-closed' => $row['closed']])>
                        <dt>
                            {{ $row['days'] }}
                            @if ($row['today'])<span class="pp-hours__today">Today</span>@endif
                        </dt>
                        <dd>{{ $row['time'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     05 — FAQ
     ══════════════════════════════════════════════════════════ --}}
<section class="faq-section" id="faq" aria-labelledby="faq-heading">
    <div class="container">

        <div class="visit-faq">
            <div class="visit-faq__aside" data-scrub>
                <h2 class="visit-faq__title" id="faq-heading" data-split>Before you pay.</h2>
                <p class="visit-faq__sub">The questions people ask most about tickets and booking.</p>
                <span class="visit-faq__links">
                    <a href="{{ route('faq') }}" class="visit__more">See all questions →</a>
                    <a href="{{ $whatsApp }}" class="visit__more" target="_blank" rel="noopener">Ask us on WhatsApp →</a>
                </span>
            </div>

            <div class="faq" role="list">
                @foreach ($faqItems as $i => $item)
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
     06 — CLOSE
     ══════════════════════════════════════════════════════════ --}}
<section class="pp-close" aria-labelledby="pp-close-heading">
    <div class="container pp-close__inner">
        <h2 class="pp-close__title" id="pp-close-heading" data-split>
            See you <em>inside.</em>
        </h2>
        <div class="pp-close__actions" data-scrub>
            <a href="{{ route('book') }}" class="pill pill--amber pill--xl">
                <span>Book Now — ${{ $visit['price_from'] }}</span>
                <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <a href="{{ route('home') }}#rooms" class="visit__more">See the rooms first →</a>
        </div>
    </div>
</section>

@endsection
