import upsideDown from '../rooms/upside-down.js';
import mirror     from '../rooms/mirror.js';
import infinity   from '../rooms/infinity.js';
import beuchet    from '../rooms/beuchet.js';
import { prefersReducedMotion } from '../utils/motion.js';
import { easeInOut } from '../rooms/shared.js';

const ILLUSIONS = { 'upside-down': upsideDown, mirror, infinity, beuchet };
const PLAY_MS   = 1800;   // one run of a scroll-driven illusion (the room turning, the chair lining up)

/**
 * Home hero strip: one tile per room, each holding that room's illusion (the
 * same modules as the rooms section). At rest every tile is still, in its
 * finished state, so the hero has one moving thing (the visit card's tunnel).
 * Hovering a tile plays its illusion once — or, for the looping ones, runs it
 * while the pointer stays — then it settles. Touch and reduced motion: still.
 */
export function initHeroStrip() {
    const tiles = [...document.querySelectorAll('[data-hero-tile]')];
    if (!tiles.length) return;

    const reduced = prefersReducedMotion();
    const hover   = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    tiles.forEach((tile) => {
        const make = ILLUSIONS[tile.dataset.heroTile];
        if (!make) return;

        const illusion = make(tile.querySelector('[data-room-stage]'), { reduced });
        illusion.setProgress(1);   // the finished state, still

        if (reduced || !hover) return;

        let raf = 0;
        const play = () => {
            cancelAnimationFrame(raf);
            const t0 = performance.now();
            const step = (now) => {
                const t = Math.min(1, (now - t0) / PLAY_MS);
                illusion.setProgress(easeInOut(t));
                if (t < 1) raf = requestAnimationFrame(step);
            };
            raf = requestAnimationFrame(step);
        };

        tile.addEventListener('pointerenter', (e) => {
            if (e.pointerType !== 'mouse') return;
            illusion.start();   // the loops (mirror, infinity) run while hovered
            play();             // the one-shot ones (upside down, chair) play through
        });

        tile.addEventListener('pointerleave', () => {
            illusion.stop();
            cancelAnimationFrame(raf);
            illusion.setProgress(1);
        });
    });
}
