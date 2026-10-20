import { prefersReducedMotion, clamp } from '../utils/motion.js';

/**
 * Footer motion, all tied to how much of the footer has been uncovered (p).
 * - Content: each column's lines rise and fade in, columns left to right and
 *   lines top to bottom within each; the legal row lands last. Scrolling back
 *   up reverses it.
 * - Watermark: its letters rise out of the bottom edge one after another,
 *   and letters near the pointer lift a little and brighten (--lit).
 * Reduced motion (or no JS): everything simply sits in place.
 */
const LINES = [
    '.footer__logo', '.footer__tagline', '.footer__social',
    '.footer__title', '.footer__links > li', '.footer__status',
    '.footer__hours-row', '.footer__address', '.footer__link--arrow',
].join(', ');

export function initFooterMotion() {
    const footer = document.querySelector('.footer');
    if (!footer || prefersReducedMotion()) return;

    const ease = (t) => 1 - Math.pow(1 - t, 3);

    // ── Content: give every line a start point in the reveal ──
    const items = [];
    [...footer.querySelectorAll('.footer__grid > *')].forEach((col, c) => {
        [...col.querySelectorAll(LINES)].forEach((el, r) => {
            items.push({ el, start: c * 0.09 + r * 0.03, t: 0, drawn: false });
        });
    });
    footer.querySelectorAll('.footer__bottom > *').forEach((el, i) => {
        items.push({ el, start: 0.5 + i * 0.05, t: 0, drawn: false });
    });
    footer.classList.add('is-moving');

    // ── Watermark letters ──
    const mark = footer.querySelector('[data-watermark]');
    const letters = mark ? [...mark.querySelectorAll('.footer__wm-l')] : [];
    const state = letters.map(() => ({ rise: 0, lit: 0 }));
    if (mark) mark.classList.add('is-driven');

    let pointerX = null;
    footer.addEventListener('pointermove', (e) => { if (e.pointerType === 'mouse') pointerX = e.clientX; }, { passive: true });
    footer.addEventListener('pointerleave', () => { pointerX = null; });

    let visible = false;
    new IntersectionObserver(([entry]) => { visible = entry.isIntersecting; }).observe(footer);

    const frame = () => {
        requestAnimationFrame(frame);
        if (!visible) return;

        // how much of the footer has been uncovered: 0 → 1 over its last screen of scroll
        const doc = document.documentElement;
        const remaining = doc.scrollHeight - (window.scrollY + window.innerHeight);
        const p = clamp(1 - remaining / footer.offsetHeight, 0, 1);

        // content lines: each takes 40% of the reveal, starting at its own point
        for (const it of items) {
            const target = ease(clamp((p - it.start) / 0.4, 0, 1));
            const t = it.t + (target - it.t) * 0.16;
            if (it.drawn && Math.abs(t - it.t) < 0.001) continue;   // settled
            it.t = t;
            it.drawn = true;
            it.el.style.opacity = t.toFixed(3);
            it.el.style.transform = t > 0.999 ? '' : `translate3d(0, ${((1 - t) * 18).toFixed(2)}px, 0)`;
        }

        // watermark: staggered rise + cursor glow
        const n = letters.length;
        letters.forEach((el, i) => {
            const st = state[i];
            const target = ease(clamp((p - (i / n) * 0.45) / 0.55, 0, 1));
            st.rise += (target - st.rise) * 0.14;

            let lit = 0;
            if (pointerX !== null) {
                const r = el.getBoundingClientRect();
                lit = Math.max(0, 1 - Math.abs(r.left + r.width / 2 - pointerX) / 220);
            }
            st.lit += (lit - st.lit) * 0.12;

            el.style.transform = `translate3d(0, ${((1 - st.rise) * 70 - st.lit * 8).toFixed(2)}%, 0)`;
            el.style.setProperty('--lit', st.lit.toFixed(3));
        });
    };
    requestAnimationFrame(frame);
}
