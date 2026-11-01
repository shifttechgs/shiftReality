@extends('layouts.app')

@section('title', 'Plan Your Visit · ' . config('shiftreality.name') . ' · ' . $location['address'] . ', ' . $location['city'])
@section('description', 'How to find Reality Shift at ' . $location['address'] . ', ' . $location['city'] . ': opening hours, how long a visit takes, ticket prices and what to bring.')

{{-- The banner is deep navy, so the nav rests in its light variant --}}
@section('body_class', 'has-hero-photo')

@php
    $status   = $openingHours->status();
    $arrow    = '<svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
    $place    = $location['address'] . ', ' . $location['city'];
    $mapLink  = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($place);
    $mapEmbed = 'https://www.google.com/maps?q=' . rawurlencode($place . ', ' . $location['country']) . '&output=embed';
    $tel      = preg_replace('/\D/', '', $contact['phone']);
    $phone    = preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '$1 $2 $3', $tel);
    $openDays = collect(config('shiftreality.hours'))->filter()->count();

    // Before you come: plain advice, only from what the FAQ and config already say
    $tips = [
        ['Book ahead', 'Weekends and public holidays are the busiest. Send a booking request and we\'ll call to hold your time. Walk-ins are welcome when there\'s space.'],
        ['Give it time', 'There\'s no time limit inside. Most visits take ' . $visit['duration'] . ', so don\'t squeeze it in before something else.'],
        ['Wear comfortable shoes', 'Some rooms play with the floor as much as the walls.'],
        ['Charge your phone', 'Photos are welcome in every room, and you\'ll take more than you think.'],
        ['Bring the kids', 'Reality Shift is for ages ' . $visit['min_age'] . ' and up. The grown-ups get fooled just as badly.'],
    ];
@endphp

@section('content')

@include('partials.page-banner', [
    'crumb' => 'Plan Your Visit',
    'title' => "Everything to know\nbefore you come.",
    'intro' => 'Where we are, when we\'re open and what to bring. Questions we haven\'t answered? Call or WhatsApp us.',
])

{{-- ══════════════════════════════════════════════════════════
     01 — FIND US
     The address and how to reach us beside the map (it loads when
     it comes into view).
     ══════════════════════════════════════════════════════════ --}}
<section class="vp-find" aria-labelledby="vp-find-heading">
    <div class="container vp-find__grid">
        <div class="vp-find__copy">
            <p class="pp-label">Find us</p>
            <h2 class="pp-value__title" id="vp-find-heading" data-split>{{ $location['address'] }},<br>{{ $location['city'] }}.</h2>

            <dl class="ticket__terms vp-find__terms">
                <div class="ticket__term" data-scrub>
                    <dt>Address</dt>
                    <dd>{{ $place }}<br><a href="{{ $mapLink }}" class="visit__more" target="_blank" rel="noopener">Get directions →</a></dd>
                </div>
                <div class="ticket__term" data-scrub>
                    <dt>Call</dt>
                    <dd><a href="tel:{{ $tel }}" class="visit__more">{{ $phone }}</a></dd>
                </div>
                <div class="ticket__term" data-scrub>
                    <dt>WhatsApp</dt>
                    <dd><a href="https://wa.me/{{ $contact['whatsapp'] }}" class="visit__more" target="_blank" rel="noopener">Message us →</a></dd>
                </div>
                <div class="ticket__term" data-scrub>
                    <dt>Email</dt>
                    <dd><a href="mailto:{{ $contact['email'] }}" class="visit__more">{{ $contact['email'] }}</a></dd>
                </div>
            </dl>
        </div>

        <div class="vp-map" data-reveal>
            <iframe data-lazy-src="{{ $mapEmbed }}" title="Map: {{ config('shiftreality.name') }} at {{ $place }}"
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     02 — OPENING HOURS (deep navy), live
     ══════════════════════════════════════════════════════════ --}}
<section class="pp-when" id="hours" aria-labelledby="vp-hours-heading">
    <div class="container pp-when__grid">
        <div class="pp-when__copy">
            <p class="pp-label pp-label--dark">Opening hours</p>
            <h2 class="pp-when__title" id="vp-hours-heading" data-split>{{ $openDays === 7 ? 'Open every day.' : 'Open ' . $openDays . ' days a week.' }}</h2>
            <p class="pp-when__sub" data-scrub>
                Most visits take {{ $visit['duration'] }}, so arrive in good time before closing.
                Weekends and public holidays are the busiest; book ahead if you can.
            </p>
            <a href="{{ route('book') }}" class="pill pill--amber pill--lg" data-scrub><span>Book My Spot</span>{!! $arrow !!}</a>
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
     03 — BEFORE YOU COME
     Numbered hairline rows, the FAQ's pattern.
     ══════════════════════════════════════════════════════════ --}}
<section class="vp-tips" aria-labelledby="vp-tips-heading">
    <div class="container visit-faq">
        <div class="visit-faq__aside" data-scrub>
            <p class="pp-label">Before you come</p>
            <h2 class="visit-faq__title" id="vp-tips-heading" data-split>A few<br>good tips.</h2>
            <p class="visit-faq__sub">From the questions people ask us most.</p>
            <span class="visit-faq__links">
                <a href="{{ route('faq') }}" class="visit__more">See all questions →</a>
            </span>
        </div>

        <ol class="vp-tips__list">
            @foreach ($tips as $i => [$tip, $line])
                <li class="vp-tip" data-scrub>
                    <span class="vp-tip__num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="vp-tip__title">{{ $tip }}</h3>
                    <p class="vp-tip__line">{{ $line }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     04 — TICKETS
     The price on a tint panel, with the two ways in.
     ══════════════════════════════════════════════════════════ --}}
<section class="vp-ticket" aria-labelledby="vp-ticket-heading">
    <div class="container">
        <div class="vp-ticket__panel" data-scrub>
            <div>
                <p class="pp-label" id="vp-ticket-heading">Tickets</p>
                <p class="vp-ticket__price"><span>${{ $visit['price_from'] }}</span> per person, every room</p>
            </div>
            <div class="vp-ticket__actions">
                <a href="{{ route('book') }}" class="pill pill--amber pill--lg"><span>Book My Spot</span>{!! $arrow !!}</a>
                <a href="{{ route('pricing') }}" class="visit__more">Pricing and groups →</a>
            </div>
        </div>
    </div>
</section>

@include('partials.inner-close')

@endsection
