{{-- The booking request email (App\Mail\BookingRequest), for the bookings team.
     Mail clients need tables and inline styles; colours are the site's tokens
     (navy #060B1F, text #0C1A3E, accent #1855E8, tint #F1F5FE, wire #DCE4F5),
     4px corners, pill buttons, Helvetica. The logo is embedded, so it shows
     before the site is live. --}}
@php
    $b    = $booking;
    $font = "font-family:Helvetica,'Helvetica Neue',Arial,sans-serif;";
    $logo = $message->embed(public_path('images/logo-h-light.png'));
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Booking request {{ $b['ref'] }}</title>
</head>
<body style="margin:0;padding:0;background:#F1F5FE;{{ $font }}color:#0C1A3E;-webkit-text-size-adjust:100%">
    {{-- Inbox preview line --}}
    <div style="display:none;max-height:0;overflow:hidden;opacity:0">{{ $b['name'] }} would like {{ $b['day'] }} at {{ $b['time'] }} for {{ $b['people'] }} {{ $guestsLabel }}. Tap to confirm on WhatsApp.</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F1F5FE">
        <tr>
            <td align="center" style="padding:32px 16px">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px">

                    {{-- Header: logo, label, the visit --}}
                    <tr>
                        <td style="background:#060B1F;border-radius:4px 4px 0 0;padding:28px 32px 32px">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align:middle"><img src="{{ $logo }}" width="120" alt="{{ config('shiftreality.name') }}" style="display:block;width:120px;height:auto;border:0"></td>
                                    <td align="right" style="vertical-align:middle;{{ $font }}font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#B8CBF7">Booking request<br><span style="color:rgba(255,255,255,.55)">{{ $b['ref'] }}</span></td>
                                </tr>
                            </table>
                            <p style="margin:36px 0 0;{{ $font }}font-size:34px;line-height:1.1;letter-spacing:-.03em;color:#ffffff">{{ $b['day'] }}</p>
                            <p style="margin:10px 0 0;{{ $font }}font-size:16px;color:rgba(255,255,255,.7)">Entry at {{ $b['time'] }} &middot; {{ $b['people'] }} {{ $guestsLabel }}</p>
                        </td>
                    </tr>

                    {{-- The figures --}}
                    <tr>
                        <td style="background:#ffffff;border-left:1px solid #DCE4F5;border-right:1px solid #DCE4F5;padding:0 32px">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-bottom:1px solid #DCE4F5">
                                <tr>
                                    @foreach ([['Entry', $b['time']], ['Guests', $b['people']], ['Total', $b['total']]] as $i => [$label, $value])
                                        <td width="33%" style="padding:24px 0 24px {{ $i ? '20px' : '0' }};{{ $i ? 'border-left:1px solid #DCE4F5;' : '' }}vertical-align:top">
                                            <p style="margin:0;{{ $font }}font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#1855E8">{{ $label }}</p>
                                            <p style="margin:6px 0 0;{{ $font }}font-size:26px;letter-spacing:-.02em;color:#0C1A3E">{{ $value }}</p>
                                        </td>
                                    @endforeach
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Who, and the one tap to confirm --}}
                    <tr>
                        <td style="background:#ffffff;border-left:1px solid #DCE4F5;border-right:1px solid #DCE4F5;padding:28px 32px 8px">
                            <p style="margin:0;{{ $font }}font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#1855E8">Visitor</p>
                            <p style="margin:8px 0 0;{{ $font }}font-size:22px;letter-spacing:-.01em;color:#0C1A3E">{{ $b['name'] }}</p>
                            <p style="margin:6px 0 0;{{ $font }}font-size:15px;line-height:1.6;color:#4A5675">
                                <a href="{{ $telLink }}" style="color:#4A5675;text-decoration:none">{{ $b['phone'] }}</a>
                                @if (filled($b['email']))
                                    &nbsp;&middot;&nbsp; <a href="mailto:{{ $b['email'] }}" style="color:#4A5675;text-decoration:none">{{ $b['email'] }}</a>
                                @endif
                            </p>

                            {{-- Inline buttons, so they wrap to a second row on a phone instead of squeezing --}}
                            <div style="margin-top:22px;font-size:0;line-height:0">
                                <a href="{{ $waLink }}" style="display:inline-block;margin:0 8px 10px 0;padding:13px 22px;border-radius:999px;background:#060B1F;{{ $font }}font-size:15px;line-height:1.2;white-space:nowrap;color:#ffffff;text-decoration:none">Confirm on WhatsApp &rarr;</a>
                                <a href="{{ $telLink }}" style="display:inline-block;margin:0 8px 10px 0;padding:12px 21px;border-radius:999px;border:1px solid #0C1A3E;{{ $font }}font-size:15px;line-height:1.2;white-space:nowrap;color:#0C1A3E;text-decoration:none">Call</a>
                                @if ($mailtoLink)
                                    <a href="{{ $mailtoLink }}" style="display:inline-block;margin:0 0 10px;padding:12px 21px;border-radius:999px;border:1px solid #0C1A3E;{{ $font }}font-size:15px;line-height:1.2;white-space:nowrap;color:#0C1A3E;text-decoration:none">Email</a>
                                @endif
                            </div>
                            <p style="margin:4px 0 0;{{ $font }}font-size:13px;line-height:1.55;color:#7A84A0">WhatsApp opens with a confirmation already written for {{ $first }}. Check it and press send.</p>
                        </td>
                    </tr>

                    {{-- Their note --}}
                    @if (filled($b['note']))
                        <tr>
                            <td style="background:#ffffff;border-left:1px solid #DCE4F5;border-right:1px solid #DCE4F5;padding:20px 32px 4px">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="background:#F1F5FE;border-radius:4px;padding:18px 20px">
                                            <p style="margin:0;{{ $font }}font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#1855E8">Their note</p>
                                            <p style="margin:8px 0 0;{{ $font }}font-size:16px;line-height:1.55;color:#0C1A3E">{!! nl2br(e($b['note'])) !!}</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- Close of the card --}}
                    <tr>
                        <td style="background:#ffffff;border:1px solid #DCE4F5;border-top:0;border-radius:0 0 4px 4px;padding:24px 32px 28px">
                            <p style="margin:0;padding-top:20px;border-top:1px solid #DCE4F5;{{ $font }}font-size:14px;line-height:1.6;color:#4A5675">Nothing has been paid online. The time is held only once you confirm it with {{ $first }}.</p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 32px 0;{{ $font }}font-size:12px;line-height:1.6;color:#7A84A0">
                            Sent from the booking form on the {{ config('shiftreality.name') }} website, {{ $b['received'] }} (Harare time). Reference {{ $b['ref'] }}.<br>
                            {{ config('shiftreality.location.address') }}, {{ config('shiftreality.location.city') }}
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
