<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PricingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

Route::get('/experience', [PageController::class, 'experience'])->name('experience');
Route::get('/visit', [PageController::class, 'visit'])->name('visit');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

Route::get('/groups', [GroupController::class, 'show'])->name('groups');
Route::post('/groups', [GroupController::class, 'send'])->middleware('throttle:10,1')->name('groups.send');

Route::get('/book', [BookingController::class, 'show'])->name('book');
Route::post('/book', [BookingController::class, 'send'])->middleware('throttle:10,1')->name('book.send');
