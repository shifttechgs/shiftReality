import { prefersReducedMotion } from '../utils/motion.js';

// Each step out from the middle, after Artora: how far across (in card
// widths), how big, how far back. Three or more steps out is hidden.
const STEPS = [
    { x: 0,     s: 1,    o: 1 },
    { x: 0.614, s: 0.75, o: 1 },
    { x: 1.022, s: 0.55, o: 1 },
    { x: 1.3,   s: 0.4,  o: 0 },
];
const FOLLOW   = 0.11;    // how fast a card catches up with its place, per frame…
const LAG      = 0.18;    // …each step out from the middle that much slower, so they settle one after another
const FAN_IN   = [0.95, 0.4];   // the deck fans out as its top goes from 95% to 40% of the screen
const FOLDED   = 0.88;    // folded, the side photos sit behind the middle one at this size
const COUNTER  = 0.14;    // a photo slides inside its card against the card's move (share of the card per step)
const DRIFT    = 0.05;    // …and drifts up inside it as the section scrolls past (share of the card)
const BLEED    = 1.14;    // the photo is this much bigger than its card, so neither move shows an edge
const KB       = [0.05, 9000];  // the middle photo slowly eases in this much, over this long (ms)

const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
const ease  = (t) => 1 - Math.pow(1 - t, 3);

/**
 * Visitor moments carousel, after Artora's "Permanent Collections": the
 * current photo big in the middle, its neighbours smaller and behind it on
 * each side. Prev / next, a click on a side photo, a swipe or the arrow keys
 * move it one step; it never moves on its own.
 * In motion: scrolling to it fans the deck out from a pile (and folds it
 * back going up), each photo drifting inside its card for depth; a step
 * glides, the cards settling one after another, each photo sliding against
 * its card's move like a view through a moving window; the caption rolls
 * over; and the middle photo slowly eases in while it rests.
 * Reduced motion: the steps jump, nothing drifts.
 */
