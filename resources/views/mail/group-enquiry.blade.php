{{-- A group enquiry (App\Mail\GroupEnquiry), for the bookings team. Same
     build as mail/booking-request: tables, inline styles, the site's tokens,
     the logo embedded. --}}
@php
    $e    = $enquiry;
    $font = "font-family:Helvetica,'Helvetica Neue',Arial,sans-serif;";
    $logo = $message->embed(public_path('images/logo-h-light.png'));
    $label = "margin:0;{$font}font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#1855E8";
    $side  = 'background:#ffffff;border-left:1px solid #DCE4F5;border-right:1px solid #DCE4F5;';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Group enquiry</title>
</head>
<body style="margin:0;padding:0;background:#F1F5FE;{{ $font }}color:#0C1A3E;-webkit-text-size-adjust:100%">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0">{{ $e['name'] }} is asking about a group of {{ $e['size'] }}{{ $e['day'] ? ' on '.$e['day'] : '' }}. Reply with a price.</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F1F5FE">
        <tr>
            <td align="center" style="padding:32px 16px">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px">

                    <tr>
                        <td style="background:#060B1F;border-radius:4px 4px 0 0;padding:28px 32px 32px">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align:middle"><img src="{{ $logo }}" width="120" alt="{{ config('shiftreality.name') }}" style="display:block;width:120px;height:auto;border:0"></td>
                                    <td align="right" style="vertical-align:middle;{{ $font }}font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#B8CBF7">Group enquiry</td>
                                </tr>
                            </table>
                            <p style="margin:36px 0 0;{{ $font }}font-size:34px;line-height:1.1;letter-spacing:-.03em;color:#ffffff">{{ $e['occasion'] }}, {{ $e['size'] }} people</p>
                            <p style="margin:10px 0 0;{{ $font }}font-size:16px;color:rgba(255,255,255,.7)">{{ $e['day'] ? 'Hoping for '.$e['day'] : 'No date yet' }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="{{ $side }}padding:28px 32px 8px">
                            <p style="{{ $label }}">Contact</p>
                            <p style="margin:8px 0 0;{{ $font }}font-size:22px;letter-spacing:-.01em;color:#0C1A3E">{{ $e['name'] }}</p>
                            @if (filled($e['organisation']))
                                <p style="margin:4px 0 0;{{ $font }}font-size:15px;color:#4A5675">{{ $e['organisation'] }}</p>
                            @endif
                            <p style="margin:6px 0 0;{{ $font }}font-size:15px;line-height:1.6;color:#4A5675">
                                <a href="{{ $telLink }}" style="color:#4A5675;text-decoration:none">{{ $e['phone'] }}</a>
                                @if (filled($e['email']))
                                    &nbsp;&middot;&nbsp; <a href="mailto:{{ $e['email'] }}" style="color:#4A5675;text-decoration:none">{{ $e['email'] }}</a>
                                @endif
                            </p>

                            <div style="margin-top:22px;font-size:0;line-height:0">
                                <a href="{{ $waLink }}" style="display:inline-block;margin:0 8px 10px 0;padding:13px 22px;border-radius:999px;background:#060B1F;{{ $font }}font-size:15px;line-height:1.2;white-space:nowrap;color:#ffffff;text-decoration:none">Reply on WhatsApp &rarr;</a>
                                <a href="{{ $telLink }}" style="display:inline-block;margin:0 8px 10px 0;padding:12px 21px;border-radius:999px;border:1px solid #0C1A3E;{{ $font }}font-size:15px;line-height:1.2;white-space:nowrap;color:#0C1A3E;text-decoration:none">Call</a>
                                @if ($mailtoLink)
                                    <a href="{{ $mailtoLink }}" style="display:inline-block;margin:0 0 10px;padding:12px 21px;border-radius:999px;border:1px solid #0C1A3E;{{ $font }}font-size:15px;line-height:1.2;white-space:nowrap;color:#0C1A3E;text-decoration:none">Email</a>
                                @endif
                            </div>
                        </td>
                    </tr>

                    @if (filled($e['note']))
                        <tr>
                            <td style="{{ $side }}padding:20px 32px 4px">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="background:#F1F5FE;border-radius:4px;padding:18px 20px">
                                            <p style="{{ $label }}">Their note</p>
                                            <p style="margin:8px 0 0;{{ $font }}font-size:16px;line-height:1.55;color:#0C1A3E">{!! nl2br(e($e['note'])) !!}</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="background:#ffffff;border:1px solid #DCE4F5;border-top:0;border-radius:0 0 4px 4px;padding:24px 32px 28px">
                            <p style="margin:0;padding-top:20px;border-top:1px solid #DCE4F5;{{ $font }}font-size:14px;line-height:1.6;color:#4A5675">They're waiting on a price. Nothing is booked until you agree a date with {{ $first }}.</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px 0;{{ $font }}font-size:12px;line-height:1.6;color:#7A84A0">
                            Sent from the group enquiry form on the {{ config('shiftreality.name') }} website, {{ $e['received'] }} (Harare time).<br>
                            {{ config('shiftreality.location.address') }}, {{ config('shiftreality.location.city') }}
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
