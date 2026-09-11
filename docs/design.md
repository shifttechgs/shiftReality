# ShiftReality — Design Document

## Design Philosophy

> We are not building a website about ShiftReality. We are building the first ShiftReality experience.

The website must make someone feel "something is different here" before they understand what ShiftReality is. Every design decision should support the transition from curious visitor to booked guest.

## Brand Identity

**Core idea:** Challenge what you see.

**Brand personality:**
- Mysterious
- Premium
- Playful
- Unexpected
- Immersive
- Cinematic
- Confident
- Slightly surreal

**Brand language (use sparingly — coherent voice, not slogan spam):**
- "Your eyes are lying to you." — hero headline
- "Reality isn't always what it looks like." — invitation section
- "Ready to shift your reality?" — final CTA

## Colour System

Defined as CSS custom properties in `resources/css/app.css` under `@theme`.

| Token | Value | Purpose |
|---|---|---|
| `--color-sr-bg` | `#080807` | Page background — warm near-black |
| `--color-sr-surface` | `#0F0F0E` | Card/panel backgrounds |
| `--color-sr-surface-raised` | `#191918` | Elevated surfaces |
| `--color-sr-text` | `#F0EBE1` | Primary text — warm ivory |
| `--color-sr-muted` | `#6E6760` | Secondary text, labels |
| `--color-sr-muted-2` | `#403C38` | Tertiary text, placeholders |
| `--color-sr-wire` | `#222220` | Borders, dividers |
| `--color-sr-accent` | `#E4B84C` | Warm amber — the brand accent |
| `--color-sr-accent-light` | `#EFC85C` | Hover state for accent |

**Rationale:** The warm amber accent creates a sense of warmth, revelation, and "lightbulb moment" — directly resonant with the optical illusion concept. It avoids the clichéd neon/electric palette common in tech/AI brands.

## Typography

| Role | Font | Weights | CSS Variable |
|---|---|---|---|
| Display / Headlines | Syne | 600, 700, 800 | `--font-display` |
| Body / UI | DM Sans | 300, 400, 500 | `--font-sans` |

**Font source:** Bunny Fonts (fonts.bunny.net) — GDPR-friendly alternative to Google Fonts.

**Typography principles:**
- Headlines: large, tight letter-spacing (`-0.03em` to `-0.04em`), uppercase where impactful
- Body: generous line-height (1.65–1.75), comfortable reading weight
- Typography is used as a primary visual element — especially in sections like "Who Is It For"

## Motion System

Defined in `resources/css/app.css` and used consistently across all animations.

| Name | Duration | Use |
|---|---|---|
| Micro | 120ms | Hover state colour changes, focus rings |
| Fast | 260ms | Small UI transitions, button states |
| Standard | 500ms | Content reveals, nav transitions |
| Cinematic | 900ms | Hero sequences, section entrances |
| Dramatic | 1400ms | Full-screen transitions, staggered reveals |

**Easing curves:**
- `--ease-out`: `cubic-bezier(0.16, 1, 0.3, 1)` — snappy deceleration for reveals
- `--ease-in-out`: `cubic-bezier(0.45, 0, 0.55, 1)` — smooth for bidirectional transitions
- `--ease-spring`: `cubic-bezier(0.34, 1.56, 0.64, 1)` — spring overshoot for buttons/magnetic

**Reduced motion:** All animations respect `prefers-reduced-motion: reduce`. Content remains fully accessible with no animation.

## Layout System

- **Container max-width:** 1200px (wide: 1440px, narrow: 800px)
- **Horizontal padding:** `clamp(1.5rem, 5vw, 4rem)`
- **Section vertical padding:** `clamp(5rem, 10vw, 9rem)`
- **Section dividers:** `1px solid var(--color-sr-wire)` at section tops
- **Grid gap (card grids):** `1px` with `background: var(--color-sr-wire)` — creates a "wire" grid aesthetic

## Interaction Design

All interactions are intentional — they serve perception, storytelling, or navigation.

| Interaction | Implementation | File |
|---|---|---|
| Custom cursor (dot + ring) | Vanilla JS, RAF loop | `app.js` |
| Cursor ring expansion on hover | CSS class toggle | `app.css` |
| Hero canvas — perspective grid | Canvas API, cursor-reactive | `animations/hero.js` |
| Hero headline parallax | GSAP, mousemove | `animations/hero.js` |
| Hero text sequence | GSAP timeline | `animations/hero.js` |
| Magnetic buttons | GSAP elastic ease | `animations/magnetic.js` |
| Scroll reveal | IntersectionObserver + CSS | `animations/reveals.js` |
| Nav scroll behaviour | `nav--scrolled` class toggle | `animations/scroll.js` |
| Nav overlay | `clip-path` animation | `app.css` + `app.js` |
| FAQ accordion | `max-height` CSS transition | `app.css` + `app.js` |
| Shift card hover | Background transition | `app.css` |
| Media block hover | Scale transform | `app.css` |

## CSS Optical Illusions

Each "Shift" card contains a pure CSS optical illusion as a decorative demonstration.

| Card | Illusion | Type |
|---|---|---|
| Perspective | Ambiguous wireframe cube | Necker cube — flips between two orientations |
| Gravity | Ball rolling uphill | Motion illusion — contradicts gradient slope |
| Scale | Ponzo illusion | Two identical bars appear different sizes |
| Perception | Checker shadow | Two identical-shade squares appear different |

These are implemented in `app.css` using CSS animations only — no images, no JS.

## Component Architecture

```
resources/views/
├── layouts/
│   └── app.blade.php        — Master layout (HTML, meta, fonts, assets)
├── components/
│   ├── nav.blade.php         — Navigation + overlay menu
│   └── footer.blade.php      — Footer
└── pages/
    ├── home.blade.php         — Homepage (all sections)
    └── placeholder.blade.php  — Coming-soon pages
```

## Awwwards Design Checklist

- [ ] Distinct visual identity? **Yes — warm-black + amber, editorial Syne type**
- [ ] Does something unexpected? **Yes — CSS optical illusions, cursor-reactive hero canvas**
- [ ] Communicates clearly? **Yes — concise copy, clear journey to booking**
- [ ] Usable? **Yes — keyboard nav, focus states, semantic HTML, no mouse-only interactions**
- [ ] Technology enhances experience? **Yes — canvas grid, GSAP sequences, magnetic buttons**
- [ ] Remains fast? **Yes — no WebGL, no heavy libraries beyond GSAP**
- [ ] Accessible? **Yes — WCAG-considered contrast, reduced motion support, semantic HTML**
- [ ] Works everywhere? **Yes — mobile-first, responsive grid, touch-device fallbacks**

## What to Avoid

- Generic SaaS layouts
- Generic startup gradients
- Excessive glassmorphism
- AI-looking blob backgrounds
- Template-looking card grids
- Stock photography heavy layouts
- Excessive animation
- Neon accent colours
