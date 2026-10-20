{{-- Five stars filled to an exact rating (4.8 fills 80% of the fifth).
     Usage: @include('partials.stars', ['rating' => 4.8, 'size' => 14])
     Colours come from --stars-fill / --stars-track on a parent. --}}
<span class="stars" style="--rating: {{ $rating }}" aria-hidden="true">
    @foreach (['track', 'fill'] as $layer)
        <span class="stars__{{ $layer }}">
            @for ($i = 0; $i < 5; $i++)
                <svg width="{{ $size ?? 14 }}" height="{{ $size ?? 14 }}" viewBox="0 0 24 24"><path d="M12 2.5l2.94 6.24 6.81.82-5.02 4.68 1.3 6.74L12 17.6l-6.03 3.38 1.3-6.74-5.02-4.68 6.81-.82z"/></svg>
            @endfor
        </span>
    @endforeach
</span>
