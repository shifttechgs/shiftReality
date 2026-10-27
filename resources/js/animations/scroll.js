import { prefersReducedMotion } from '../utils/motion.js';

/**
 * Nav scroll behaviour — the header stays pinned at all times.
 * .nav--scrolled once the page leaves the top: the flat, frosted inset bar.
 * The top bar (as on Artora) is there at the top of the page and tucks away
 * once you scroll; back at the top, it's back.
 */
function initNavScroll() {
    const nav    = document.querySelector('.nav');
    const banner = document.getElementById('top-banner');
    if (!nav) return;

    const FLOAT_THRESHOLD  = 80;   // px before the bar style kicks in
    const BANNER_THRESHOLD = 40;   // px before the top bar tucks away

    const update = () => {
        const sy = window.scrollY;

        nav.classList.toggle('nav--scrolled', sy > FLOAT_THRESHOLD);

        banner?.classList.toggle('is-hidden', sy > BANNER_THRESHOLD);
    };

    window.addEventListener('scroll', update, { passive: true });
    update();
}

/**
 * Parallax layers — elements with data-parallax="speed" drift at a fraction
 * of the scroll speed. Disabled for reduced-motion users and touch devices.
 */
function initParallax() {
    if (prefersReducedMotion()) return;
    if (window.matchMedia('(hover: none)').matches) return;

    const layers = Array.from(document.querySelectorAll('[data-parallax]')).map(el => ({
        el,
        speed: parseFloat(el.dataset.parallax) || 0.15,
    }));

    if (!layers.length) return;

    const tick = () => {
        const sy = window.scrollY;
        layers.forEach(({ el, speed }) => {
            el.style.transform = `translateY(${sy * speed}px)`;
        });
    };

    window.addEventListener('scroll', tick, { passive: true });
    tick();
}

export function initScroll() {
    initNavScroll();
    initParallax();
}
