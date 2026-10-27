{{-- Top bar, after Artora's: one shade darker than the page. Left: whether
     we're open right now (live). Right: email and address. Built from config,
     so it never claims what isn't true. Hidden on phones, as on Artora;
     scroll.js tucks it away once you scroll and brings it back at the top. --}}
@php
    $bannerStatus  = \App\Support\OpeningHours::fromConfig()->status();
    $bannerContact = config('shiftreality.contact');
    $bannerPlace   = config('shiftreality.location');
    $bannerMaps    = 'https://www.google.com/maps/dir/?api=1&destination='
        . $bannerPlace['coordinates']['lat'] . ',' . $bannerPlace['coordinates']['lng'];
@endphp
<div class="top-banner" id="top-banner" role="region" aria-label="Opening hours and contact">
    <div class="top-banner__inner">
        <p @class(['top-banner__item', 'top-banner__status', 'is-open' => $bannerStatus['open']])>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2.5 2.5M5 3 2 6M19 3l3 3"/></svg>
            {{ $bannerStatus['label'] }}&nbsp;· {{ $bannerStatus['detail'] }}
        </p>
        <div class="top-banner__right">
            <a href="mailto:{{ $bannerContact['email'] }}" class="top-banner__item top-banner__link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                {{ $bannerContact['email'] }}
            </a>
            <a href="{{ $bannerMaps }}" class="top-banner__item top-banner__link" target="_blank" rel="noopener">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                {{ $bannerPlace['address'] }}, {{ $bannerPlace['city'] }}
            </a>
        </div>
    </div>
</div>
