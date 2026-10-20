import { prefersReducedMotion } from '../utils/motion.js';

/**
 * Full-bleed hero slider — no GSAP, runs on the critical path.
 *
 * All motion lives in CSS (clip-path wipe, parallax push, slow zoom).
 * The active progress bar's fill animation is the timer: when it ends we
 * advance, so pausing the CSS animation pauses the slider too.
 */
export function initHeroSlider() {
    const root = document.querySelector('[data-hero-slider]');
    if (!root) return;

    const slides  = [...root.querySelectorAll('[data-hero-slide]')];
    const bars    = [...root.querySelectorAll('[data-hero-bar]')];
    const counter = root.querySelector('[data-hero-current]');
    if (slides.length < 2) return;

    let current = 0;

    const show = (next) => {
        if (next === current) return;

        slides.forEach((s) => s.classList.remove('is-leaving'));
        slides[current].classList.replace('is-active', 'is-leaving');
        slides[next].classList.add('is-active');

        bars[current].classList.remove('is-active');
        bars[next].classList.add('is-active');

        if (counter) counter.textContent = String(next + 1).padStart(2, '0');
        current = next;
    };

    // Once the wipe finishes, the slide underneath can go back to idle
    slides.forEach((slide) => {
        slide.addEventListener('transitionend', (e) => {
            if (e.target !== slide || e.propertyName !== 'clip-path') return;
            if (!slide.classList.contains('is-active')) return;
            slides.forEach((s) => s !== slide && s.classList.remove('is-leaving'));
        });
    });

    // Autoplay — skipped for reduced motion (CSS collapses the bar to ~0ms)
    bars.forEach((bar, i) => {
        bar.addEventListener('animationend', () => {
            if (prefersReducedMotion() || i !== current) return;
            show((current + 1) % slides.length);
        });
        bar.addEventListener('click', () => show(i));
    });

    // Pause while the hero is off-screen or the tab is hidden
    let offscreen = false;
    const syncPaused = () => root.classList.toggle('is-paused', offscreen || document.hidden);

    new IntersectionObserver(([entry]) => {
        offscreen = !entry.isIntersecting;
        syncPaused();
    }).observe(root);

    document.addEventListener('visibilitychange', syncPaused);
}
