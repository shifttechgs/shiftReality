@extends('layouts.app')

@section('title', 'Group Visits · ' . config('shiftreality.name') . ' · Schools, teams and birthdays')
@section('description', 'Group visits to Reality Shift in ' . config('shiftreality.location.city') . ': school trips, team building and birthday parties. Group rates and priority entry. Send an enquiry and we\'ll come back with a price.')

{{-- The banner is deep navy, so the nav rests in its light variant --}}
@section('body_class', 'has-hero-photo')

@php
    $arrow = '<svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
    $tel   = preg_replace('/\D/', '', $contact['phone']);
    $phone = preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '$1 $2 $3', $tel);
    $today = now(config('shiftreality.timezone'))->toDateString();
    $steps = [
        ['Tell us about your group', 'What it\'s for, roughly how many are coming and the day you have in mind.'],
        ['We send you a price', 'We call or WhatsApp you with your group rate and the times that are free.'],
        ['Agree the day', 'Once the day and the price suit you, your time is held for the whole group.'],
    ];
@endphp

@section('content')

@include('partials.page-banner', [
    'crumb' => 'Group Visits',
    'title' => "Bring the\nwhole group.",
    'intro' => 'Schools, teams and birthdays get their own rates and priority entry. Tell us about your group and we\'ll come back with a price.',
])

{{-- ══════════════════════════════════════════════════════════
     01 — WHO IT'S FOR
     Our photo on one side, the occasions on the other.
     ══════════════════════════════════════════════════════════ --}}
<section class="pp-groups gp-intro" aria-labelledby="gp-intro-heading">
    <div class="container pp-groups__grid">

        <figure class="pp-photo pp-groups__media" data-reveal>
            <img src="{{ asset($groups['image']) }}" srcset="{{ asset($groups['image']) }} 800w, {{ asset(str_replace('-800.', '-1600.', $groups['image'])) }} 1600w"
                 sizes="(max-width: 900px) 100vw, 50vw"
                 alt="{{ $groups['image_alt'] }}" class="pp-photo__img" decoding="async">
        </figure>

        <div class="pp-groups__content">
            <p class="pp-label">Groups</p>
            <h2 class="pp-groups__title" id="gp-intro-heading" data-split>Made for a crowd.</h2>
            <p class="pp-groups__sub" data-scrub>
                Every room is better with people to fool. More than {{ $minGroup - 1 }} of you?
                Book as a group and we'll set you up.
            </p>

            <p class="pp-groups__price" data-scrub>
                <span class="pp-groups__figure">On request</span>
                <span class="pp-groups__unit">group rates</span>
            </p>

            <ul class="groups__occasions pp-groups__occasions">
                @foreach ($groups['occasions'] as $occasion)
                    <li class="groups__occasion" data-scrub>
                        <h3 class="groups__occasion-title">{{ $occasion['title'] }}</h3>
                        <p class="groups__occasion-line">{{ $occasion['line'] }}</p>
                    </li>
                @endforeach
            </ul>

            <div class="pp-groups__actions" data-scrub>
                <a href="#enquire" class="pill pill--amber pill--lg"><span>Send an enquiry</span>{!! $arrow !!}</a>
                <a href="https://wa.me/{{ $contact['whatsapp'] }}?text={{ rawurlencode('Hi Reality Shift! I\'d like to enquire about a group booking.') }}" class="visit__more" target="_blank" rel="noopener">Or ask on WhatsApp →</a>
            </div>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     02 — HOW IT WORKS (deep navy)
     ══════════════════════════════════════════════════════════ --}}
<section class="pp-when" aria-labelledby="gp-how-heading">
    <div class="container pp-when__grid">
        <div class="pp-when__copy">
            <p class="pp-label pp-label--dark">How it works</p>
            <h2 class="pp-when__title" id="gp-how-heading" data-split>How a group<br>visit works.</h2>
            <p class="pp-when__sub" data-scrub>
                Your enquiry goes straight to our bookings team. Nothing is booked
                or paid until you've agreed a day and a price with us.
            </p>
        </div>

        <ol class="gp-steps">
            @foreach ($steps as $i => [$step, $line])
                <li class="gp-step" data-scrub>
                    <span class="gp-step__num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="gp-step__title">{{ $step }}</h3>
                    <p class="gp-step__line">{{ $line }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     03 — THE ENQUIRY
     Emailed to the bookings inbox (GroupController@send). After
     sending, the thanks replaces the form.
     ══════════════════════════════════════════════════════════ --}}
