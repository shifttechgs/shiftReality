{{-- Top bar: one still line of real facts — live open status, where, the price —
     and the way to book. Built from config, so it never claims what isn't true. --}}
@php $bannerStatus = \App\Support\OpeningHours::fromConfig()->status(); @endphp
<div class="top-banner" id="top-banner" role="region" aria-label="Opening status">
    <p class="top-banner__line">
        <span @class(['top-banner__status', 'is-open' => $bannerStatus['open']])>{{ $bannerStatus['label'] }}<span class="top-banner__extra">&nbsp;· {{ $bannerStatus['detail'] }}</span></span>
        <span class="top-banner__extra">{{ config('shiftreality.location.address') }}, {{ config('shiftreality.location.city') }}</span>
        <span>From ${{ config('shiftreality.visit.price_from') }}</span>
        <a href="{{ route('book') }}" class="top-banner__link">Book your spot&nbsp;→</a>
    </p>

    <button class="top-banner__close" id="top-banner-close" aria-label="Dismiss">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
            <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
    </button>
</div>
