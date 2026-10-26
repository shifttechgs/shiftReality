@extends('layouts.app')

@section('title', 'Book Your Visit — ' . config('shiftreality.name') . ' · Harare')
@section('description', 'Book your visit to ' . config('shiftreality.name') . ' at ' . config('shiftreality.location.address') . ', ' . config('shiftreality.location.city') . '. Pick a day and time — $' . $visit['price_from'] . ' per person, confirmed on WhatsApp.')

{{-- The banner is deep navy, so the nav rests in its light variant --}}
@section('body_class', 'has-hero-photo')

@php
    $price    = (float) $visit['price_from'];
    $max      = $booking['max_people'];
    $people   = (int) old('people', 1);
    // Preselect the day they picked before, else the first day with times left
    $firstOpen = collect($days)->first(fn ($d) => $d['slots'])['date'] ?? null;
    $selected  = old('date', $firstOpen);
    $money     = fn ($n) => '$' . rtrim(rtrim(number_format($n, 2), '0'), '.');
    // The calendar runs Mon … Sun, so the first week starts with blanks before today
    $months    = collect($days)->pluck('month')->unique()->implode(' – ');
    $lead      = $days[0]['iso'] - 1;
    $status    = $openingHours->status();
    $location  = config('shiftreality.location');
    $pickedDay = collect($days)->firstWhere('date', $selected);
    // Weekly closed days from the opening hours, e.g. "Mondays"
    $closedOn  = collect(config('shiftreality.hours'))->filter(fn ($h) => $h === null)->keys()
        ->map(fn ($iso) => now()->startOfWeek()->addDays($iso - 1)->format('l') . 's')->implode(' and ');
    $arrow     = '<svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
@endphp

@section('content')
@include('partials.page-banner', [
    'crumb' => 'Book',
    'title' => 'Book your visit.',
    'intro' => 'Four quick questions, then your booking opens in WhatsApp — we\'ll reply to confirm.',
])

