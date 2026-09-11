# ShiftReality — SEO / AEO / GEO Strategy

## Overview

ShiftReality needs to rank for:
1. Local searches in Zimbabwe (Harare entertainment, things to do Harare)
2. Tourism searches (Zimbabwe attractions, Harare experiences, immersive experience Zimbabwe)
3. Category searches (optical illusion experience, immersive art Zimbabwe)
4. Brand searches (ShiftReality)

## Technical SEO Foundation

### Implemented

- [x] Semantic HTML structure (h1, h2, h3 hierarchy)
- [x] Title tag pattern: `[Page] — [Tagline]`
- [x] Meta description (via `@section('description')`)
- [x] Canonical URLs (via `@section('canonical')`)
- [x] Open Graph tags (og:title, og:description, og:image, og:type, og:locale)
- [x] Twitter Card tags
- [x] robots.txt (allows all, blocks admin paths)
- [x] site.webmanifest (PWA support)
- [x] theme-color meta tag
- [x] Structured data slot (`@stack('head')` + `@push('head')`)
- [x] TouristAttraction schema on homepage
- [x] Semantic landmark roles (main, nav, footer, article)
- [x] aria-label on navigation, sections, interactive elements

### To Implement

- [ ] XML sitemap (`spatie/laravel-sitemap` recommended)
- [ ] Real favicon + apple-touch-icon (brand assets needed)
- [ ] Real OG image (`public/images/og-default.jpg` — 1200x630)
- [ ] Breadcrumb schema (BreadcrumbList) on inner pages
- [ ] FAQPage schema on FAQ page (use existing FAQ content from config)
- [ ] LocalBusiness schema with full address, phone, geo coordinates
- [ ] Image alt text on all photography once images are added

## Structured Data Strategy

### Homepage

```json
{
  "@type": "TouristAttraction",
  "name": "ShiftReality",
  "addressLocality": "Harare",
  "addressCountry": "ZW",
  "openingHoursSpecification": [...]
}
```

### Future Pages

| Page | Schema Type |
|---|---|
| `/faq` | FAQPage |
| `/visit` | LocalBusiness with full address |
| `/experience` | Event (if ticketed events) or Thing |
| `/groups` | Service |

## AEO — AI Search / Answer Engine Optimisation

AI search engines (ChatGPT, Perplexity, Gemini, Claude) extract answers from structured semantic content.

### Target Q&A coverage (built into homepage copy):

| Question | Answered in section |
|---|---|
| What is ShiftReality? | Section 02 — Invitation |
| What experiences does ShiftReality offer? | Section 03 — The Shifts |
| Where is ShiftReality? | Section 07 — Plan Your Visit + structured data |
| How long does it take? | Section 07 — Plan Your Visit |
| Is it suitable for children? | Section 07 — FAQ |
| Can I take photos? | Section 07 — FAQ |
| Who is it for? | Section 06 — Who Is It For? |
| How do I book? | All CTAs + Section 08 — Final CTA |
| Is booking required? | Section 07 — FAQ |

### Content principles for AEO:

- Write naturally — no keyword stuffing
- Use clear question-answer structures (FAQ accordion)
- Ensure answers are complete in 1–3 sentences
- Use semantic HTML (`<article>`, `<section>`, `<h2>`)
- Structured data reinforces what prose says

## GEO — Geographic / Local SEO

### Zimbabwe-focused strategy:

- Primary location: Harare, Zimbabwe
- Locale metadata: `en_ZW` (Open Graph locale)
- Structured data: `addressCountry: "ZW"`, `addressRegion: "Harare Province"`
- Once address is confirmed: add Google Maps embed, full PostalAddress schema
- Align with Google Business Profile (to be set up once address confirmed)

### Local keyword targets:

- "things to do in Harare"
- "Harare entertainment"
- "immersive experience Zimbabwe"
- "optical illusion Zimbabwe"
- "tourist attraction Harare"
- "family activities Harare"
- "unique experiences Zimbabwe"

### Local landing pages (future):

Only create if genuinely useful. Do not create thin pages for SEO sake.
Potential: `/harare` if targeting specific suburb-level searches.

## Content SEO

### Page title patterns:

```
Homepage:    ShiftReality — Your eyes are lying to you.
Experience:  The Experience — ShiftReality
Visit:       Plan Your Visit — ShiftReality | Harare, Zimbabwe
FAQ:         Frequently Asked Questions — ShiftReality
Groups:      Group & Corporate Bookings — ShiftReality
Book:        Book Your Experience — ShiftReality
```

### Meta description pattern:

Concise (under 160 chars), includes primary benefit + location + CTA signal.

Example:
```
ShiftReality is an immersive optical illusion experience in Harare, Zimbabwe. 
Challenge what you see. Book your experience today.
```

## Performance & SEO

Google uses Core Web Vitals as a ranking signal.

Targets:
- LCP: under 2.5s
- FID/INP: under 200ms
- CLS: under 0.1
- Lighthouse SEO: 95+

Key performance decisions:
- No autoplay video
- Lazy loading on all images (once added)
- CSS optical illusions (no image overhead)
- Bunny Fonts with `display=swap`
- Canvas API (not WebGL) for hero
- No unnecessary third-party scripts

## Sitemap (To Implement)

Install: `composer require spatie/laravel-sitemap`

Target URLs:
- `/` (homepage — priority 1.0)
- `/experience` (0.9)
- `/visit` (0.9)
- `/groups` (0.8)
- `/faq` (0.8)
- `/book` (0.7)
