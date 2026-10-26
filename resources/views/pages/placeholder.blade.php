@extends('layouts.app')

@section('title', ($title ?? 'Coming Soon') . ' — ' . config('shiftreality.name'))

{{-- The banner is deep navy, so the nav rests in its light variant --}}
@section('body_class', 'has-hero-photo')

@section('content')
@include('partials.page-banner', [
    'title' => $title ?? 'Coming Soon',
    'intro' => $message ?? 'This page is being built. Check back soon.',
])

<section class="placeholder-page">
    <div class="container placeholder-page__inner">
        <p class="placeholder-page__message">This page is being built. In the meantime, everything you need is on the homepage.</p>
        <a href="{{ route('home') }}" class="pill pill--amber pill--lg">
            <span>Back to home</span>
            <svg class="pill__arr" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>
@endsection
