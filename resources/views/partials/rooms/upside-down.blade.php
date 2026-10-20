{{-- Upside Down Room — a one-point-perspective room that rotates 180° on scroll
     while the visitor figure stays put, ending up "standing on the ceiling". --}}
<svg class="ud" viewBox="0 0 400 400" role="img" aria-label="Illustration: a room turning upside down around a visitor">
    <g class="ud__room" data-ud-room>
        {{-- Room box: back wall + edges to the corners --}}
        <rect class="ud__wall" x="110" y="110" width="180" height="180" />
        <path class="ud__edge" d="M0 0 L110 110 M400 0 L290 110 M0 400 L110 290 M400 400 L290 290" />

        {{-- Floor planks --}}
        <path class="ud__floor" d="M0 400 L400 400 L290 290 L110 290 Z" />
        <path class="ud__line" d="M146 290 L67 400 M182 290 L133 400 M218 290 L267 400 M254 290 L333 400" />

        {{-- Picture frame on the back wall --}}
        <rect class="ud__frame" x="160" y="140" width="80" height="54" rx="2" />
        <path class="ud__art" d="M168 186 L190 162 L204 176 L214 166 L232 186 Z" />

        {{-- Window on the right wall --}}
        <path class="ud__window" d="M318 146 L360 112 L360 236 L318 238 Z" />
        <path class="ud__line" d="M339 129 L339 237 M318 192 L360 174" />

        {{-- Hanging lamp from the ceiling --}}
        <path class="ud__line" d="M200 40 L200 92" />
        <path class="ud__lamp" d="M180 112 Q200 84 220 112 Z" />
        <circle class="ud__bulb" cx="200" cy="116" r="5" />

        {{-- Chair on the floor --}}
        <path class="ud__chair" d="M136 262 L136 330 M136 300 L184 300 L184 340 M136 300 L136 344 M150 300 L150 334" />

        {{-- Side table + plant --}}
        <path class="ud__chair" d="M238 318 L292 318 M246 318 L242 352 M284 318 L288 352" />
        <path class="ud__plant" d="M264 318 C254 296 258 284 264 276 C270 284 274 296 264 318 Z" />
    </g>

    {{-- The visitor — never rotates. Stands right of centre so the flipped lamp clears them. --}}
    <g class="ud__person" data-ud-person>
        <circle cx="322" cy="318" r="9" />
        <path d="M322 328 L322 360 M322 338 L308 352 M322 338 L336 352 M322 360 L312 384 M322 360 L332 384" />
    </g>

    <text class="ud__label" x="200" y="22" text-anchor="middle" data-ud-label>CEILING</text>
</svg>
