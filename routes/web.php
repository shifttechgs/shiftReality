<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

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

Route::get('/book', fn () => view('pages.placeholder', [
    'title' => 'Book Your Experience',
    'message' => 'Online booking is coming soon. In the meantime, please contact us directly.',
]))->name('book');
