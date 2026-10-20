import { prefersReducedMotion } from '../utils/motion.js';

/**
 * Counts [data-count-to] elements up from zero the first time they scroll
 * into view. The markup already holds the final value, so no-JS and
 * reduced-motion visitors just see the number.
 */
export function initCountUp(selector = '[data-count-to]') {
    if (prefersReducedMotion()) return;

    const easeOutExpo = (t) => (t === 1 ? 1 : 1 - Math.pow(2, -10 * t));

    const run = (el) => {
        const target   = parseFloat(el.dataset.countTo);
        const decimals = (el.dataset.countTo.split('.')[1] ?? '').length;
        const duration = 1600;
        const start    = performance.now();

        const tick = (now) => {
            const t = Math.min(1, (now - start) / duration);
            el.textContent = (target * easeOutExpo(t)).toFixed(decimals);
            if (t < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    };

    const io = new IntersectionObserver((entries) => {
        for (const entry of entries) {
            if (!entry.isIntersecting) continue;
            io.unobserve(entry.target);
            run(entry.target);
        }
    }, { threshold: 0.6 });

    document.querySelectorAll(selector).forEach((el) => {
        el.textContent = (0).toFixed((el.dataset.countTo.split('.')[1] ?? '').length);
        io.observe(el);
    });
}
