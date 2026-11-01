@extends('layouts.app')

@section('title', 'Book Your Visit · ' . config('shiftreality.name') . ' · Harare')
@section('description', 'Book your visit to ' . config('shiftreality.name') . ' at ' . config('shiftreality.location.address') . ', ' . config('shiftreality.location.city') . '. Pick a day and time. $' . $visit['price_from'] . ' per person. Send a request and we confirm by phone.')

{{-- The banner is deep navy, so the nav rests in its light variant --}}
@section('body_class', 'has-hero-photo')

@php
    $price    = (float) $visit['price_from'];
    $max      = $booking['max_people'];
    // A day and guests can arrive in the link (the home hero's card on phones)
    $people   = max(1, min($max, (int) old('people', request()->integer('people', 1))));
    // Preselect the day they picked before (or in the link), else the first day with times left
    $firstOpen = collect($days)->first(fn ($d) => $d['slots'])['date'] ?? null;
    $linked    = collect($days)->first(fn ($d) => $d['slots'] && $d['date'] === request('date'))['date'] ?? null;
    $selected  = old('date', $linked ?? $firstOpen);
    // The home card hands over its time and name as well; with a valid day and
    // time in the link, the form opens on the last step (contact details)
    $linkedDay = collect($days)->firstWhere('date', $linked);
    $linkTime  = $linkedDay && in_array(request('time'), $linkedDay['slots'], true) ? request('time') : null;
    $pickTime  = old('time', $linkTime);
    $startStep = (! $errors->any() && $linkTime) ? 3 : 0;
    $money     = fn ($n) => '$' . rtrim(rtrim(number_format($n, 2), '0'), '.');
    // The calendar runs Mon … Sun, so the first week starts with blanks before today
    $lead      = $days[0]['iso'] - 1;
    // …and runs in pages of four weeks (booking.js shows one at a time)
    $calPages  = collect($days)->values()->groupBy(fn ($d, $i) => intdiv($lead + $i, 28));
    $status    = $openingHours->status();
    $location  = config('shiftreality.location');
    $pickedDay = collect($days)->firstWhere('date', $selected);
    // Weekly closed days from the opening hours, e.g. "Mondays"
    $closedOn  = collect(config('shiftreality.hours'))->filter(fn ($h) => $h === null)->keys()
        ->map(fn ($iso) => now()->startOfWeek()->addDays($iso - 1)->format('l') . 's')->implode(' and ');
    // 0790034005 → "079 003 4005" to read, "+263790034005" to dial (as the footer)
    $phone     = preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '$1 $2 $3', config('shiftreality.contact.phone'));
    $tel       = '+263' . ltrim(config('shiftreality.contact.phone'), '0');
    $arrow     = '<svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
@endphp

@section('content')
@include('partials.page-banner', [
    'crumb' => 'Book',
    'title' => 'Book your visit.',
    'intro' => $sent ? 'Your request is with our bookings team.' : 'Four quick questions, then send your request. We\'ll call or WhatsApp you to confirm.',
])

