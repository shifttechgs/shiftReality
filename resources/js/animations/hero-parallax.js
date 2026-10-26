import { prefersReducedMotion, isTouchDevice } from '../utils/motion.js';

/**
 * Hero background follows the cursor, like the headline's letters do:
 * the photo layer drifts a few pixels toward the pointer (a hair of depth
 * behind the copy) and eases back to centre when the mouse leaves.
 * It's scaled up slightly so the edges never show. Mouse only; off for reduced motion.
 */
const RANGE_X = 18;   // px each way
const RANGE_Y = 12;

export function initHeroParallax() {
    const layer = document.querySelector('[data-hero-parallax]');
    const stage = layer?.closest('[data-hero-stage]');
    if (!layer || !stage || prefersReducedMotion() || isTouchDevice()) return;

    const target = { x: 0, y: 0 };
    const pos = { x: 0, y: 0 };
    let running = false;

    const tick = () => {
        pos.x += (target.x - pos.x) * 0.08;
        pos.y += (target.y - pos.y) * 0.08;
        layer.style.transform = `translate3d(${pos.x.toFixed(2)}px, ${pos.y.toFixed(2)}px, 0) scale(1.04)`;
        if (Math.abs(target.x - pos.x) > 0.05 || Math.abs(target.y - pos.y) > 0.05) {
            requestAnimationFrame(tick);
        } else {
            running = false;
        }
    };
    const kick = () => { if (!running) { running = true; requestAnimationFrame(tick); } };

    stage.addEventListener('pointermove', (e) => {
        if (e.pointerType !== 'mouse') return;
        const r = stage.getBoundingClientRect();
        target.x = ((e.clientX - r.left) / r.width - 0.5) * 2 * RANGE_X;
        target.y = ((e.clientY - r.top) / r.height - 0.5) * 2 * RANGE_Y;
        kick();
    }, { passive: true });

    stage.addEventListener('pointerleave', () => {
        target.x = 0;
        target.y = 0;
        kick();
    });

    layer.style.transform = 'scale(1.04)';   // headroom for the drift, from the start
}
