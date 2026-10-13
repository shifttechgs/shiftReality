<div class="top-banner" id="top-banner" role="banner" aria-label="Announcement">
    <div class="top-banner__track-wrap" aria-hidden="true">
        <div class="top-banner__track">
            {{-- Repeated so the loop is seamless --}}
            @for($i = 0; $i < 2; $i++)
                <span class="top-banner__item">Secure your spot now!</span>
                <span class="top-banner__sep">◆</span>
                <a href="{{ route('book') }}" class="top-banner__item top-banner__item--link">Book tickets here</a>
                <span class="top-banner__sep">◆</span>
                <span class="top-banner__item">Weekends selling out fast</span>
                <span class="top-banner__sep">◆</span>
                <a href="{{ route('book') }}" class="top-banner__item top-banner__item--link">From $15 · Reserve now</a>
                <span class="top-banner__sep">◆</span>
                <span class="top-banner__item">Limited slots this weekend</span>
                <span class="top-banner__sep">◆</span>
                <a href="{{ route('book') }}" class="top-banner__item top-banner__item--link">Don't miss out</a>
                <span class="top-banner__sep">◆</span>
            @endfor
        </div>
    </div>

    <button class="top-banner__close" id="top-banner-close" aria-label="Dismiss announcement">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
            <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
    </button>
</div>
