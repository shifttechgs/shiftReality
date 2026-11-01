GROUP ENQUIRY

{{ $enquiry['occasion'] }}, {{ $enquiry['size'] }} people
{{ $enquiry['day'] ? 'Hoping for '.$enquiry['day'] : 'No date yet' }}

CONTACT
{{ $enquiry['name'] }}
@if (filled($enquiry['organisation']))
{{ $enquiry['organisation'] }}
@endif
Phone:  {{ $enquiry['phone'] }}
@if (filled($enquiry['email']))
Email:  {{ $enquiry['email'] }}
@endif
@if (filled($enquiry['note']))

THEIR NOTE
{{ $enquiry['note'] }}
@endif

Reply on WhatsApp:
{{ $waLink }}

They're waiting on a price. Nothing is booked until you agree a date with {{ $first }}.

Sent from the group enquiry form on the {{ config('shiftreality.name') }} website, {{ $enquiry['received'] }} (Harare time).
