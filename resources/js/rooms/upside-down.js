import { segment, easeInOut } from './shared.js';

/**
 * Upside Down Room — the room rotates 180° with scroll; the visitor doesn't,
 * so they end up standing on what was the ceiling. They sway a little mid-turn.
 */
export default function upsideDown(stage) {
    const room   = stage.querySelector('[data-ud-room]');
    const person = stage.querySelector('[data-ud-person]');
    const label  = stage.querySelector('[data-ud-label]');

    return {
        setProgress(p) {
            const t = easeInOut(segment(p, 0.12, 0.5));
            room.style.transform   = `rotate(${t * 180}deg)`;
            person.style.transform = `rotate(${Math.sin(t * Math.PI) * 7}deg)`;
            label.textContent      = t > 0.5 ? 'FLOOR' : 'CEILING';
        },
        start() {},
        stop() {},
    };
}
