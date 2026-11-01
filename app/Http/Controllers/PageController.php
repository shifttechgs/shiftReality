<?php

namespace App\Http\Controllers;

use App\Support\OpeningHours;

/**
 * The content pages: the rooms (/experience), planning a visit (/visit) and
 * every question (/faq). Everything shown comes from config/shiftreality.php.
 */
class PageController extends Controller
{
    public function experience()
    {
        return view('pages.experience', $this->shared() + [
            // The other spots inside, from our visitors' photos: one per name, minus the rooms above
            'extras' => collect(config('shiftreality.visitor_photos'))
                ->reject(fn ($p) => in_array($p[2], collect(config('shiftreality.shifts'))->pluck('title')->all(), true))
                ->unique(2)
                ->values()
                ->all(),
        ]);
    }

    public function visit()
    {
        return view('pages.visit', $this->shared());
    }

    public function faq()
    {
        return view('pages.faq', $this->shared() + [
            'faq' => config('shiftreality.faq'),
        ]);
    }

    private function shared(): array
    {
        return [
            'shifts'       => config('shiftreality.shifts'),
            'visit'        => config('shiftreality.visit'),
            'location'     => config('shiftreality.location'),
            'contact'      => config('shiftreality.contact'),
            'booking'      => config('shiftreality.booking'),
            'openingHours' => OpeningHours::fromConfig(),
        ];
    }
}
