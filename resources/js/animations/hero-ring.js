import { prefersReducedMotion, clamp } from '../utils/motion.js';

const PERSPECTIVE = 1000;   // px — matches .wh-hero__ring's perspective
const FRONT_SCALE = 0.92;   // the card straight ahead sits a little behind the screen
const SPEED       = 90;     // px per second along the ring, at the front (Artora's pace)
const NEAR        = 0.55;   // hide cards closer than this share of the perspective
const ASPECT      = 1.45;   // card height / width (Artora's cards)

/**
 * Home hero ring, after Artora: the cards stand around the inside of a
 * cylinder that turns slowly, centred on the eye line, so the ones at the
 * edges come closer, grow both ways and lean in — rising beside the
 * headline and running off the bottom of the hero. Every card gets one
 * transform per frame; cards that swing round towards the viewer are
 * hidden. The mouse over the ring slows it to a stop; it pauses in a
 * hidden tab and once off screen. Reduced motion: the ring stands still.
 */
export function initHeroRing() {
    const ring  = document.querySelector('[data-hero-ring]');
    const cards = ring ? [...ring.querySelectorAll('.wh-hero__card')] : [];
    if (!cards.length) return;

    const reduced = prefersReducedMotion();
    let radius = 0, step = 0, centre = 0, cardW = 0;

    // Card size from the width (Artora: ~280px on a 1440 screen, the front
    // one drawn at ~258); a tight cylinder, so a card at the screen's edge is
    // drawn about 1.5× the front one, as on Artora
    const measure = () => {
        const w = ring.clientWidth;
        // About a fifth smaller than Artora's, so the big headline leads.
        // Phones: bigger relative to the screen, about two and a half across
        cardW  = w < 700 ? clamp(w * 0.46, 160, 240) : clamp(w * 0.155, 180, 260);
        radius = Math.max(w * 0.4, cardW * 2);
        // Push the cylinder back so the front card lands at FRONT_SCALE
        centre = radius - PERSPECTIVE * (1 / FRONT_SCALE - 1);
        ring.style.setProperty('--card-w', `${cardW}px`);
        ring.style.setProperty('--card-h', `${cardW * ASPECT}px`);
        step = (cardW * 1.1) / radius;
        // The hero's background fades out over the ring (not the words): it
        // needs the ring's height
        ring.closest('.wh-hero')?.style.setProperty('--ring-h', `${ring.offsetHeight}px`);
    };

    let offset = 0;   // radians turned so far
    const place = () => {
        const span = step * cards.length;
        cards.forEach((card, i) => {
            // Angle from straight ahead, wrapped into -span/2 … span/2
            let a = (i * step - offset) % span;
            if (a < 0) a += span;
            a -= span / 2;
            const z = centre - radius * Math.cos(a);
            card.style.visibility = z > PERSPECTIVE * NEAR ? 'hidden' : '';
            card.style.transform =
                `translateZ(${centre.toFixed(1)}px) rotateY(${(-a).toFixed(4)}rad) translateZ(${(-radius).toFixed(1)}px)`;
        });
    };

    measure();
    place();
    ring.classList.add('is-placed');
    window.addEventListener('resize', () => { measure(); place(); }, { passive: true });
    if (reduced) return;

    // Velocity eases to 0 under the mouse and back up after
    let velocity = 1, target = 1, last = 0, raf = 0;
    let away = false;

    const tick = (now) => {
        const dt = last ? Math.min(0.05, (now - last) / 1000) : 0;
        last = now;
        velocity += (target - velocity) * Math.min(1, dt * 4);
        offset += (SPEED / radius) * velocity * dt;
        place();
        raf = requestAnimationFrame(tick);
    };

    const run  = () => { if (!raf && !away && !document.hidden) { last = 0; raf = requestAnimationFrame(tick); } };
    const halt = () => { cancelAnimationFrame(raf); raf = 0; };

    ring.addEventListener('pointerenter', (e) => { if (e.pointerType === 'mouse') target = 0; });
    ring.addEventListener('pointerleave', (e) => { if (e.pointerType === 'mouse') target = 1; });
    document.addEventListener('visibilitychange', () => (document.hidden ? halt() : run()));
    new IntersectionObserver(([entry]) => {
        away = !entry.isIntersecting;
        away ? halt() : run();
    }).observe(ring);

    run();
}
