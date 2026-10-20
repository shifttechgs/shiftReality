{{-- Beuchet Chair — the chair is two separate pieces metres apart. On scroll the
     camera lines them up: the far seat lands on the near legs, so the person on it
     looks tiny and the person by the legs looks giant. --}}
<svg class="bc" viewBox="0 0 400 320" role="img" aria-label="Illustration: two chair parts lining up into one chair, making one person look giant and one tiny">
    {{-- Perspective floor — paths are rebuilt by rooms/beuchet.js as the horizon moves --}}
    <path class="bc__grid" data-bc-grid d="" />

    {{-- Near piece: big chair legs + the "giant" --}}
    <g class="bc__near">
        <path class="bc__chair" d="M190 236 L190 306 M262 236 L262 306 M204 236 L200 296 M248 236 L252 296" />
        <g class="bc__person bc__person--giant">
            <circle cx="112" cy="140" r="15" />
            <path d="M112 156 L112 226 M112 176 L88 206 M112 176 L136 206 M112 226 L96 300 M112 226 L128 300" />
        </g>
    </g>

    {{-- Far piece: small seat + backrest + the "tiny" one, slides into alignment --}}
    <g class="bc__far" data-bc-far>
        <path class="bc__chair" d="M190 236 L262 236 M262 236 L262 186" />
        <g class="bc__person bc__person--tiny">
            <circle cx="232" cy="208" r="5" />
            <path d="M232 213 L232 230 M232 220 L224 228 M232 220 L240 226 M232 230 L222 236" />
        </g>
    </g>

    {{-- Ghost outlines: both people's true, identical height --}}
    <g class="bc__truth" data-bc-truth>
        <path d="M60 300 L60 124 M52 124 L68 124 M52 300 L68 300" />
        <path d="M300 300 L300 124 M292 124 L308 124 M292 300 L308 300" />
        <text x="60" y="114" text-anchor="middle">1.7 m</text>
        <text x="300" y="114" text-anchor="middle">1.7 m</text>
    </g>

    <text class="bc__caption" x="200" y="34" text-anchor="middle" data-bc-caption>Two pieces. Metres apart.</text>
</svg>
