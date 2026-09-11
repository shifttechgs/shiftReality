import { prefersReducedMotion } from '../utils/motion.js';

/**
 * Nav scroll behaviour — adds .nav--scrolled class when page is scrolled.
 */
function initNavScroll() {
    const nav = document.querySelector('.nav');
    if (!nav) return;

    const threshold = 60;

    const update = () => {
        nav.classList.toggle('nav--scrolled', window.scrollY > threshold);
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
