{{-- Navigation component
     Desktop: fixed header with links + BOOK NOW
     Mobile:  logo + hamburger → full-screen overlay
--}}

<nav class="nav" role="navigation" aria-label="Main navigation" id="site-nav">
    <a href="{{ route('home') }}" class="nav__logo" aria-label="{{ config('shiftreality.name') }}">
        {{ config('shiftreality.name') }}
    </a>

    {{-- Desktop links --}}
    <ul class="nav__links" role="list">
        <li><a href="{{ route('experience') }}" class="nav__link">Experience</a></li>
        <li><a href="{{ route('visit') }}"      class="nav__link">Visit</a></li>
        <li><a href="{{ route('groups') }}"     class="nav__link">Groups</a></li>
        <li><a href="{{ route('faq') }}"        class="nav__link">FAQ</a></li>
    </ul>

    <a href="{{ route('book') }}" class="nav__cta" data-magnetic>
        Book Now
    </a>

    {{-- Mobile toggle --}}
    <button
        class="nav__toggle"
        aria-expanded="false"
        aria-controls="nav-overlay"
        aria-label="Open menu"
    >
        <span class="nav__toggle-bars" aria-hidden="true">
            <span class="nav__toggle-bar"></span>
            <span class="nav__toggle-bar"></span>
            <span class="nav__toggle-bar"></span>
        </span>
        <span>Menu</span>
    </button>
</nav>

{{-- Full-screen overlay menu --}}
<div class="nav-overlay" id="nav-overlay" aria-hidden="true" role="dialog" aria-label="Navigation menu">
    <ul class="nav-overlay__list" role="list">
        <li class="nav-overlay__item">
            <a href="{{ route('experience') }}" class="nav-overlay__link">Experience</a>
        </li>
        <li class="nav-overlay__item">
            <a href="{{ route('visit') }}" class="nav-overlay__link">Visit</a>
        </li>
        <li class="nav-overlay__item">
            <a href="{{ route('groups') }}" class="nav-overlay__link">Groups</a>
        </li>
        <li class="nav-overlay__item">
            <a href="{{ route('faq') }}" class="nav-overlay__link">FAQ</a>
        </li>
    </ul>

    <a href="{{ route('book') }}" class="nav-overlay__book">
        Book Your Experience →
    </a>
</div>
