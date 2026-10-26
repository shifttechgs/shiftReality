<?php

namespace App\Http\Controllers;

use App\Support\OpeningHours;

class PricingController extends Controller
{
    public function index()
    {
        $visit  = config('shiftreality.visit');
        $shifts = config('shiftreality.shifts');
        $price  = (float) $visit['price_from'];

        // Fill :price / :duration / :min_age in the ticket copy, as the homepage does
        $fill = fn ($text) => strtr((string) $text, [
            ':price'    => $visit['price_from'],
            ':duration' => str_replace(' ', "\u{00A0}", $visit['duration']),   // never split "1.5 – 2.5 hours"
            ':min_age'  => $visit['min_age'],
        ]);

        $ticket = config('shiftreality.pricing');
        $ticket['price']    = $ticket['price'] === null ? null : $fill($ticket['price']);
        $ticket['includes'] = array_map($fill, $ticket['includes']);

        // The value math comes only from real config: the price, the rooms, the visit length.
        // "1.5 – 2.5 hours" → cost per minute at the longest and shortest visit, in cents.
        preg_match_all('/\d+(?:\.\d+)?/', $visit['duration'], $m);
        $hours     = array_map('floatval', $m[0]);
        $perMinute = count($hours) === 2 && min($hours) > 0
            ? [(int) round($price / (max($hours) * 60) * 100), (int) round($price / (min($hours) * 60) * 100)]
            : null;

        return view('pages.pricing', [
            'visit'        => $visit,
            'shifts'       => $shifts,
            'ticket'       => $ticket,
            'groups'       => config('shiftreality.groups'),
            'faq'          => config('shiftreality.faq'),
            'openingHours' => OpeningHours::fromConfig(),
            'perRoom'      => $shifts ? $price / count($shifts) : null,
            'perMinute'    => $perMinute,
        ]);
    }
}
