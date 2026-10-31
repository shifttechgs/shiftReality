{{-- Photo-collage band, after Artora's "Visit Today": solid deep navy, the
     eyebrow, the line, an optional sub-line, one white pill and the live
     status centred, with eight photos scattered round the edges — still.
     With `bg` (a -960 path with a -1536 twin): a card instead, the picture
     dimmed inside it, the rooms listed beside the headline (`eyebrow` heads
     the list) and a bar at its foot with `ask`, the status and the pill. Without it, the collage:
     our kaleidoscope photo (upside down) and the rooms' and visitors'
     photos, which `offset` rotates so two bands don't match.
     Params: id, headingId, eyebrow, title (HTML), sub (HTML, optional),
     pill, bg (optional), ask (optional), list (optional), bleed (optional), offset (int, optional). Needs $shifts and $openingHours.
     (Not "shift": includes inherit the page's variables, and the rooms loop
     leaves a $shift array behind.) --}}
@php
    $bandStatus = $openingHours->status();
    $bandPhotos = collect($shifts)->filter(fn ($s) => ! empty($s['hero_photo']))
        ->map(fn ($s) => [str_replace('-2064.', '-1000.', $s['hero_photo']), true])
        ->merge(collect($shifts)->pluck('photo')->merge(config('shiftreality.hero_extras'))
            ->merge(collect(config('shiftreality.visitor_photos'))->pluck(0))->filter()->map(fn ($p) => [$p, false]))
        ->take(8)->values();
    $bandShift  = (int) ($offset ?? 0) % max(1, $bandPhotos->count());
    $bandPhotos = $bandPhotos->slice($bandShift)->merge($bandPhotos->take($bandShift))->values();
@endphp
<section @class(['visit-cta', 'visit-cta--bleed' => ! empty($bg)]) @isset($id) id="{{ $id }}" @endisset aria-labelledby="{{ $headingId }}">

    @if (empty($bg))
        <div class="visit-cta__photos" aria-hidden="true">
            @foreach ($bandPhotos as $i => [$src, $flip])
                <div @class(['visit-cta__photo', 'visit-cta__photo--' . ($i + 1), 'visit-cta__photo--flip' => $flip])>
                    <img src="{{ asset($src) }}" alt="" loading="lazy" decoding="async">
                </div>
            @endforeach
        </div>
    @endif

    @if (! empty($bg))
    {{-- A card on the page's edges (or, with `bleed`, the full width), the
         picture dimmed inside it: the headline and its line on the left, a
         list on the right (`list`: [label, href] rows, the rooms by default,
         headed by `eyebrow`), and along its foot a bar with the ask, the live
         status and the pill --}}
    @php
        $bandList = $list ?? collect($shifts)->map(fn ($r) => [$r['title'], route('experience')])->all();
    @endphp
    <div @class(['container' => empty($bleed)])>
        <div @class(['visit-cta__card', 'visit-cta__card--bleed' => ! empty($bleed)])>
            @if (! empty($bleed))<div class="container visit-cta__in">@endif
            <div class="visit-cta__bg" aria-hidden="true" data-focus-pull>
                <img src="{{ asset($bg) }}" srcset="{{ asset($bg) }} 960w, {{ asset(str_replace('-960.', '-1536.', $bg)) }} 1536w"
                     sizes="100vw" alt="" loading="lazy" decoding="async">
            </div>

            <div class="visit-cta__split">
                <div class="visit-cta__lead">
                    <h2 class="visit-cta__title" id="{{ $headingId }}" data-split>{!! $title !!}</h2>
                    @if (! empty($sub))
                        <p class="visit-cta__sub">{!! $sub !!}</p>
                    @endif
                </div>
                <nav class="visit-cta__list" aria-label="{{ $eyebrow }}">
                    <p class="visit-cta__list-head">{{ $eyebrow }}</p>
                    <ul>
                        @foreach ($bandList as [$label, $href])
                            <li>@if ($href)<a href="{{ $href }}">{{ $label }}</a>@else<span>{{ $label }}</span>@endif</li>
                        @endforeach
                    </ul>
                </nav>
            </div>

            <div class="visit-cta__bar">
                <p class="visit-cta__ask">{{ $ask ?? 'Ready to question everything?' }}</p>
                <div class="visit-cta__row">
                    {{-- the live status as the counter's sign: open or not, then when --}}
                    <p @class(['visit-cta__sign', 'is-open' => $bandStatus['open']])>
                        <span class="visit-cta__sign-now"><span class="visit-cta__dot" aria-hidden="true"></span>{{ $bandStatus['label'] }}</span>
                        <span class="visit-cta__sign-when">{{ $bandStatus['detail'] }}</span>
                    </p>
                    <a href="{{ route('book') }}" class="pill pill--amber pill--lg visit-cta__pill">
                        <span>{{ $pill }}</span>
                        <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            @if (! empty($bleed))</div>@endif
        </div>
    </div>
    @else
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
    @endif

</section>
