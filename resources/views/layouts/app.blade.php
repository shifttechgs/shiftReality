<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- ── Primary Meta ─────────────────────────────────────────────── --}}
    <title>@yield('title', config('shiftreality.name') . ' — ' . config('shiftreality.tagline'))</title>
    <meta name="description" content="@yield('description', config('shiftreality.description'))">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta name="robots" content="@yield('robots', 'index, follow')">

    {{-- ── Open Graph ──────────────────────────────────────────────── --}}
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:title"       content="@yield('og_title', config('shiftreality.name') . ' — ' . config('shiftreality.tagline'))">
    <meta property="og:description" content="@yield('og_description', config('shiftreality.description'))">
    <meta property="og:image"       content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:locale"      content="en_ZW">
    <meta property="og:site_name"   content="{{ config('shiftreality.name') }}">

    {{-- ── Twitter / X ─────────────────────────────────────────────── --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="@yield('og_title', config('shiftreality.name'))">
    <meta name="twitter:description" content="@yield('og_description', config('shiftreality.description'))">
    <meta name="twitter:image"       content="@yield('og_image', asset('images/og-default.jpg'))">

    {{-- Fonts: Helvetica from the visitor's system (Arial where it isn't installed) — nothing to download --}}

    {{-- ── Favicon & Manifest ─────────────────────────────────────── --}}
    {{-- Replace these with actual brand assets when available --}}
    <link rel="icon"             href="{{ asset('favicon.ico') }}"             type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="manifest"         href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color"     content="#080807">

    {{-- ── Structured Data ────────────────────────────────────────── --}}
    @yield('structured_data')

    {{-- ── Vite Assets ────────────────────────────────────────────── --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ── Analytics placeholder ─────────────────────────────────── --}}
    {{-- Insert Google Tag Manager / Meta Pixel snippet here --}}
    {{-- @include('partials.analytics') --}}

    @stack('head')
</head>
<body class="@yield('body_class')">

    {{-- Intro screen (homepage only — pushed via @push('intro') in home.blade.php) --}}
    @stack('intro')

    {{-- Custom cursor (desktop only — hidden via CSS on touch devices) --}}
    <div class="cursor"      aria-hidden="true"></div>
    <div class="cursor-ring" aria-hidden="true"></div>

    {{-- Shooting-star trail behind the mouse, on every page (comet.js) --}}
    <canvas class="comet" data-comet aria-hidden="true"></canvas>

    {{-- Page transition overlay --}}
    <div class="page-transition" aria-hidden="true"></div>

    {{-- ── Top banner (a page can leave it out with @section('no_banner')) ── --}}
    @unless(View::hasSection('no_banner'))
        @include('components.banner')
    @endunless

    {{-- ── Navigation ────────────────────────────────────────────── --}}
    @include('components.nav')

    {{-- ── Page Content ─────────────────────────────────────────── --}}
    <main id="main-content">
        @yield('content')
    </main>

    {{-- ── Footer ─────────────────────────────────────────────────── --}}
    @include('components.footer')

    @stack('scripts')

    <script>
        document.documentElement.classList.remove('no-js');
        document.documentElement.classList.add('js');
    </script>
</body>
</html>
