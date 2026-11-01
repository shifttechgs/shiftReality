{{-- Every inner page's close: the home page's final call to action
     (partials/visit-band) on our vortex photo, the visit's facts beside the
     headline, the bar with the ask and Book My Spot. Hands over to the footer.
     Optional: title, sub, ask. Needs $shifts and $openingHours. --}}
@php
    $closeVisit = config('shiftreality.visit');
    $closeDays  = collect(config('shiftreality.hours'))->filter()->count();
    $closeMap   = 'https://www.google.com/maps/search/?api=1&query='
        . rawurlencode(config('shiftreality.location.address') . ', ' . config('shiftreality.location.city'));
@endphp
@include('partials.visit-band', [
    'id'        => 'visit-today',
    'headingId' => 'final-heading',
    'eyebrow'   => 'Your visit',
    'title'     => $title ?? '<span class="nowrap">Question everything.</span><br><span class="nowrap">Starting today.</span>',
    'sub'       => $sub ?? 'Zimbabwe\'s first optical illusion experience. Come with your people and a camera.',
    'list'      => [
        [config('shiftreality.location.address') . ', ' . config('shiftreality.location.city'), $closeMap],
        ['Entry from $' . $closeVisit['price_from'], null],
        ['Ages ' . $closeVisit['min_age'] . ' and up', null],
        [$closeDays === 7 ? 'Open every day' : 'Open ' . $closeDays . ' days a week', null],
    ],
    'ask'       => $ask ?? 'Pick a day and time.',
    'pill'      => 'Book My Spot',
    'bg'        => config('shiftreality.band_bg'),
])