<section class="gp-enquire" id="enquire" aria-labelledby="gp-form-heading">
    <div class="container gp-enquire__grid">

        <div class="gp-enquire__aside">
            <p class="pp-label">Enquire</p>
            <h2 class="pp-value__title" id="gp-form-heading" data-split>Tell us about<br>your group.</h2>
            <p class="pp-value__sub gp-enquire__sub" data-scrub>
                A few details and we'll come back to you with a price.
                Rather talk? Call or email us.
            </p>
            <dl class="ticket__terms gp-enquire__contact" data-scrub>
                <div class="ticket__term"><dt>Call</dt><dd><a href="tel:{{ $tel }}" class="visit__more">{{ $phone }}</a></dd></div>
                <div class="ticket__term"><dt>Email</dt><dd><a href="mailto:{{ $contact['email'] }}" class="visit__more">{{ $contact['email'] }}</a></dd></div>
            </dl>
        </div>

        @if ($sent)
            <div class="gp-done" role="status">
                <p class="bk-done__label">Enquiry sent</p>
                <p class="bk-done__title">Thanks, {{ \Illuminate\Support\Str::before(trim($sent['name']), ' ') }}. It's with our team.</p>
                <p class="bk-done__text">
                    We'll call or WhatsApp you on {{ $sent['phone'] }} with a price for
                    {{ $sent['size'] }} people{{ $sent['day'] ? ' on ' . $sent['day'] : '' }}.
                </p>
                <div class="bk-done__actions">
                    <a href="{{ route('home') }}" class="pill pill--amber pill--lg"><span>Back to home</span>{!! $arrow !!}</a>
                    <a href="{{ route('experience') }}" class="visit__more">See the rooms →</a>
                </div>
            </div>
        @else
            <form class="gp-form" method="POST" action="{{ route('groups.send') }}#enquire" novalidate>
                @csrf

                <fieldset class="gp-form__occasion">
                    <legend class="bk-field__label">What's it for?</legend>
                    <div class="gp-chips">
                        @foreach ($occasions as $occasion)
                            <label class="gp-chip">
                                <input type="radio" name="occasion" value="{{ $occasion }}" @checked(old('occasion', $occasions[0]) === $occasion) required>
                                <span>{{ $occasion }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div class="bk-fields gp-form__fields">
                    <label class="bk-field">
                        <span class="bk-field__label">How many people?</span>
                        <input type="number" name="size" value="{{ old('size') }}" min="2" max="1000" inputmode="numeric" required>
                    </label>
                    <label class="bk-field">
                        <span class="bk-field__label">Preferred date <em>Optional</em></span>
                        <input type="date" name="date" value="{{ old('date') }}" min="{{ $today }}">
                    </label>
                    <label class="bk-field">
                        <span class="bk-field__label">Your name</span>
                        <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="80" required>
                    </label>
                    <label class="bk-field">
                        <span class="bk-field__label">School or company <em>Optional</em></span>
                        <input type="text" name="organisation" value="{{ old('organisation') }}" autocomplete="organization" maxlength="120">
                    </label>
                    <label class="bk-field">
                        <span class="bk-field__label">Phone</span>
                        <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" inputmode="tel" maxlength="20" placeholder="077 123 4567" required>
                    </label>
                    <label class="bk-field">
                        <span class="bk-field__label">Email <em>Optional</em></span>
                        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="120">
                    </label>
                    <label class="bk-field gp-form__wide">
                        <span class="bk-field__label">Anything else? <em>Optional: ages, timing, a cake</em></span>
                        <textarea name="note" rows="3" maxlength="600">{{ old('note') }}</textarea>
                    </label>
                </div>

                @if ($errors->any())
                    <div class="gp-form__errors" role="alert">
                        @foreach ($errors->all() as $error)
                            <p class="bk-step__error">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="gp-form__send">
                    <button type="submit" class="pill pill--amber pill--xl"><span>Send enquiry</span>{!! $arrow !!}</button>
                    <p class="bk-step__note">Goes straight to our bookings team.</p>
                </div>
            </form>
        @endif

    </div>
</section>

@include('partials.inner-close', [
    'title' => '<span class="nowrap">Just a few of you?</span><br><span class="nowrap">Book in a minute.</span>',
    'sub'   => 'Up to ' . ($minGroup - 1) . ' people can book straight away. Pick a day, a time and how many are coming.',
])

@endsection
