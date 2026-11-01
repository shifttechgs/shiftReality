@extends('layouts.app')

@section('title', 'FAQ · ' . config('shiftreality.name') . ' · Tickets, booking and visiting')
@section('description', 'Answers about Reality Shift in ' . $location['city'] . ': ticket prices, booking, ages, how long a visit takes, photos and where to find us.')

@push('head')
<script type="application/ld+json">
@php echo json_encode([
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_map(fn ($item) => [
        '@type'          => 'Question',
        'name'           => $item['question'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['answer']],
    ], $faq),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); @endphp
</script>
@endpush

{{-- The banner is deep navy, so the nav rests in its light variant --}}
@section('body_class', 'has-hero-photo')

@php
    $tel   = preg_replace('/\D/', '', $contact['phone']);
    $phone = preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '$1 $2 $3', $tel);
@endphp

@section('content')

@include('partials.page-banner', [
    'crumb' => 'FAQ',
    'title' => "Questions,\nanswered.",
    'intro' => 'What people ask before they visit. If yours isn\'t here, ask us. A real person answers.',
])

<section class="faq-section faq-page" aria-label="Frequently asked questions">
    <div class="container visit-faq">

        <aside class="visit-faq__aside faq-page__aside" data-scrub>
            <p class="pp-label">Still wondering?</p>
            <p class="faq-page__ask">Call, WhatsApp or email us.</p>
            <dl class="ticket__terms faq-page__contact">
                <div class="ticket__term"><dt>Call</dt><dd><a href="tel:{{ $tel }}" class="visit__more">{{ $phone }}</a></dd></div>
                <div class="ticket__term"><dt>WhatsApp</dt><dd><a href="https://wa.me/{{ $contact['whatsapp'] }}" class="visit__more" target="_blank" rel="noopener">Message us →</a></dd></div>
                <div class="ticket__term"><dt>Email</dt><dd><a href="mailto:{{ $contact['email'] }}" class="visit__more">{{ $contact['email'] }}</a></dd></div>
            </dl>
            <span class="visit-faq__links">
                <a href="{{ route('visit') }}" class="visit__more">Plan your visit →</a>
                <a href="{{ route('groups') }}" class="visit__more">Group visits →</a>
            </span>
        </aside>

        <div class="faq faq-page__list" role="list">
            @foreach ($faq as $i => $item)
                <div @class(['faq__item', 'is-open' => $i === 0]) role="listitem" data-scrub>
                    <button class="faq__question" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="faq-a-{{ $i }}" id="faq-q-{{ $i }}">
                        <span class="faq-page__q"><span class="faq-page__num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $item['question'] }}</span>
                        <span class="faq__icon" aria-hidden="true"></span>
                    </button>
                    <div class="faq__answer" id="faq-a-{{ $i }}" role="region" aria-labelledby="faq-q-{{ $i }}">
                        <div class="faq__answer-clip">
                            <p class="faq__answer-inner">{{ $item['answer'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

@include('partials.inner-close')

@endsection
