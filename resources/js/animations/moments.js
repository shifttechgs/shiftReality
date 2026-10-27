import { prefersReducedMotion } from '../utils/motion.js';

// Each step out from the middle, after Artora: how far across (in card
// widths), how big, how far back. Three or more steps out is hidden.
const STEPS = [
    { x: 0,     s: 1,    z: 3, o: 1 },
    { x: 0.614, s: 0.75, z: 2, o: 1 },
    { x: 1.022, s: 0.55, z: 1, o: 1 },
    { x: 1.3,   s: 0.4,  z: 0, o: 0 },
];

/**
 * Visitor moments carousel, after Artora's "Permanent Collections": the
 * current photo big in the middle, its neighbours smaller and behind it on
 * each side. Prev / next, a click on a side photo, a swipe or the arrow keys
 * move it one step; the caption follows. It never moves on its own.
 * Reduced motion: the steps jump instead of sliding.
 */
export function initMoments() {
    const root = document.querySelector('[data-moments]');
    if (!root) return;

    const slides = [...root.querySelectorAll('.moments__slide')];
    const meta   = root.querySelector('[data-moments-meta]');
    const title  = root.querySelector('[data-moments-title]');
    const n = slides.length;
    if (n < 2) return;

    if (prefersReducedMotion()) root.classList.add('is-still');

    let current = 0;

    const render = () => {
        slides.forEach((slide, k) => {
            // Signed distance from the current slide, the short way round
            let rel = (k - current) % n;
            if (rel > n / 2) rel -= n;
            if (rel < -Math.floor((n - 1) / 2)) rel += n;
            const step = STEPS[Math.min(Math.abs(rel), STEPS.length - 1)];
            slide.style.setProperty('--x', String(Math.sign(rel) * step.x));
            slide.style.setProperty('--s', String(step.s));
            slide.style.setProperty('--o', String(step.o));
            slide.style.zIndex = String(step.z);
            slide.dataset.rel = String(rel);
            slide.classList.toggle('is-current', rel === 0);
        });
        const slide = slides[current];
        meta.textContent  = slide.dataset.meta;
        title.textContent = slide.dataset.title;
    };

    const go = (d) => { current = (current + d + n) % n; render(); };

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

    render();
}
