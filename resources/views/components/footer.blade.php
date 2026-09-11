<footer class="footer" role="contentinfo">
    <div class="container">
        <div class="footer__inner">

            {{-- Brand column --}}
            <div>
                <div class="footer__brand">{{ config('shiftreality.name') }}</div>
                <p class="footer__tagline">
                    An immersive optical illusion experience in Zimbabwe.<br>
                    Reality is negotiable.
                </p>
                @if(config('shiftreality.contact.email'))
                    <a href="mailto:{{ config('shiftreality.contact.email') }}" class="footer__contact">
                        {{ config('shiftreality.contact.email') }}
                    </a>
                @endif
            </div>

            {{-- Explore column --}}
            <div>
                <p class="footer__nav-title">Explore</p>
                <nav aria-label="Footer navigation">
                    <a href="{{ route('experience') }}" class="footer__nav-link">The Experience</a>
                    <a href="{{ route('visit') }}"      class="footer__nav-link">Plan Your Visit</a>
                    <a href="{{ route('groups') }}"     class="footer__nav-link">Groups &amp; Events</a>
                    <a href="{{ route('faq') }}"        class="footer__nav-link">FAQ</a>
                </nav>
            </div>

            {{-- Visit column --}}
            <div>
                <p class="footer__nav-title">Visit</p>
                <p style="font-size: 0.9375rem; color: var(--color-sr-muted); line-height: 1.6;">
                    {{ config('shiftreality.location.city') }},<br>
                    {{ config('shiftreality.location.country') }}
                </p>
                <div style="margin-top: 1.25rem;">
                    <a href="{{ route('book') }}"
                       class="btn btn-primary"
                       data-magnetic
                       style="font-size: 0.8rem; padding: 0.75rem 1.5rem;">
                        Book Now
                    </a>
                </div>
            </div>

        </div>

        <div class="footer__bottom">
            <p class="footer__legal">
                &copy; {{ date('Y') }} {{ config('shiftreality.name') }}. All rights reserved.
            </p>
            <p class="footer__legal">
                {{ config('shiftreality.location.city') }}, {{ config('shiftreality.location.country') }}
            </p>
        </div>
    </div>
</footer>
