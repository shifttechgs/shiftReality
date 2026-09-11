# ShiftReality — Project Memory

This file records non-obvious decisions, resolved ambiguities, and context that would be lost between sessions.

---

## Project Genesis

- **Date started:** September 2026
- **Working directory:** `C:\WCBS_Projects\Experiments\Claude\shiftReality`
- **GitHub repo:** https://github.com/shifttechgs/shiftReality.git

## Technology Decisions

### Laravel 13 (not 11 or 12)
Using `laravel/laravel v13.10.1` with `laravel/framework v13.31.0`. Scaffolded fresh — no starter kit (Breeze, Jetstream, etc.) to keep the frontend clean and unopinionated.

### Tailwind CSS v4 (not v3)
The project uses `@tailwindcss/vite` plugin (v4 integration). Key differences from v3:
- Design tokens go in `@theme {}` block (not `tailwind.config.js`)
- `@source` directives instead of `content` array in config
- CSS variables defined in `@theme` automatically become Tailwind utilities
- No `tailwind.config.js` needed

### Fonts via Bunny Fonts (not Google Fonts)
Direct `<link>` to `fonts.bunny.net` rather than the `bunny()` vite helper (removed from vite.config.js). This gives full control over preconnect, display=swap, and weight selection.
- **Display:** Syne (600, 700, 800) — geometric, editorial, experimental
- **Body:** DM Sans (300, 400, 500) — clean, readable, neutral

### GSAP for hero sequence only
GSAP is used only where CSS transitions can't achieve the required control:
- Hero text reveal sequence (staggered, skewed, sequenced)
- Magnetic button elastic snap-back
- Cursor position updates (via RAF, not GSAP)

Everything else uses CSS transitions or IntersectionObserver.

### No WebGL
The hero canvas uses the native Canvas 2D API for the perspective grid. Kept lightweight — no Three.js, no WebGL. Achieves the immersive effect without the performance cost.

## Design Decisions

### Colour palette — warm amber, not neon
The accent colour `#E4B84C` (warm amber) was chosen specifically to avoid:
- Generic neon/electric colours common in entertainment brands
- The "AI aesthetic" (purple/blue gradients)
The amber creates warmth and a "revelation/lightbulb" association — directly resonant with optical illusions.

### Typography as visual element
The "Who Is It For" section deliberately avoids icon grids. Audience labels are displayed at wildly different sizes to create a typographic composition. This is a design signature, not an oversight.

### CSS optical illusions in shift cards
Each shift card (Perspective, Gravity, Scale, Perception) contains a pure CSS demonstration of the relevant optical illusion. These are `aria-hidden="true"` decorative elements — they don't replace content but reinforce the concept visually.

### Hero headline — "ARE LYING" in amber, aligned right
The second headline line ("ARE LYING") is intentionally right-aligned and amber. This creates a visual tension/asymmetry that subtly mirrors the optical illusion concept in the typography itself.

## Content Architecture Decision

Content is centralised in `config/shiftreality.php` and passed through `HomeController`. This will make CMS integration easier later — the view already consumes arrays, so a CMS can just replace those arrays.

## What Is Intentionally Missing

- Database / migrations — not needed at this stage
- Authentication — not needed at this stage
- Booking system — placeholder `/book` route only
- Admin dashboard — not needed at this stage
- Real photography — placeholders in Section 04 (media-block elements)
- Real OG images — `public/images/og-default.jpg` does not exist yet
- Brand icons — `public/icons/` does not exist yet

## Ambiguities Resolved

| Question | Decision |
|---|---|
| Booking integration | Placeholder for now; will be separate feature phase |
| Full address | Not yet confirmed — left as TBC in config |
| Ticket pricing | Not yet confirmed — left as TBC in config |
| Social media handles | Not yet confirmed — left as null in config |
| Domain | Assumed `shiftreality.co.zw` in robots.txt and structured data |
