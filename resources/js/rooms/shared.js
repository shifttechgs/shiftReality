import { clamp } from '../utils/motion.js';

/** Map progress p into 0–1 across the [from, to] window. */
export const segment = (p, from, to) => clamp((p - from) / (to - from), 0, 1);

/** Cubic ease-in-out — the illusions settle rather than snap. */
export const easeInOut = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

/**
 * Tracks the pointer over an element as -1…1 on each axis. When the pointer
 * has been idle (or never arrived — touch), `active` is false so callers can
 * run an autopilot instead.
 */
export function pointerTracker(el) {
    const state = { x: 0, y: 0, active: false };
    let idle = 0;

    const onMove = (e) => {
        const r = el.getBoundingClientRect();
        state.x = ((e.clientX - r.left) / r.width) * 2 - 1;
        state.y = ((e.clientY - r.top) / r.height) * 2 - 1;
        state.active = true;
        clearTimeout(idle);
        idle = setTimeout(() => { state.active = false; }, 2500);
    };

    return {
        state,
        listen:   () => el.addEventListener('pointermove', onMove, { passive: true }),
        unlisten: () => { el.removeEventListener('pointermove', onMove); state.active = false; },
    };
}