<section class="bk" aria-label="Booking">
    <div class="container">
        @if ($sent)
            {{-- Sent: what they asked for, and what happens next --}}
            <div class="bk__grid bk-done">
                <div class="bk-done__main" role="status">
                    <p class="bk-done__label">Request sent</p>
                    <h2 class="bk-done__title">Thanks, {{ \Illuminate\Support\Str::before($sent['name'], ' ') }}. We've got it.</h2>
                    <p class="bk-done__text">Your booking request is with our team. We'll call or WhatsApp you on {{ $sent['phone'] }} to confirm your time. Nothing to pay until you visit.</p>
                    <div class="bk-done__actions">
                        <a href="{{ route('home') }}" class="pill pill--amber pill--lg"><span>Back to home</span>{!! $arrow !!}</a>
                        <a href="{{ route('book') }}" class="visit__more">Make another booking →</a>
                    </div>
                </div>
                <aside class="bk-pass" aria-label="Your request">
                    <div class="bk-pass__head">
                        <span>{{ config('shiftreality.name') }}</span>
                        <span>{{ $location['city'] }}</span>
                    </div>
                    <p class="bk-pass__day">{{ $sent['day'] }}</p>
                    <dl class="bk-pass__rows">
                        <div><dt>Entry</dt><dd>{{ $sent['time'] }}</dd></div>
                        <div><dt>Guests</dt><dd>{{ $sent['people'] }}</dd></div>
                        <div><dt>Name</dt><dd>{{ $sent['name'] }}</dd></div>
                        <div><dt>Reference</dt><dd>{{ $sent['ref'] }}</dd></div>
                    </dl>
                    <p class="bk-pass__total">
                        <span>Total</span>
                        <span class="bk-pass__amount">{{ $sent['total'] }}</span>
                    </p>
                    <dl class="bk-pass__info">
                        <div><dt>Where</dt><dd>{{ $location['address'] }}</dd></div>
                        <div><dt>Call</dt><dd><a href="tel:{{ $tel }}">{{ $phone }}</a></dd></div>
                    </dl>
                </aside>
            </div>
        @else
        <form class="bk__grid" method="POST" action="{{ route('book.send') }}" data-booking data-price="{{ $price }}" data-start-step="{{ $startStep }}">
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
                    <legend class="bk-step__title" tabindex="-1">Which day suits you?</legend>
                    {{-- Four weeks to a page from today's week (booking.js shows one at
                         a time, with ‹ ›); without JS every page shows, each titled --}}
                    <div class="bk-cal-pages" data-cal>
                        @foreach ($calPages as $p => $pageDays)
                            <div class="bk-cal-page" data-cal-page>
                                <div class="bk-cal__head">
                                    <button type="button" class="bk-cal__nav" data-cal-step="-1" aria-label="Earlier dates" hidden>&lsaquo;</button>
                                    <p class="bk-cal__month">{{ $pageDays->pluck('month')->unique()->implode(' to ') }}</p>
                                    <button type="button" class="bk-cal__nav" data-cal-step="1" aria-label="Later dates" hidden>&rsaquo;</button>
                                </div>
                                <div class="bk-cal">
                                    @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $wd)
                                        <span class="bk-cal__wd" aria-hidden="true">{{ $wd }}</span>
                                    @endforeach
                                    @if ($p === 0)
                                        @for ($i = 0; $i < $lead; $i++)
                                            <span aria-hidden="true"></span>
                                        @endfor
                                    @endif
                                    @foreach ($pageDays as $day)
                                        @php $firstOfMonth = $day['day'] === '1'; @endphp
                                        @if ($day['slots'])
                                            <label class="bk-day">
                                                <input type="radio" name="date" value="{{ $day['date'] }}" data-label="{{ $day['label'] }}"
                                                       @checked($selected === $day['date']) required>
                                                @if ($firstOfMonth)<span class="bk-day__mon" aria-hidden="true">{{ substr($day['month'], 0, 3) }}</span>@endif
                                                <span class="bk-day__d">{{ $day['day'] }}</span>
                                                <span class="sr-only">{{ $day['label'] }}</span>
                                                @if ($day['today'])<span class="bk-day__today" aria-hidden="true"></span>@endif
                                            </label>
                                        @else
                                            {{-- Closed, or no times left today --}}
                                            <span class="bk-day is-off">
                                                @if ($firstOfMonth)<span class="bk-day__mon" aria-hidden="true">{{ substr($day['month'], 0, 3) }}</span>@endif
                                                <span class="bk-day__d" aria-hidden="true">{{ $day['day'] }}</span>
                                                <span class="sr-only">{{ $day['label'] }}, unavailable</span>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
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
                    <legend class="bk-step__title" tabindex="-1">What time will you arrive?</legend>
                    @foreach ($days as $day)
                        @continue(! $day['slots'])
                        {{-- Without JS every day's times show, each under its day's name --}}
                        <div @class(['bk-times', 'is-active' => $selected === $day['date']]) data-times-for="{{ $day['date'] }}">
                            <p class="bk-times__day">{{ $day['label'] }}</p>
                            <div class="bk-times__grid">
                                @foreach ($day['slots'] as $slot)
                                    <label class="bk-time">
                                        <input type="radio" name="time" value="{{ $slot }}"
                                               @checked($selected === $day['date'] && $pickTime === $slot) required>
                                        <span>{{ $slot }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    <p class="bk-step__note">This is your entry time. Once you're in, stay as long as you like. Most visits take {{ $visit['duration'] }}.</p>
                    @error('time')<p class="bk-step__error">{{ $message }}</p>@enderror
                    <div class="bk-nav">
                        <button type="button" class="bk-nav__back" data-prev>← Back</button>
                        <button type="button" class="pill pill--amber pill--lg" data-next><span>Continue</span>{!! $arrow !!}</button>
                    </div>
                </fieldset>

                {{-- 03 — Guests --}}
                <fieldset class="bk-step" data-step-panel>
                    <legend class="bk-step__title" tabindex="-1">How many guests?</legend>
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
                    <legend class="bk-step__title" tabindex="-1">How do we reach you?</legend>
                    <div class="bk-fields">
                        <label class="bk-field">
                            <span class="bk-field__label">Full name</span>
                            <input type="text" name="name" value="{{ old('name', request('name')) }}" autocomplete="name" maxlength="80" required>
                        </label>
                        <label class="bk-field">
                            <span class="bk-field__label">Phone <em>We'll call or WhatsApp you to confirm</em></span>
                            <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" inputmode="tel" maxlength="20" placeholder="077 123 4567" required>
                        </label>
                        <label class="bk-field">
                            <span class="bk-field__label">Email <em>Optional</em></span>
                            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="120">
                        </label>
                        <label class="bk-field">
                            <span class="bk-field__label">Anything we should know? <em>Optional: a birthday, a surprise</em></span>
                            <textarea name="note" rows="2" maxlength="300">{{ old('note') }}</textarea>
                        </label>
                    </div>
                    @foreach (['name', 'phone', 'email', 'send'] as $field)
                        @error($field)<p class="bk-step__error">{{ $message }}</p>@enderror
                    @endforeach
                    <div class="bk-nav">
                        <button type="button" class="bk-nav__back" data-prev>← Back</button>
                        <button type="submit" class="pill pill--amber pill--xl"><span>Send booking request</span>{!! $arrow !!}</button>
                    </div>
                    <p class="bk-step__note">Nothing to pay now. Your request goes to our bookings team, and we'll call or WhatsApp you to confirm.</p>
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
                    <div><dt>Entry</dt><dd data-sum="time">{{ $pickTime ?: 'Not picked' }}</dd></div>
                    <div><dt>Guests</dt><dd data-sum="people">{{ $people }}</dd></div>
                    <div><dt>Name</dt><dd data-sum="name">{{ old('name', request('name')) ?: 'Not given' }}</dd></div>
                </dl>

                <p class="bk-pass__total">
                    <span>Total</span>
                    <span class="bk-pass__amount" data-sum="total">{{ $money($price * $people) }}</span>
                </p>

                <dl class="bk-pass__info">
                    <div><dt>Where</dt><dd>{{ $location['address'] }}</dd></div>
                    <div><dt>Today</dt><dd>{{ $status['label'] }}&nbsp;· {{ $status['detail'] }}</dd></div>
                    <div><dt>Call</dt><dd><a href="tel:{{ $tel }}">{{ $phone }}</a></dd></div>
                </dl>
            </aside>

        </form>
        @endif
    </div>
</section>
@endsection
