import { prefersReducedMotion, clamp } from '../utils/motion.js';

/**
 * Brand banner, driven by scroll.
 * - Drifts at a gentle base speed in the direction you last scrolled
 *   (down → left, up → right) and speeds up with scroll velocity.
 * - Leans (skewX) with fast scrolls, settling back when you stop.
 * - [data-flip] words turn upside down as they cross the centre of
 *   the screen: a nod to the Upside Down room.
 * Without this script the CSS drift keeps running; reduced motion stays still.
 */
export function initBrandBanner() {
    const band = document.querySelector('[data-brand-banner]');
    if (!band || prefersReducedMotion()) return;

    const track = band.querySelector('.brand-banner__track');
    const set   = band.querySelector('.brand-banner__set');
    const flips = [...band.querySelectorAll('[data-flip]')];
    if (!track || !set) return;

    band.classList.add('is-driven');   // hands the motion from CSS to here

    let loop = set.offsetWidth;        // one set, trailing gap included
    new ResizeObserver(() => { loop = set.offsetWidth; }).observe(set);

    const BASE = 40;                   // px per second at rest
    let x = 0, dir = 1, speed = BASE, skew = 0;
    let lastY = window.scrollY, lastT = performance.now();
    let visible = false;

    new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        if (visible) { lastY = window.scrollY; lastT = performance.now(); }
    }, { rootMargin: '10% 0px' }).observe(band);

    const ease = (t) => t * t * (3 - 2 * t);

    const frame = (now) => {
        requestAnimationFrame(frame);
        if (!visible) return;

        const dt = Math.min(0.05, (now - lastT) / 1000);
        lastT = now;

        // scroll velocity in px/s
        const dy = window.scrollY - lastY;
        lastY = window.scrollY;
        const v = dt ? dy / dt : 0;
        if (Math.abs(dy) > 0.5) dir = Math.sign(dy);

        speed += (BASE + Math.min(Math.abs(v) * 0.45, 1400) - speed) * 0.08;
        skew  += (clamp(-v * 0.004, -7, 7) - skew) * 0.1;

        x -= dir * speed * dt;
        if (loop) x = ((x % loop) - loop) % loop;   // keep within (-loop, 0]

        track.style.transform = `translate3d(${x.toFixed(2)}px, 0, 0) skewX(${skew.toFixed(2)}deg)`;

        // flip near the centre: upright at the edges of the zone, held upside down through the middle
        const mid = window.innerWidth / 2;
        const zone = window.innerWidth * 0.26;
        for (const word of flips) {
            const r = word.getBoundingClientRect();
            const p = clamp((1 - Math.abs(r.left + r.width / 2 - mid) / zone) * 1.7, 0, 1);
            word.style.transform = `rotate(${(ease(p) * 180).toFixed(1)}deg)`;
        }
    };
    requestAnimationFrame(frame);
}
