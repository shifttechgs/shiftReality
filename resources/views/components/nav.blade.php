{{-- Navigation, after Artora's header
     Desktop at rest: [logo] ……… [links] [Book pill], on the page itself, no bar.
     Scrolled: the same row pinned as a slim frosted pill bar.
     "More" opens a full-width deep-navy panel (nav-more.js).
     Mobile:  [logo] [hamburger]
--}}
@php
    $navContact = config('shiftreality.contact');
    $navPlace   = config('shiftreality.location');
    $navMaps    = 'https://www.google.com/maps/dir/?api=1&destination='
        . $navPlace['coordinates']['lat'] . ',' . $navPlace['coordinates']['lng'];
    $navPhone   = preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '$1 $2 $3', $navContact['phone']);
    $navTel     = '+263' . ltrim($navContact['phone'], '0');
@endphp
<nav class="nav" role="navigation" aria-label="Main navigation" id="site-nav">

    {{-- LEFT: logo --}}
    <a href="{{ route('home') }}" class="nav__logo" aria-label="{{ config('shiftreality.name') }}">
        <img src="{{ asset('images/logo-dark.png') }}" alt="{{ config('shiftreality.name') }}" class="nav__logo-img" width="88" height="88">
        <img src="{{ asset('images/logo-light.png') }}" alt="" class="nav__logo-img nav__logo-img--light" width="88" height="88" aria-hidden="true">
    </a>

    {{-- RIGHT: links, then the Book pill --}}
    <ul class="nav__menu" role="list">
        <li><a href="{{ route('experience') }}" class="nav__link">Exhibits</a></li>
        <li><a href="{{ route('pricing') }}"   class="nav__link">Pricing</a></li>
        <li><a href="{{ route('groups') }}"     class="nav__link">Group Visits</a></li>
        <li><a href="{{ route('visit') }}"      class="nav__link">Current Events</a></li>
        <li><a href="{{ route('book') }}"       class="nav__link">Membership</a></li>
        <li>
            <button type="button" class="nav__link nav__more" aria-expanded="false" aria-controls="nav-more-panel" data-nav-more>
                More
                <svg class="nav__more-chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
            </button>
        </li>
    </ul>

    <ul class="nav__actions" role="list">
        <li>
            <a href="{{ route('book') }}" class="pill pill--amber pill--lg nav__cta">
                <span>Book Your Spot</span>
                <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </li>
    </ul>

    {{-- "More": a full-width deep-navy panel under the header (Artora's "Pages") --}}
    <div class="nav-more" id="nav-more-panel" hidden data-nav-more-panel>
        <div class="nav-more__inner">
            <div class="nav-more__col">
                <p class="nav-more__title">More pages</p>
                <ul role="list">
                    <li><a href="{{ route('visit') }}">Plan your visit</a></li>
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                    <li><a href="{{ route('book') }}">Book tickets</a></li>
                    <li><a href="{{ route('groups') }}">Groups &amp; events</a></li>
                </ul>
            </div>
            <div class="nav-more__col">
                <p class="nav-more__title">Get in touch</p>
                <ul role="list">
                    <li><a href="https://wa.me/{{ $navContact['whatsapp'] }}" target="_blank" rel="noopener">WhatsApp us</a></li>
                    <li><a href="tel:{{ $navTel }}">{{ $navPhone }}</a></li>
                    <li><a href="mailto:{{ $navContact['email'] }}">{{ $navContact['email'] }}</a></li>
                    <li><a href="{{ $navMaps }}" target="_blank" rel="noopener">Get directions ↗</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Mobile: links + CTA collapse into the overlay --}}
    <button
        class="nav__toggle"
        aria-expanded="false"
        aria-controls="nav-overlay"
        aria-label="Open menu"
    >
        <span class="nav__toggle-bars" aria-hidden="true">
            <span class="nav__toggle-bar"></span>
            <span class="nav__toggle-bar"></span>
        </span>
    </button>

</nav>

{{-- Full-screen overlay --}}
<div class="nav-overlay" id="nav-overlay" aria-hidden="true" role="dialog" aria-label="Navigation menu">

    <div class="nav-overlay__header">
        <a href="{{ route('home') }}" class="nav-overlay__logo">
            <img src="{{ asset('images/logo-dark.png') }}" alt="{{ config('shiftreality.name') }}" class="nav__logo-img">
        </a>
        <button class="nav-overlay__close" aria-label="Close menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <ul class="nav-overlay__list" role="list">
        <li class="nav-overlay__item"><a href="{{ route('experience') }}" class="nav-overlay__link">Exhibits</a></li>
        <li class="nav-overlay__item"><a href="{{ route('pricing') }}"   class="nav-overlay__link">Pricing</a></li>
        <li class="nav-overlay__item"><a href="{{ route('groups') }}"     class="nav-overlay__link">Group Visits</a></li>
        <li class="nav-overlay__item"><a href="{{ route('visit') }}"      class="nav-overlay__link">Current Events</a></li>
        <li class="nav-overlay__item"><a href="{{ route('book') }}"       class="nav-overlay__link">Membership</a></li>
        <li class="nav-overlay__item"><a href="{{ route('faq') }}"        class="nav-overlay__link">FAQ</a></li>
    </ul>

    <div class="nav-overlay__foot">
        <a href="{{ route('book') }}" class="pill pill--amber pill--lg">Book Your Spot</a>
        <a href="https://wa.me/{{ config('shiftreality.contact.whatsapp') }}" class="nav-overlay__wa" target="_blank" rel="noopener">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
            WhatsApp us
        </a>
    </div>

</div>
