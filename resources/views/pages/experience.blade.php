@extends('layouts.app')

@section('title', 'The Rooms · ' . config('shiftreality.name') . ' · Optical illusions in Harare')
@section('description', 'Inside Reality Shift: the Upside Down Room, Stranger in the Mirror, the Infinity Room, the Beuchet Chair and more, at ' . $location['address'] . ', ' . $location['city'] . '. Every room on one ticket from $' . $visit['price_from'] . '.')

{{-- The banner is deep navy, so the nav rests in its light variant --}}
@section('body_class', 'has-hero-photo')

@php
    $arrow = '<svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
    $twin  = fn ($src) => str_replace('-800.', '-1600.', $src);
@endphp

@section('content')

@include('partials.page-banner', [
    'crumb' => 'Exhibits',
    'title' => "Rooms that\nlie to your eyes.",
    'intro' => 'Every room is built to fool you, and built for the camera. Here\'s what\'s waiting inside.',
])

{{-- ══════════════════════════════════════════════════════════
     01 — THE ROOMS, a scroll story (js/animations/exhibits.js)
     Desktop: the photo frame stays pinned on the right while each
     room's words pass on the left; the next photo wipes up over the
     last as its words reach the middle. Names under the frame jump
     to a room. Phones: one swipe row, a card per room.
     Without JS (or with reduced motion) the photos simply swap.
     ══════════════════════════════════════════════════════════ --}}
<section class="ex-rooms" data-ex-rooms aria-label="The rooms">
    <div class="container ex-rooms__grid">

        <div class="ex-rooms__stage" aria-hidden="true">
            <div class="ex-rooms__frame">
                @foreach ($shifts as $i => $room)
                    <img src="{{ asset($room['photo']) }}"
                         srcset="{{ asset($room['photo']) }} 800w, {{ asset($twin($room['photo'])) }} 1600w"
                         sizes="45vw" alt="" @class(['ex-rooms__img', 'is-shown' => $i === 0])
                         data-ex-img="{{ $i }}" style="z-index: {{ $i + 1 }}"
                         @if ($i > 0) loading="lazy" @endif decoding="async">
                @endforeach
                <p class="ex-rooms__count"><span data-ex-count>1</span> / {{ count($shifts) }}</p>
            </div>
            <ol class="ex-rooms__tabs">
                @foreach ($shifts as $i => $room)
                    <li><button type="button" @class(['ex-rooms__tab', 'is-active' => $i === 0]) data-ex-tab="{{ $i }}" tabindex="-1">{{ $room['title'] }}</button></li>
                @endforeach
            </ol>
        </div>

        <div class="ex-rooms__list">
            @foreach ($shifts as $i => $room)
                <article class="ex-room" id="{{ $room['slug'] }}" data-ex-room="{{ $i }}" aria-labelledby="ex-room-{{ $room['slug'] }}">
                    {{-- the card's own photo, shown on phones (the pinned frame shows it on desktop) --}}
                    <figure class="ex-room__media">
                        <img src="{{ asset($room['photo']) }}"
                             srcset="{{ asset($room['photo']) }} 800w, {{ asset($twin($room['photo'])) }} 1600w"
                             sizes="(max-width: 900px) 85vw, 1px"
                             alt="{{ $room['photo_alt'] ?? $room['title'] }}"
                             @if ($i > 0) loading="lazy" @endif decoding="async">
                    </figure>

                    <div class="ex-room__body">
                        <p class="pp-label">In the room</p>
                        <h2 class="ex-room__title" id="ex-room-{{ $room['slug'] }}">{{ $room['title'] }}</h2>
                        <p class="ex-room__line">{{ $room['tagline'] }}</p>
                        <p class="ex-room__text">{{ $room['description'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>

        <p class="ex-rooms__hint" aria-hidden="true">Swipe for the next room →</p>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     02 — ALSO INSIDE
     The other spots, from our visitors' own photos.
     ══════════════════════════════════════════════════════════ --}}
@if ($extras)
<section class="ex-more" aria-labelledby="ex-more-heading">
    <div class="container">
        <div class="pp-value__intro">
            <div>
                <p class="pp-label">Also inside</p>
                <h2 class="pp-value__title" id="ex-more-heading" data-split>And between<br>the rooms.</h2>
            </div>
            <p class="pp-value__sub" data-scrub>
                Mirrors, tilted floors and a table full of you, in our visitors' own photos. Drag or swipe along.
            </p>
        </div>

        <ul class="ex-more__grid" data-drag-row>
            @foreach ($extras as [$src, $meta, $name])
                <li class="ex-more__item">
                    <span class="ex-more__photo">
                        <img src="{{ asset($src) }}" srcset="{{ asset($src) }} 800w, {{ asset($twin($src)) }} 1600w"
                             sizes="(max-width: 640px) 75vw, 26vw"
                             alt="Visitors in the {{ $name }} at {{ config('shiftreality.name') }}" loading="lazy" decoding="async">
                    </span>
                    <span class="ex-more__name">{{ $name }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════
     03 — HOW IT WORKS (deep navy)
     The visit's plain facts, as the ticket prints them.
     ══════════════════════════════════════════════════════════ --}}
<section class="pp-when" aria-labelledby="ex-how-heading">
    <div class="container pp-when__grid">
        <div class="pp-when__copy">
            <p class="pp-label pp-label--dark">How it works</p>
            <h2 class="pp-when__title" id="ex-how-heading" data-split>Go at your<br>own pace.</h2>
            <p class="pp-when__sub" data-scrub>
                One ticket opens every room. Wander, go back, take the photo again.
                Nobody hurries you along.
            </p>
            <a href="{{ route('book') }}" class="pill pill--amber pill--lg" data-scrub><span>Book My Spot</span>{!! $arrow !!}</a>
        </div>

        <dl class="pp-hours ex-facts" data-scrub>
            <div class="pp-hours__row"><dt>Time inside</dt><dd>{{ $visit['duration'] }}</dd></div>
            <div class="pp-hours__row"><dt>Entry</dt><dd>${{ $visit['price_from'] }} per person</dd></div>
            <div class="pp-hours__row"><dt>Ages</dt><dd>{{ $visit['min_age'] }} and up</dd></div>
            <div class="pp-hours__row"><dt>Photos</dt><dd>Welcome in every room</dd></div>
        </dl>
    </div>
</section>

@include('partials.inner-close')

@endsection