export function initMoments() {
    const root = document.querySelector('[data-moments]');
    if (!root) return;

    const slides = [...root.querySelectorAll('.moments__slide')];
    const n = slides.length;
    if (n < 2) return;

    const section = root.closest('section') ?? root;
    const reduced = prefersReducedMotion();
    if (reduced) root.classList.add('is-still');

    // Each card: where it's going (a signed step from the middle) and where it's drawn
    const cards = slides.map((el) => ({ el, img: el.querySelector('img'), to: 0, at: 0 }));
    let current = 0, since = performance.now();

    const relOf = (k) => {
        let rel = (k - current) % n;
        if (rel > n / 2) rel -= n;
        if (rel < -Math.floor((n - 1) / 2)) rel += n;
        return rel;
    };

    // The look at a (fractional) step out, between the STEPS
    const look = (r) => {
        const a = Math.min(Math.abs(r), STEPS.length - 1);
        const i = Math.min(Math.floor(a), STEPS.length - 2), t = a - i;
        const A = STEPS[i], B = STEPS[i + 1];
        return {
            x: Math.sign(r) * (A.x + (B.x - A.x) * t),
            s: A.s + (B.s - A.s) * t,
            o: A.o + (B.o - A.o) * t,
        };
    };

    // The caption rolls over: the old line up and out, the new one up from below
    const roll = (box, text) => {
        if (reduced) { box.querySelector('.moments__line').textContent = text; return; }
        const line = document.createElement('span');
        line.className = 'moments__line is-below';
        line.textContent = text;
        box.querySelectorAll('.moments__line:not(.is-above)').forEach((old) => {
            old.classList.add('is-above');
            old.setAttribute('aria-hidden', 'true');
            setTimeout(() => old.remove(), 700);
        });
        box.append(line);
        void line.offsetWidth;
        line.classList.remove('is-below');
    };
    const metaBox  = root.querySelector('[data-moments-meta]').parentElement;
    const titleBox = root.querySelector('[data-moments-title]');
    [metaBox, titleBox].forEach((box) => {
        const first = box.querySelector('[data-moments-meta]') ?? document.createElement('span');
        if (!first.parentElement) { first.textContent = box.textContent.trim(); box.textContent = ''; box.append(first); }
        first.classList.add('moments__line');
    });

    const place = (snap) => {
        cards.forEach((c, k) => {
            const rel = relOf(k);
            // Going round the back (hidden), it jumps rather than crossing the deck
            if (snap || Math.abs(rel - c.at) > n / 2) c.at = rel;
            c.to = rel;
            c.el.dataset.rel = String(rel);
            c.el.classList.toggle('is-current', rel === 0);
        });
    };

    const go = (d) => {
        current = (current + d + n) % n;
        since = performance.now();
        place(reduced);
        const slide = slides[current];
        roll(metaBox, slide.dataset.meta);
        roll(titleBox, slide.dataset.title);
        wake();
    };

    // Every frame while it's on screen: the deck's fan from the scroll, then
    // each card catching up with its place
    let fan = reduced ? 1 : 0, raf = 0, seen = false;
    const draw = (now) => {
        raf = 0;
        const r = section.getBoundingClientRect(), vh = window.innerHeight;
        const fanTo = reduced ? 1 : ease(clamp((vh * FAN_IN[0] - r.top) / (vh * (FAN_IN[0] - FAN_IN[1])), 0, 1));
        fan += (fanTo - fan) * 0.12;
        if (Math.abs(fanTo - fan) < 0.001) fan = fanTo;
        const pass = clamp((vh - r.top) / (vh + r.height), 0, 1) - 0.5;   // -0.5 → 0.5 as it scrolls past
        const kb = reduced ? 0 : KB[0] * ease(clamp((now - since) / KB[1], 0, 1));

        let moving = fan !== fanTo;
        cards.forEach((c) => {
            const k = FOLLOW / (1 + LAG * Math.abs(c.to));
            c.at += (c.to - c.at) * (reduced ? 1 : k);
            if (Math.abs(c.to - c.at) < 0.0005) c.at = c.to;
            else moving = true;

            const L = look(c.at);
            const s = c.to ? L.s + (Math.min(L.s, FOLDED) - L.s) * (1 - fan) : L.s;
            c.el.style.setProperty('--x', (L.x * fan).toFixed(4));
            c.el.style.setProperty('--s', s.toFixed(4));
            c.el.style.setProperty('--o', (c.to === 0 ? 1 : L.o * (0.35 + 0.65 * fan)).toFixed(3));
            c.el.style.zIndex = String(10 - Math.round(Math.abs(c.at) * 2));

            if (!c.img || reduced) return;
            const slide = -(c.at - c.to) * COUNTER * 100;                  // against the move
            const drift = -pass * DRIFT * (1 + 0.5 * Math.abs(c.to)) * 100; // the sides drift more
            const z = BLEED + (c.to === 0 ? kb : 0);
            c.img.style.transform = `translate(${slide.toFixed(2)}%, ${drift.toFixed(2)}%) scale(${z.toFixed(4)})`;
        });
        // In motion it follows the scroll every frame while on screen; still, only while a step lands
        if (seen && (!reduced || moving)) wake();
    };
    const wake = () => { if (!raf) raf = requestAnimationFrame(draw); };

    new IntersectionObserver(([e]) => { seen = e.isIntersecting; if (seen) wake(); }, { rootMargin: '10% 0px' })
        .observe(section);

    root.querySelector('[data-moments-prev]').addEventListener('click', () => go(-1));
    root.querySelector('[data-moments-next]').addEventListener('click', () => go(1));

    // A side photo brings itself to the middle
    slides.forEach((slide) => slide.addEventListener('click', () => {
        const rel = Number(slide.dataset.rel);
        if (rel) go(rel);
    }));

    root.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft')  { e.preventDefault(); go(-1); }
        if (e.key === 'ArrowRight') { e.preventDefault(); go(1); }
    });

    // Swipe on touch
    let x0 = null;
    const stage = root.querySelector('.moments__stage');
    stage.addEventListener('pointerdown', (e) => { if (e.pointerType !== 'mouse') x0 = e.clientX; });
    stage.addEventListener('pointerup', (e) => {
        if (x0 === null) return;
        const dx = e.clientX - x0;
        x0 = null;
        if (Math.abs(dx) > 40) go(dx < 0 ? 1 : -1);
    });

    place(true);
    draw(performance.now());
}
