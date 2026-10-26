{{-- Footer — on every page, so it reads its own data rather than relying on a controller --}}
@php
    $footHours    = \App\Support\OpeningHours::fromConfig();
    $footStatus   = $footHours->status();
    $footLocation = config('shiftreality.location');
    $footContact  = config('shiftreality.contact');
    $footSocial   = array_filter(config('shiftreality.social', []));
    $footMaps     = 'https://www.google.com/maps/dir/?api=1&destination='
        . $footLocation['coordinates']['lat'] . ',' . $footLocation['coordinates']['lng'];
    // 0790034005 → "079 003 4005" to read, "+263790034005" to dial
    $footPhone    = preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '$1 $2 $3', $footContact['phone']);
    $footTel      = '+263' . ltrim($footContact['phone'], '0');
    $socialIcons  = [
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.75" fill="currentColor" stroke="none"/>',
        'facebook'  => '<path d="M15 3h-2.5A3.5 3.5 0 0 0 9 6.5V9H6.5v3.5H9V21h3.5v-8.5H15l.5-3.5h-3V7a1 1 0 0 1 1-1H15z"/>',
        'tiktok'    => '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5"/><path d="M14 3a5 5 0 0 0 5 5"/>',
        'x'         => '<path d="M4 4l16 16M20 4 4 20"/>',
    ];
@endphp
<footer class="footer" role="contentinfo">

    <div class="container footer__container">

        <div class="footer__grid">

            {{-- Brand --}}
            <div class="footer__brand">
                <a href="{{ route('home') }}" class="footer__logo" aria-label="{{ config('shiftreality.name') }} home">
                    <img src="{{ asset('images/logo-light.png') }}" alt="{{ config('shiftreality.name') }}" width="88" height="88" loading="lazy">
                </a>
                <p class="footer__tagline">
                    Zimbabwe's optical illusion experience. Four rooms built to make your eyes argue with your brain.
                </p>
                @if ($footSocial)
                    <ul class="footer__social" role="list">
                        @foreach ($footSocial as $network => $url)
                            <li>
                                <a href="{{ $url }}" class="footer__social-link" target="_blank" rel="noopener" aria-label="{{ config('shiftreality.name') }} on {{ ucfirst($network) }}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $socialIcons[$network] ?? '' !!}</svg>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Explore --}}
            <nav class="footer__col" aria-label="Footer navigation">
                <p class="footer__title">Explore</p>
                <ul class="footer__links" role="list">
                    <li><a href="{{ route('experience') }}" class="footer__link">The Experience</a></li>
                    <li><a href="{{ route('pricing') }}"    class="footer__link">Pricing</a></li>
                    <li><a href="{{ route('visit') }}"      class="footer__link">Plan Your Visit</a></li>
                    <li><a href="{{ route('groups') }}"     class="footer__link">Groups &amp; Events</a></li>
                    <li><a href="{{ route('faq') }}"        class="footer__link">FAQ</a></li>
                    <li><a href="{{ route('book') }}"       class="footer__link">Book Tickets</a></li>
                </ul>
            </nav>

            {{-- Visit: live status, hours, address --}}
            <div class="footer__col">
                <p class="footer__title">Visit</p>
                <p @class(['footer__status', 'is-open' => $footStatus['open']])>
                    <span class="footer__dot" aria-hidden="true"></span>
                    {{ $footStatus['label'] }}&nbsp;· {{ $footStatus['detail'] }}
                </p>
                <dl class="footer__hours">
                    @foreach ($footHours->rows() as $row)
                        <div @class(['footer__hours-row', 'is-today' => $row['today']])>
                            <dt>{{ $row['days'] }}</dt>
                            <dd>{{ $row['time'] }}</dd>
                        </div>
                    @endforeach
                </dl>
                <address class="footer__address">
                    {{ $footLocation['address'] }}<br>
                    {{ $footLocation['city'] }}, {{ $footLocation['country'] }}
                </address>
                <a href="{{ $footMaps }}" class="footer__link footer__link--arrow" target="_blank" rel="noopener">Get directions <span aria-hidden="true">↗</span></a>
            </div>

            {{-- Contact --}}
            <div class="footer__col">
                <p class="footer__title">Contact</p>
                <ul class="footer__links" role="list">
                    <li><a href="https://wa.me/{{ $footContact['whatsapp'] }}" class="footer__link" target="_blank" rel="noopener">WhatsApp us</a></li>
                    <li><a href="tel:{{ $footTel }}" class="footer__link">{{ $footPhone }}</a></li>
                    <li><a href="mailto:{{ $footContact['email'] }}" class="footer__link">{{ $footContact['email'] }}</a></li>
                </ul>
            </div>

        </div>

        <div class="footer__bottom">
            <p class="footer__legal">&copy; {{ date('Y') }} {{ config('shiftreality.name') }} · {{ $footLocation['city'] }}, {{ $footLocation['country'] }}</p>
            <p class="footer__credit">
                Designed by <a href="https://www.shifttechgs.com" class="footer__credit-link" target="_blank" rel="noopener">ShiftTech</a>
            </p>
            <a href="#main-content" class="footer__top">Back to top <span aria-hidden="true">↑</span></a>
        </div>
    </div>

    {{-- The name, cropped by the bottom edge. Letters are split so footer-motion.js
         can raise them as the footer is uncovered and lift them under the cursor. --}}
    <p class="footer__watermark" aria-hidden="true" data-watermark>
        <span class="footer__wm-word">@foreach (mb_str_split('Reality') as $ch)<span class="footer__wm-l">{{ $ch }}</span>@endforeach</span>
        <em class="footer__wm-word">@foreach (mb_str_split('Shift') as $ch)<span class="footer__wm-l">{{ $ch }}</span>@endforeach</em>
    </p>

</footer>
