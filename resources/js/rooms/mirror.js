import { segment, pointerTracker } from './shared.js';

/**
 * Stranger in the Mirror — three reflections of the pointer, each disobeying
 * differently: mirrored left/right, lagging far behind, and upside down.
 * Without a pointer (touch, or idle) an autopilot traces a slow figure-eight.
 */
export default function mirror(stage, { reduced }) {
    const panes = [...stage.querySelectorAll('[data-mirror-pane]')].map((el) => ({
        el,
        mode: el.dataset.mirrorPane,
        fig:  el.querySelector('[data-mirror-figure]'),
    }));

    const pointer = pointerTracker(stage.closest('[data-room]') ?? stage);
    const cur = { x: 0, y: 0 };
    const lag = { x: 0, y: 0 };
    let raf = 0;

    const place = (fig, x, y, flip) => {
        fig.style.transform = `translate(${x * 62}%, ${y * 30}%)${flip ? ' rotate(180deg)' : ''}`;
    };

    const tick = (now) => {
        let { x: tx, y: ty } = pointer.state;
        if (!pointer.state.active) {
            const s = now / 1000;
            tx = Math.sin(s * 0.8) * 0.75;
            ty = Math.sin(s * 1.6) * 0.5;
        }

        cur.x += (tx - cur.x) * 0.16;
        cur.y += (ty - cur.y) * 0.16;
        lag.x += (tx - lag.x) * 0.022;
        lag.y += (ty - lag.y) * 0.022;

        for (const { mode, fig } of panes) {
            if (mode === 'mirrored') place(fig, -cur.x, cur.y, false);
            if (mode === 'delayed')  place(fig, lag.x, lag.y, false);
            if (mode === 'inverted') place(fig, cur.x, -cur.y, true);
        }
        raf = requestAnimationFrame(tick);
    };

    return {
        setProgress(p) {
            // Panes rise in one after another
            panes.forEach(({ el }, i) => el.style.setProperty('--in', segment(p, 0.08 + i * 0.07, 0.34 + i * 0.07)));
        },
        start() {
            if (reduced || raf) return;
            pointer.listen();
            raf = requestAnimationFrame(tick);
        },
        stop() {
            cancelAnimationFrame(raf);
            raf = 0;
            pointer.unlisten();
        },
    };
}
