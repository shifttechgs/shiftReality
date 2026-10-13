{{-- Navigation — centred logo, split links, frosted glass
     Desktop: [left links] [logo] [right links + CTA]
     Mobile:  [logo] [hamburger]
--}}

<nav class="nav" role="navigation" aria-label="Main navigation" id="site-nav">

    {{-- LEFT: primary nav --}}
    <ul class="nav__left" role="list">
        <li><a href="{{ route('experience') }}" class="nav__link">Exhibits</a></li>
        <li><a href="{{ route('groups') }}"     class="nav__link">Group Visits</a></li>
        <li><a href="{{ route('visit') }}"      class="nav__link">Current Events</a></li>
    </ul>

    {{-- CENTRE: logo --}}
    <a href="{{ route('home') }}" class="nav__logo" aria-label="{{ config('shiftreality.name') }}">
        <img src="{{ asset('images/logo.jpg') }}" alt="{{ config('shiftreality.name') }}" class="nav__logo-img" width="88" height="88">
    </a>

    {{-- RIGHT: secondary nav + CTA --}}
    <ul class="nav__right" role="list">
        <li>
            <a href="{{ route('book') }}" class="nav__link">Membership</a>
        </li>
        <li>
            <a href="{{ route('faq') }}" class="nav__link">More</a>
        </li>
        <li>
            <a href="{{ route('book') }}" class="nav__cta" data-magnetic>
                <svg class="pill__ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2z"/><path d="M13 5v2M13 17v2M13 11v2"/></svg>
                <span>Book Tickets</span>
                <svg class="pill__arr" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </li>
    </ul>

    {{-- Mobile: logo + toggle --}}
    <a href="{{ route('home') }}" class="nav__logo-mobile" aria-label="{{ config('shiftreality.name') }}">
        <img src="{{ asset('images/logo.jpg') }}" alt="{{ config('shiftreality.name') }}" class="nav__logo-img" width="88" height="88">
    </a>

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
            <img src="{{ asset('images/logo.jpg') }}" alt="{{ config('shiftreality.name') }}" class="nav__logo-img">
        </a>
        <button class="nav-overlay__close" aria-label="Close menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <ul class="nav-overlay__list" role="list">
        <li class="nav-overlay__item"><a href="{{ route('experience') }}" class="nav-overlay__link">Exhibits</a></li>
        <li class="nav-overlay__item"><a href="{{ route('groups') }}"     class="nav-overlay__link">Group Visits</a></li>
        <li class="nav-overlay__item"><a href="{{ route('visit') }}"      class="nav-overlay__link">Current Events</a></li>
        <li class="nav-overlay__item"><a href="{{ route('book') }}"       class="nav-overlay__link">Membership</a></li>
        <li class="nav-overlay__item"><a href="{{ route('faq') }}"        class="nav-overlay__link">FAQ</a></li>
    </ul>

    <div class="nav-overlay__foot">
        <a href="{{ route('book') }}" class="pill pill--amber pill--lg">Book Tickets</a>
        <a href="https://wa.me/{{ config('shiftreality.contact.whatsapp') }}" class="nav-overlay__wa" target="_blank" rel="noopener">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
            WhatsApp us
        </a>
    </div>

</div>
