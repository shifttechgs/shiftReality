BOOKING REQUEST {{ $booking['ref'] }}

{{ $booking['day'] }}
Entry at {{ $booking['time'] }}, {{ $booking['people'] }} {{ $guestsLabel }}

Entry:  {{ $booking['time'] }}
Guests: {{ $booking['people'] }}
Total:  {{ $booking['total'] }}

VISITOR
{{ $booking['name'] }}
Phone:  {{ $booking['phone'] }}
@if (filled($booking['email']))
Email:  {{ $booking['email'] }}
@endif
@if (filled($booking['note']))

THEIR NOTE
{{ $booking['note'] }}
@endif

Confirm on WhatsApp (a confirmation is already written for {{ $first }}):
{{ $waLink }}

Nothing has been paid online. The time is held only once you confirm it with {{ $first }}.

Sent from the booking form on the {{ config('shiftreality.name') }} website, {{ $booking['received'] }} (Harare time).
Reference {{ $booking['ref'] }}
