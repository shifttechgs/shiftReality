/**
 * Reduced motion detection and animation utilities.
 * Always check this before triggering non-essential animations.
 */

export const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/**
 * Watches for changes to the prefers-reduced-motion media query.
 * Calls the callback immediately with the current value, then on each change.
 */
export function watchReducedMotion(callback) {
    const mq = window.matchMedia('(prefers-reduced-motion: reduce)');
    callback(mq.matches);
    mq.addEventListener('change', (e) => callback(e.matches));
}

/** True on touch-primary devices — scale back cursor/hover-only effects. */
export const isTouchDevice = () =>
    window.matchMedia('(hover: none)').matches;

/** Linear interpolation */
export const lerp = (a, b, t) => a + (b - a) * t;

/** Clamp a value between min and max */
export const clamp = (value, min, max) => Math.min(Math.max(value, min), max);
