import { segment, easeInOut } from './shared.js';

/**
 * Beuchet Chair — the seat is a separate, distant piece. Scrolling moves the
 * camera until it lines up with the near legs: one chair, one giant, one tiny.
 * The dashed "1.7 m" markers show they're really the same height.
 */
const VIEW_W = 400;
const FLOOR  = 320;

/** Perspective floor grid converging on (200, horizon). */
function floorPath(horizon) {
    const d = [];
    for (let x = -400; x <= VIEW_W + 400; x += 50) {
        const startY = horizon + 14;
        const k = (startY - horizon) / (FLOOR - horizon);
        d.push(`M${200 + (x - 200) * k} ${startY} L${x} ${FLOOR}`);
    }
    for (let n = 1; n <= 7; n++) {
        const y = horizon + (FLOOR - horizon) * Math.pow(n / 7, 2);
        if (y > horizon + 14) d.push(`M0 ${y} L${VIEW_W} ${y}`);
    }
    return d.join(' ');
}

export default function beuchet(stage) {
    const far     = stage.querySelector('[data-bc-far]');
    const grid    = stage.querySelector('[data-bc-grid]');
    const truth   = stage.querySelector('[data-bc-truth]');
    const caption = stage.querySelector('[data-bc-caption]');

    return {
        setProgress(p) {
            const t = easeInOut(segment(p, 0.12, 0.5));

            far.style.transform = `translate(${(1 - t) * 96}px, ${(1 - t) * -84}px)`;
            grid.setAttribute('d', floorPath(212 - t * 92));
            truth.style.opacity = String(0.15 + segment(t, 0.7, 1) * 0.85);

            const aligned = t > 0.9;
            caption.textContent = aligned ? 'One chair. Same height. Giant and tiny.' : 'Two pieces. Metres apart.';
            caption.classList.toggle('is-aligned', aligned);
        },
        start() {},
        stop() {},
    };
}
