{{-- The hero's booking card: day, time, guests and name in one go, then the
     request opens in WhatsApp — the same route and checks as /book
     (BookingController@send), so nothing is paid online. A failed check
     comes back here with the answers kept. hero-book.js fills the time list
     for the picked day and keeps the total; the day opens as a calendar of
     the bookable fortnight (ui/date-picker.js), the time as the site's own
     dropdown (ui/select.js). Without JS the first day's times show and the server still
     checks the pick.
     Params: $days (BookingSlots), $booking, $visit. --}}
@php
    $openDays = collect($days)->filter(fn ($d) => $d['slots'])->values();
    $price    = (float) $visit['price_from'];
    $max      = $booking['max_people'];
    $money    = fn ($n) => '$' . rtrim(rtrim(number_format($n, 2), '0'), '.');
    $dayPick  = old('date', $openDays->first()['date'] ?? null);
    $dayRow   = $openDays->firstWhere('date', $dayPick) ?? $openDays->first();
    $people   = max(1, min($max, (int) old('people', 1)));
    $arrow    = '<svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
@endphp
<form class="walk__card" method="POST" action="{{ route('book.send') }}" aria-labelledby="hero-book-title"
      data-hero-book data-price="{{ $price }}">
    @csrf
    <div class="walk__card-head">
        <p class="walk__card-title" id="hero-book-title">Book your visit</p>
        <p class="walk__card-price"><strong>{{ $money($price) }}</strong> per person</p>
    </div>

    @if ($openDays->isEmpty())
        <p class="walk__card-note">No times are open online in the next {{ $booking['days_ahead'] }} days. Message us on WhatsApp and we'll find you one.</p>
    @else
        <div class="walk__card-grid">
            {{-- Day and Time are real selects, dressed by ui/date-picker.js (the
                 bookable fortnight as a small calendar) and ui/select.js (the
                 site's own dropdown); closed days are disabled options --}}
            <label class="walk__field">
                <span class="walk__field-label">Day</span>
                <select name="date" required data-hero-day data-datepicker>
                    @foreach ($days as $day)
                        @php $date = \Carbon\Carbon::parse($day['date']); @endphp
                        <option value="{{ $day['date'] }}" data-slots='@json($day['slots'])' data-today="{{ $day['today'] ? 1 : 0 }}"
                                @selected($day['date'] === $dayRow['date']) @disabled(! $day['slots'])>
                            {{ $day['today'] ? 'Today, ' . $date->format('j M') : $date->format('D j M') }}{{ $day['slots'] ? '' : ' (closed)' }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="walk__field">
                <span class="walk__field-label">Time</span>
                <select name="time" required data-hero-time data-dropdown>
                    @foreach ($dayRow['slots'] as $slot)
                        <option value="{{ $slot }}" @selected(old('time') === $slot)>{{ $slot }}</option>
                    @endforeach
                </select>
            </label>

            <div class="walk__field">
                <span class="walk__field-label" id="hero-book-guests">Guests</span>
                <div class="walk__count">
                    <button type="button" data-hero-count="-1" aria-label="One fewer guest">&minus;</button>
                    <input type="number" name="people" value="{{ $people }}" min="1" max="{{ $max }}" inputmode="numeric" aria-labelledby="hero-book-guests" data-hero-people>
                    <button type="button" data-hero-count="1" aria-label="One more guest">+</button>
                </div>
            </div>

            <label class="walk__field">
                <span class="walk__field-label">Your name</span>
                <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="80" required placeholder="Full name">
            </label>
        </div>

        @if ($errors->hasAny(['date', 'time', 'people', 'name']))
            <p class="walk__card-error" role="alert">{{ $errors->first('date') ?: $errors->first('time') ?: $errors->first('people') ?: $errors->first('name') }}</p>
        @endif

        <div class="walk__card-foot">
            <p class="walk__card-total">Total <strong data-hero-total>{{ $money($price * $people) }}</strong></p>
            <button type="submit" class="pill pill--amber pill--lg"><span>Book My Spot</span>{!! $arrow !!}</button>
        </div>
        <p class="walk__card-note">Nothing to pay now. WhatsApp opens with your booking written out, and the team replies to confirm. More than {{ $max }}? <a href="{{ route('groups') }}">Group rates</a></p>
    @endif
</form>
