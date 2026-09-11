import { prefersReducedMotion } from '../utils/motion.js';

/**
 * Scroll-triggered reveal system using IntersectionObserver.
 * Works with data-reveal attributes on any element.
 * CSS handles the actual transition — this just toggles .is-revealed.
 */
export function initReveals() {
    if (prefersReducedMotion()) {
        // Show everything immediately
        document.querySelectorAll('[data-reveal]').forEach(el => {
            el.classList.add('is-revealed');
        });
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    document.querySelectorAll('[data-reveal]').forEach(el => {
        observer.observe(el);
    });
}
