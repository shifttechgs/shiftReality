{{-- Inner-page banner: a full-bleed band of solid deep navy, the footer's colour, so
     every inner page opens and closes on the same surface. The nav rests on it in its
     light variant — pages using this set body_class 'has-hero-photo'.
     @include('partials.page-banner', [
         'crumb' => 'Pricing',               // label after "Home /" (defaults to the title)
         'title' => 'Heading',               // the page's h1; a newline in it sets a line break
         'intro' => 'One or two lines.',     // optional
     ]) --}}
<section class="page-banner" aria-labelledby="page-banner-title">
    <div class="container page-banner__head">
        <div class="page-banner__main">
            <nav class="page-banner__crumbs" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $crumb ?? $title }}</span>
            </nav>
            <h1 class="page-banner__title" id="page-banner-title" data-split>{!! nl2br(e($title), false) !!}</h1>
        </div>
        @if (!empty($intro))
            <p class="page-banner__intro" data-scrub>{{ $intro }}</p>
        @endif
    </div>
</section>
