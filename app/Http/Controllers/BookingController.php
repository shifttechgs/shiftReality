<?php

namespace App\Http\Controllers;

use App\Support\BookingSlots;
use App\Support\OpeningHours;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function show()
    {
        return view('pages.book', [
            'days'         => BookingSlots::fromConfig()->days(),
            'visit'        => config('shiftreality.visit'),
            'booking'      => config('shiftreality.booking'),
            'openingHours' => OpeningHours::fromConfig(),
        ]);
    }

    /**
     * Checks the request against the real slots, then hands it to WhatsApp
     * pre-written, so the team confirms it in the chat.
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'date'   => ['required', 'date_format:Y-m-d'],
            'time'   => ['required', 'date_format:H:i'],
            'people' => ['required', 'integer', 'min:1', 'max:'.config('shiftreality.booking.max_people')],
            'name'   => ['required', 'string', 'max:80'],
            'note'   => ['nullable', 'string', 'max:300'],
        ], [
            'date.required' => 'Pick a day.',
            'time.required' => 'Pick a time.',
            'name.required' => 'Tell us whose name the booking is under.',
        ]);

        if (! BookingSlots::fromConfig()->isAvailable($data['date'], $data['time'])) {
            return back()->withInput()->withErrors(['time' => 'That time is no longer available — please pick another.']);
        }

        $price = (float) config('shiftreality.visit.price_from');
        $total = $price * $data['people'];
        $day   = CarbonImmutable::parse($data['date'])->format('l j F');

        $message = implode("\n", array_filter([
            'Hi Reality Shift! I\'d like to book a visit.',
            '',
            "Name: {$data['name']}",
            "Day: {$day}",
            "Time: {$data['time']}",
            "People: {$data['people']}",
            'Total: $'.rtrim(rtrim(number_format($total, 2), '0'), '.'),
            filled($data['note'] ?? null) ? "Note: {$data['note']}" : null,
        ], fn ($line) => $line !== null));

        return redirect()->away('https://wa.me/'.config('shiftreality.contact.whatsapp').'?text='.rawurlencode($message));
    }
}
