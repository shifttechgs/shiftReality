<?php

namespace App\Http\Controllers;

use App\Support\OpeningHours;

class PricingController extends Controller
{
    public function index()
    {
        $visit  = config('shiftreality.visit');
        $shifts = config('shiftreality.shifts');

        // Fill :price / :duration / :min_age in the ticket copy, as the homepage does
        $fill = fn ($text) => strtr((string) $text, [
            ':price'    => $visit['price_from'],
            ':duration' => str_replace(' ', "\u{00A0}", $visit['duration']),   // never split "1.5 – 2.5 hours"
            ':min_age'  => $visit['min_age'],
            ':rooms'    => collect($shifts)->pluck('title')->implode(', '),
        ]);

        $ticket = config('shiftreality.pricing');
        $ticket['price']    = $ticket['price'] === null ? null : $fill($ticket['price']);
        $ticket['includes'] = array_map($fill, $ticket['includes']);
        $ticket['terms']    = array_map(fn ($t) => [$t[0], $fill($t[1])], $ticket['terms']);

        return view('pages.pricing', [
            'visit'        => $visit,
            'shifts'       => $shifts,
            'ticket'       => $ticket,
            'groups'       => config('shiftreality.groups'),
            'faq'          => config('shiftreality.faq'),
            'openingHours' => OpeningHours::fromConfig(),
        ]);
    }
}
