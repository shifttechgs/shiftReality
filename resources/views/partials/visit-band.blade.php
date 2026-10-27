{{-- Photo-collage band, after Artora's "Visit Today": solid deep navy, the
     eyebrow, the line, an optional sub-line, one white pill and the live
     status centred, with eight photos scattered round the edges — still.
     Our kaleidoscope photo first (upside down), then the stand-ins; `offset`
     rotates the set so two bands on one page don't repeat the same collage.
     Params: id, headingId, eyebrow, title (HTML), sub (HTML, optional),
     pill, offset (int, optional). Needs $shifts and $openingHours.
     (Not "shift": includes inherit the page's variables, and the rooms loop
     leaves a $shift array behind.) --}}
@php
    $bandStatus = $openingHours->status();
    $bandPhotos = collect($shifts)->filter(fn ($s) => ! empty($s['hero_photo']))
        ->map(fn ($s) => [str_replace('-2064.', '-1000.', $s['hero_photo']), true])
        ->merge(collect($shifts)->pluck('photo')->merge(config('shiftreality.hero_extras'))->filter()->map(fn ($p) => [$p, false]))
        ->take(8)->values();
    $bandShift  = (int) ($offset ?? 0) % max(1, $bandPhotos->count());
    $bandPhotos = $bandPhotos->slice($bandShift)->merge($bandPhotos->take($bandShift))->values();
@endphp
<section class="visit-cta" @isset($id) id="{{ $id }}" @endisset aria-labelledby="{{ $headingId }}">

    <div class="visit-cta__photos" aria-hidden="true">
        @foreach ($bandPhotos as $i => [$src, $flip])
            <div @class(['visit-cta__photo', 'visit-cta__photo--' . ($i + 1), 'visit-cta__photo--flip' => $flip])>
                <img src="{{ asset($src) }}" alt="" loading="lazy" decoding="async">
            </div>
        @endforeach
    </div>

    <div class="visit-cta__inner">
        <p class="visit-cta__eyebrow">{{ $eyebrow }}</p>
        <h2 class="visit-cta__title" id="{{ $headingId }}" data-split>{!! $title !!}</h2>
        @if (! empty($sub))
            <p class="visit-cta__sub">{!! $sub !!}</p>
        @endif
        <a href="{{ route('book') }}" class="pill pill--amber pill--lg visit-cta__pill">
            <span>{{ $pill }}</span>
            <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <p @class(['visit-cta__note', 'is-open' => $bandStatus['open']])>
            <span class="visit-cta__dot" aria-hidden="true"></span>
            {{-- &nbsp; keeps each "·" on the line it follows --}}
            {{ $bandStatus['label'] }}&nbsp;· {{ $bandStatus['detail'] }}&nbsp;· {{ config('shiftreality.location.address') }}, {{ config('shiftreality.location.city') }}
        </p>
    </div>

</section>