<section class="bk" aria-label="Booking">
    <div class="container">
        <form class="bk__grid" method="POST" action="{{ route('book.send') }}" data-booking data-price="{{ $price }}">
            @csrf

            {{-- ── The flow: one question at a time once booking.js runs;
                 without it, all four show in order and the form still works ── --}}
            <div class="bk-flow">

                <div class="bk-progress" aria-hidden="true">
                    <span class="bk-progress__count"><span data-step-current>01</span> / 04</span>
                    <span class="bk-progress__bar"><span data-step-bar></span></span>
                </div>

                {{-- 01 — Day --}}
                <fieldset class="bk-step" data-step-panel>
                    <legend class="bk-step__title" tabindex="-1">Which <em>day</em> suits you?</legend>
                    <p class="bk-cal__month">{{ $months }}</p>
                    <div class="bk-cal">
                        @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $wd)
                            <span class="bk-cal__wd" aria-hidden="true">{{ $wd }}</span>
                        @endforeach
                        @for ($i = 0; $i < $lead; $i++)
                            <span aria-hidden="true"></span>
                        @endfor
                        @foreach ($days as $day)
                            @if ($day['slots'])
                                <label class="bk-day">
                                    <input type="radio" name="date" value="{{ $day['date'] }}" data-label="{{ $day['label'] }}"
                                           @checked($selected === $day['date']) required>
                                    <span class="bk-day__d">{{ $day['day'] }}</span>
                                    <span class="sr-only">{{ $day['label'] }}</span>
                                    @if ($day['today'])<span class="bk-day__today">Today</span>@endif
                                </label>
                            @else
                                {{-- Closed, or no times left today --}}
                                <span class="bk-day is-off">
                                    <span class="bk-day__d" aria-hidden="true">{{ $day['day'] }}</span>
                                    <span class="sr-only">{{ $day['label'] }}, unavailable</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                    <p class="bk-step__note">@if ($closedOn)Closed on {{ $closedOn }}. @endif Weekends and public holidays are the busiest.</p>
                    @error('date')<p class="bk-step__error">{{ $message }}</p>@enderror
                    <div class="bk-nav">
                        <button type="button" class="pill pill--amber pill--lg" data-next><span>Continue</span>{!! $arrow !!}</button>
                    </div>
                </fieldset>

                {{-- 02 — Time: one group per day; booking.js shows the picked day's --}}
                <fieldset class="bk-step" data-step-panel>
                    <legend class="bk-step__title" tabindex="-1">What <em>time</em> will you arrive?</legend>
                    @foreach ($days as $day)
                        @continue(! $day['slots'])
                        {{-- Without JS every day's times show, each under its day's name --}}
                        <div @class(['bk-times', 'is-active' => $selected === $day['date']]) data-times-for="{{ $day['date'] }}">
                            <p class="bk-times__day">{{ $day['label'] }}</p>
                            <div class="bk-times__grid">
                                @foreach ($day['slots'] as $slot)
                                    <label class="bk-time">
                                        <input type="radio" name="time" value="{{ $slot }}"
                                               @checked(old('date') === $day['date'] && old('time') === $slot) required>
                                        <span>{{ $slot }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    <p class="bk-step__note">This is your entry time — once you're in, stay as long as you like. Most visits take {{ $visit['duration'] }}.</p>
                    @error('time')<p class="bk-step__error">{{ $message }}</p>@enderror
                    <div class="bk-nav">
                        <button type="button" class="bk-nav__back" data-prev>← Back</button>
                        <button type="button" class="pill pill--amber pill--lg" data-next><span>Continue</span>{!! $arrow !!}</button>
                    </div>
                </fieldset>

                {{-- 03 — Guests --}}
                <fieldset class="bk-step" data-step-panel>
                    <legend class="bk-step__title" tabindex="-1">How many <em>guests</em>?</legend>
                    <div class="bk-count">
                        <button type="button" class="bk-count__btn" data-count="-1" aria-label="One fewer guest">−</button>
                        <input class="bk-count__input" type="number" name="people" id="bk-people" value="{{ $people }}" min="1" max="{{ $max }}" inputmode="numeric" aria-label="Number of guests">
                        <button type="button" class="bk-count__btn" data-count="1" aria-label="One more guest">+</button>
                    </div>
                    <p class="bk-step__note">{{ $money($price) }} per guest, ages {{ $visit['min_age'] }} and up. More than {{ $max }}? <a href="{{ route('groups') }}" class="visit__more">Enquire for group rates →</a></p>
                    @error('people')<p class="bk-step__error">{{ $message }}</p>@enderror
                    <div class="bk-nav">
                        <button type="button" class="bk-nav__back" data-prev>← Back</button>
                        <button type="button" class="pill pill--amber pill--lg" data-next><span>Continue</span>{!! $arrow !!}</button>
                    </div>
                </fieldset>

                {{-- 04 — Name, then send --}}
                <fieldset class="bk-step" data-step-panel>
                    <legend class="bk-step__title" tabindex="-1">Whose name is it <em>under</em>?</legend>
                    <div class="bk-fields">
                        <label class="bk-field">
                            <span class="bk-field__label">Full name</span>
                            <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="80" required>
                        </label>
                        <label class="bk-field">
                            <span class="bk-field__label">Anything we should know? <em>Optional — a birthday, a surprise</em></span>
                            <textarea name="note" rows="2" maxlength="300">{{ old('note') }}</textarea>
                        </label>
                    </div>
                    @error('name')<p class="bk-step__error">{{ $message }}</p>@enderror
                    <div class="bk-nav">
                        <button type="button" class="bk-nav__back" data-prev>← Back</button>
                        <button type="submit" class="pill pill--amber pill--xl"><span>Send on WhatsApp</span>{!! $arrow !!}</button>
                    </div>
                    <p class="bk-step__note">Nothing to pay now. WhatsApp opens with your booking written out — press send, and we'll reply to confirm.</p>
                </fieldset>

            </div>

            {{-- ── The visit pass: deep navy, like the banner and footer. It fills in
                 as each question is answered and stays in view on desktop. ── --}}
            <aside class="bk-pass" aria-label="Your visit">
                <div class="bk-pass__head">
                    <span>{{ config('shiftreality.name') }}</span>
                    <span>{{ $location['city'] }}</span>
                </div>

                <p class="bk-pass__day" data-sum="day">{{ $pickedDay['label'] ?? 'Pick a day' }}</p>

                <dl class="bk-pass__rows">
                    <div><dt>Entry</dt><dd data-sum="time">{{ old('time', '—') }}</dd></div>
                    <div><dt>Guests</dt><dd data-sum="people">{{ $people }}</dd></div>
                    <div><dt>Name</dt><dd data-sum="name">{{ old('name') ?: '—' }}</dd></div>
                </dl>

                <p class="bk-pass__total">
                    <span>Total</span>
                    <span class="bk-pass__amount" data-sum="total">{{ $money($price * $people) }}</span>
                </p>

                <dl class="bk-pass__info">
                    <div><dt>Where</dt><dd>{{ $location['address'] }}</dd></div>
                    <div><dt>Today</dt><dd>{{ $status['label'] }}&nbsp;· {{ $status['detail'] }}</dd></div>
                    <div><dt>Call</dt><dd><a href="tel:{{ config('shiftreality.contact.phone') }}">{{ config('shiftreality.contact.phone') }}</a></dd></div>
                </dl>
            </aside>

        </form>
    </div>
</section>
@endsection
