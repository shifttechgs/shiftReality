import { prefersReducedMotion } from '../utils/motion.js';

/**
 * Nav scroll behaviour — adds .nav--scrolled class when page is scrolled.
 */
function initNavScroll() {
    const nav    = document.querySelector('.nav');
    const banner = document.getElementById('top-banner');
    if (!nav) return;

    const FLOAT_THRESHOLD  = 80;   // px before nav becomes floating island
    const BANNER_THRESHOLD = 40;   // px before banner auto-collapses

    const update = () => {
        const sy = window.scrollY;

        // Floating island
        nav.classList.toggle('nav--scrolled', sy > FLOAT_THRESHOLD);

        // Auto-collapse banner on scroll — keeps it as a seen signal, clears space
        if (banner && sy > BANNER_THRESHOLD && !banner.classList.contains('is-hidden')) {
            banner.classList.add('is-hidden');
            sessionStorage.setItem('banner-dismissed', '1');
        }
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
