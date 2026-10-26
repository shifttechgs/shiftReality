<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PricingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

Route::get('/experience', fn () => view('pages.placeholder', [
    'title' => 'The Experience',
    'message' => 'A deeper look at what awaits you inside ShiftReality.',
]))->name('experience');

Route::get('/visit', fn () => view('pages.placeholder', [
    'title' => 'Plan Your Visit',
    'message' => 'Everything you need to know before you arrive.',
]))->name('visit');

Route::get('/groups', fn () => view('pages.placeholder', [
    'title' => 'Groups & Events',
    'message' => 'ShiftReality for corporate teams, school groups, birthday parties and more.',
]))->name('groups');

Route::get('/faq', fn () => view('pages.placeholder', [
    'title' => 'Frequently Asked Questions',
    'message' => 'Your questions, answered.',
]))->name('faq');

Route::get('/book', [BookingController::class, 'show'])->name('book');
Route::post('/book', [BookingController::class, 'send'])->middleware('throttle:10,1')->name('book.send');
