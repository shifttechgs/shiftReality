<?php

namespace App\Http\Controllers;

use App\Mail\BookingRequest;
use App\Support\BookingSlots;
use App\Support\OpeningHours;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class BookingController extends Controller
{
    public function show()
    {
        return view('pages.book', [
            'days'         => BookingSlots::fromConfig()->days(),
            'visit'        => config('shiftreality.visit'),
            'booking'      => config('shiftreality.booking'),
            'openingHours' => OpeningHours::fromConfig(),
            // Set once a request has been emailed: /book shows it instead of the form
            'sent'         => session('booking_sent'),
        ]);
    }

    /**
     * Checks the request against the real slots, then emails it to the
     * bookings inbox (config shiftreality.booking.email). The team confirms
     * with the visitor by phone or WhatsApp; nothing is paid online.
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'date'   => ['required', 'date_format:Y-m-d'],
            'time'     => ['required', 'date_format:H:i'],
            'people'   => ['required', 'integer', 'min:1', 'max:'.config('shiftreality.booking.max_people')],
            'name'     => ['required', 'string', 'max:80'],
            'phone'    => ['required', 'string', 'max:20', 'regex:/^\+?[\d\s()-]{7,20}$/'],
            'email'    => ['nullable', 'email', 'max:120'],
            'note'     => ['nullable', 'string', 'max:300'],
        ], [
            'date.required'  => 'Pick a day.',
            'time.required'  => 'Pick a time.',
            'name.required'  => 'Tell us whose name the booking is under.',
            'phone.required' => 'Add a phone number so we can confirm your booking.',
            'phone.regex'    => 'That phone number doesn\'t look right. Use digits, like 077 123 4567.',
            'email.email'    => 'That email address doesn\'t look right.',
        ]);

        if (! BookingSlots::fromConfig()->isAvailable($data['date'], $data['time'])) {
            return back()->withInput()->withErrors(['time' => 'That time is no longer available. Please pick another.']);
        }

        $price   = (float) config('shiftreality.visit.price_from');
        $booking = [
            // A short reference both sides can quote, e.g. RS-K7M2Q (no 0/O, 1/I)
            'ref'      => 'RS-'.collect(range(1, 5))->map(fn () => Str::of('ABCDEFGHJKLMNPQRSTUVWXYZ23456789')->charAt(random_int(0, 31)))->implode(''),
            'name'     => $data['name'],
            'phone'    => trim($data['phone']),
            'email'    => $data['email'] ?? null,
            'note'     => $data['note'] ?? null,
            'day'      => CarbonImmutable::parse($data['date'])->format('l j F'),
            'time'     => $data['time'],
            'people'   => (int) $data['people'],
            'total'    => '$'.rtrim(rtrim(number_format($price * $data['people'], 2), '0'), '.'),
            // when it came in, e.g. "Sat 1 Nov at 18:42"
            'received' => CarbonImmutable::now('Africa/Harare')->format('D j M \a\t H:i'),
        ];

        try {
            Mail::to(config('shiftreality.booking.email'))->send(new BookingRequest($booking));
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'send' => 'We couldn\'t send your booking just now. Please try again, or call us on '.config('shiftreality.contact.phone').'.',
            ]);
        }

        return redirect()->route('book')->with('booking_sent', $booking);
    }
}
