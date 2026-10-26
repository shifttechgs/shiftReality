import infinity from '../rooms/infinity.js';
import { prefersReducedMotion } from '../utils/motion.js';

/**
 * Home hero object: the Infinity Room's LED tunnel, live, in a tall window.
 * The pointer anywhere over the hero drags the vanishing point; idle, it drifts
 * on its own. Runs only while the hero is on screen. Reduced motion: one still frame.
 */
const HERO_LEDS = ['#5382c7', '#c4cddd'];   // the hero palette's soft blue and pale blue-white

export function initHeroTunnel() {
    const stage = document.querySelector('[data-hero-tunnel]');
    if (!stage) return;

    const reduced = prefersReducedMotion();
    const tunnel  = infinity(stage, { reduced, fit: true, track: stage.closest('.wh-hero'), colors: HERO_LEDS });
    tunnel.setProgress(1);   // full brightness from the start

    new IntersectionObserver(([entry]) => {
        entry.isIntersecting ? tunnel.start() : tunnel.stop();
    }).observe(stage);
}
