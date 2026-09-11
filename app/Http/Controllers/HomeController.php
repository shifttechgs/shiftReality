<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home', [
            'shifts'       => config('shiftreality.shifts'),
            'audiences'    => config('shiftreality.audiences'),
            'testimonials' => config('shiftreality.testimonials'),
            'faq'          => config('shiftreality.faq'),
            'hours'        => config('shiftreality.hours'),
            'visit'        => config('shiftreality.visit'),
        ]);
    }
}
