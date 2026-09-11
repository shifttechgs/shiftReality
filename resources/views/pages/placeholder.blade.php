@extends('layouts.app')

@section('title', ($title ?? 'Coming Soon') . ' — ' . config('shiftreality.name'))

@section('content')
<section class="placeholder-page">
    <p class="placeholder-page__label">{{ config('shiftreality.name') }}</p>
    <h1 class="placeholder-page__title">{{ $title ?? 'Coming Soon' }}</h1>
    <p class="placeholder-page__message">{{ $message ?? 'This page is being built. Check back soon.' }}</p>
    <a href="{{ route('home') }}" class="btn btn-outline btn-arrow">
        Back to home
        <span class="arrow" aria-hidden="true">→</span>
    </a>
</section>
@endsection
