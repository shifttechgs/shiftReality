import { prefersReducedMotion, clamp } from '../utils/motion.js';

/**
 * Scroll-scrubbed reveals for [data-scrub]: each element rises and fades in
 * by its own position on screen — from the bottom edge until it's ~40% of the
 * way up — so the motion follows the scroll (slow scroll, slow reveal; scroll
 * back, it reverses). Unlike [data-reveal], nothing plays on its own.
 * Reduced motion / no JS: everything simply shows.
 */
export function initScrubReveal() {
    const items = [...document.querySelectorAll('[data-scrub]')].map((el) => ({ el, t: 0, drawn: false, visible: false }));
    if (!items.length || prefersReducedMotion()) return;

    document.documentElement.classList.add('has-scrub');

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            const it = items.find((x) => x.el === entry.target);
            if (it) it.visible = entry.isIntersecting;
        });
    }, { rootMargin: '20% 0px' });
    items.forEach((it) => io.observe(it.el));

    const ease = (t) => 1 - Math.pow(1 - t, 3);

    const frame = () => {
        requestAnimationFrame(frame);
        const vh = window.innerHeight;

        for (const it of items) {
            const top = it.el.getBoundingClientRect().top;
            const target = ease(clamp((vh * 0.98 - top) / (vh * 0.38), 0, 1));
            // off-screen (after a fast scroll or a jump): snap to the end state for where
            // it is — shown above the viewport, hidden below — so nothing is left half-way
            // and nothing below re-plays a fade-out when you scroll down to it again
            const t = it.visible ? it.t + (target - it.t) * 0.18 : (target > 0.5 ? 1 : 0);
            if (it.drawn && t === it.t) continue;
            if (it.drawn && it.visible && Math.abs(t - it.t) < 0.0008) continue;   // settled
            it.t = t;
            it.drawn = true;
            it.el.style.opacity = t.toFixed(3);
            it.el.style.transform = t > 0.999 ? '' : `translate3d(0, ${((1 - t) * 36).toFixed(2)}px, 0)`;
        }
    };
    requestAnimationFrame(frame);
}
