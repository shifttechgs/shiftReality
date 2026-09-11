# ShiftReality — Developer Instructions

## Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.4) |
| Templating | Blade |
| Bundler | Vite 8 |
| CSS | Tailwind CSS 4 + custom CSS layers |
| JS | Vanilla JS (ES modules) + GSAP 3 |
| Fonts | Bunny Fonts (fonts.bunny.net) |

## Project Structure

```
app/
  Http/
    Controllers/
      HomeController.php      — Homepage controller (passes content config to view)

config/
  shiftreality.php            — ALL site content: shifts, testimonials, hours, FAQs, etc.

docs/
  business.md                 — Business context
  design.md                   — Design system documentation
  instructions.md             — This file
  memory.md                   — Persistent project memory
  seo.md                      — SEO strategy and structure
  security.md                 — Security policies and checklist

public/
  robots.txt                  — Search engine directives
  site.webmanifest            — PWA manifest
  images/                     — [Insert real photography here]
  icons/                      — [Insert brand icons here]

resources/
  css/
    app.css                   — Design system: @theme tokens, all component CSS

  js/
    app.js                    — Entry point — boots all modules
    utils/
      motion.js               — prefersReducedMotion(), isTouchDevice(), lerp(), clamp()
    animations/
      hero.js                 — Canvas perspective grid + GSAP hero timeline
      magnetic.js             — Magnetic button effect (GSAP)
      reveals.js              — Scroll reveal (IntersectionObserver)
      scroll.js               — Nav scroll behaviour + parallax

  views/
    layouts/
      app.blade.php           — Master layout (meta, fonts, structured data slots)
    components/
      nav.blade.php           — Navigation + full-screen overlay menu
      footer.blade.php        — Footer
    pages/
      home.blade.php          — Homepage (all 8 sections)
      placeholder.blade.php   — Coming-soon stub for /experience, /visit, /groups, /faq, /book

routes/
  web.php                     — All routes
```

## Running Locally

```bash
# Start PHP development server
php artisan serve

# Start Vite (in a separate terminal)
npm run dev
```

Visit `http://localhost:8000`.

## Adding New Content

All content is driven from `config/shiftreality.php`. To change:

- **Shift cards** → edit the `shifts` array
- **Testimonials** → edit the `testimonials` array
- **Opening hours** → edit the `hours` array
- **FAQ** → edit the `faq` array
- **Audiences** → edit the `audiences` array
- **Visit info** → edit `visit`, `location`, `contact` keys

The `HomeController` passes these arrays to `pages/home.blade.php`.

## Adding Real Photography

Photography placeholders are in `home.blade.php` under Section 04 — The Experience.

To replace:
1. Place images in `public/images/`
2. In `home.blade.php`, find the `media-block--primary` and `media-block--secondary` divs
3. Uncomment the `<img>` tag inside each and update `src` and `alt` attributes
4. Remove the `.media-block__placeholder` div

For best performance:
- Use WebP format (with JPEG fallback via `<picture>`)
- Provide responsive sizes with `srcset`
- Use `loading="lazy"` on all non-hero images
- Aim for < 200KB per image after compression

## Adding Routes

Routes are in `routes/web.php`. The pattern is:

```php
Route::get('/new-page', fn () => view('pages.new-page', [
    'data' => config('shiftreality.something'),
]))->name('new-page');
```

## Adding Blade Components

Create in `resources/views/components/`. Include with:
```blade
@include('components.my-component')
```

Or as an anonymous component:
```blade
<x-my-component :prop="$value" />
```

## Adding New Sections

1. Add the section markup to `pages/home.blade.php`
2. Add the CSS in `app.css` (use a BEM-style class with a distinct section prefix)
3. Add content data to `config/shiftreality.php` and pass it through `HomeController`
4. Use `data-reveal` on elements you want to animate in on scroll

## Animation Guidelines

- **Use GSAP only** where CSS transitions can't achieve the effect
- **Always check `prefersReducedMotion()`** before running non-essential animations
- **Magnetic buttons**: add `data-magnetic` attribute to any button/link
- **Scroll reveals**: add `data-reveal` (or `data-reveal="left|right|scale"`) to elements
- **Delay staggering**: add `data-reveal-delay="1"` through `5` (each adds 80ms)

## Environment Variables

Key variables in `.env`:

```
APP_NAME=ShiftReality
APP_URL=https://shiftreality.co.zw
APP_ENV=production
APP_DEBUG=false
```

Never commit `.env` to version control.

## Building for Production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Next Development Phases

1. **Photography integration** — replace all media block placeholders
2. **Booking system** — implement ticket purchasing
3. **Admin dashboard** — manage bookings, content, pricing
4. **CMS integration** — make content editable without code changes
5. **Email automation** — booking confirmations, reminders
6. **Analytics** — integrate GTM, GA4, Meta Pixel
7. **Sitemap** — auto-generate via `spatie/laravel-sitemap`
8. **i18n** — multi-language if needed for tourist audience
