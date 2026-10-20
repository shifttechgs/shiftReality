<?php

namespace App\Http\Controllers;

use App\Support\OpeningHours;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home', [
            'shifts'       => config('shiftreality.shifts'),
            'audiences'    => config('shiftreality.audiences'),
            'testimonials' => config('shiftreality.testimonials'),
            'faq'          => config('shiftreality.faq'),
            'openingHours' => OpeningHours::fromConfig(),
            'visit'        => config('shiftreality.visit'),
        ]);
    }
}
