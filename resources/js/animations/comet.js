import { prefersReducedMotion } from '../utils/motion.js';

/**
 * A shooting-star trail behind the mouse, on every page (it began in the home
 * hero). The last few hundred ms of pointer positions are drawn as one tapering
 * streak on a fixed, full-screen canvas that ignores the pointer. It reads what
 * it's passing over: on light backgrounds light blue → the deep brand blue at
 * its small head; on dark ones (the navy bands) light blue → white, so it never
 * disappears. Mouse only; the loop stops as soon as the trail has faded.
 * Off for reduced motion and touch.
 */
const LIFE_MS = 320;     // how long a point stays in the trail
const WIDTH   = 3.2;     // px at the head
const TAIL    = [61, 116, 245];                    // the lighter brand blue (#3D74F5)
const HEAD    = { light: [24, 85, 232],            // the brand blue on light (#1855E8)
                  dark:  [255, 255, 255] };        // white on navy
const PROBE_MS = 80;     // how often to re-check the background under the pointer

export function initComet() {
    const canvas = document.querySelector('[data-comet]');
    if (!canvas || prefersReducedMotion()) return;
    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;

    const ctx = canvas.getContext('2d');
    let points = [];
    let raf = 0;
    let w = 0, h = 0;

    const resize = () => {
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        w = window.innerWidth;
        h = window.innerHeight;
        canvas.width  = Math.round(w * dpr);
        canvas.height = Math.round(h * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    };
    resize();   // size it now: drawing on the default 300×150 canvas would stretch every stroke
    window.addEventListener('resize', resize, { passive: true });

    // Is the pointer over a dark surface? The first painted background up the
    // tree decides (the canvas itself ignores the pointer, so it's skipped).
    let dark = false, probedAt = 0;
    const probe = (x, y, now) => {
        if (now - probedAt < PROBE_MS) return dark;
        probedAt = now;
        let el = document.elementFromPoint(x, y);
        while (el && el !== document.documentElement) {
            const m = getComputedStyle(el).backgroundColor.match(/[\d.]+/g);
            if (m && (m.length < 4 || Number(m[3]) > 0.5)) {
                const [r, g, b] = m.map(Number);
                dark = (0.2126 * r + 0.7152 * g + 0.0722 * b) < 110;
                return dark;
            }
            el = el.parentElement;
        }
        dark = false;
        return dark;
    };

    // 1 = fresh … 0 = gone. Clamped: a frame's timestamp can trail the event's,
    // which would otherwise read as "fresher than new" and fatten the stroke.
    const lifeOf = (p, now) => Math.min(1, Math.max(0, 1 - (now - p.t) / LIFE_MS));

    const draw = () => {
        const now = performance.now();   // the same clock the points are stamped with
        points = points.filter((p) => now - p.t < LIFE_MS);
        ctx.clearRect(0, 0, w, h);

        if (points.length > 1) {
            ctx.lineCap = 'round';
            // Segment by segment, older = thinner and fainter; each takes the
            // head colour of the surface its point was drawn over
            for (let i = 1; i < points.length; i++) {
                const a = points[i - 1], b = points[i];
                const life = lifeOf(b, now);
                const along = i / (points.length - 1);       // 0 tail … 1 head
                const mix = along * along;
                const end = HEAD[b.dark ? 'dark' : 'light'];
                const r = Math.round(TAIL[0] + (end[0] - TAIL[0]) * mix);
                const g = Math.round(TAIL[1] + (end[1] - TAIL[1]) * mix);
                const bl = Math.round(TAIL[2] + (end[2] - TAIL[2]) * mix);
                ctx.strokeStyle = `rgba(${r}, ${g}, ${bl}, ${(life * (0.25 + 0.75 * along)).toFixed(3)})`;
                ctx.lineWidth = Math.max(0.4, WIDTH * along * life);
                ctx.beginPath();
                ctx.moveTo(a.x, a.y);
                ctx.lineTo(b.x, b.y);
                ctx.stroke();
            }

            // The star itself: a small bright head with a soft halo
            const head = points[points.length - 1];
            const life = lifeOf(head, now);
            const c = HEAD[head.dark ? 'dark' : 'light'].join(', ');
            ctx.fillStyle = `rgba(${c}, ${((head.dark ? 0.18 : 0.12) * life).toFixed(3)})`;
            ctx.beginPath();
            ctx.arc(head.x, head.y, 7, 0, Math.PI * 2);
            ctx.fill();
            ctx.fillStyle = `rgba(${c}, ${life.toFixed(3)})`;
            ctx.beginPath();
            ctx.arc(head.x, head.y, 2, 0, Math.PI * 2);
            ctx.fill();
        }

        raf = points.length ? requestAnimationFrame(draw) : 0;
    };

    window.addEventListener('pointermove', (e) => {
        if (e.pointerType !== 'mouse') return;
        const now = performance.now();
        points.push({ x: e.clientX, y: e.clientY, t: now, dark: probe(e.clientX, e.clientY, now) });
        if (!raf) raf = requestAnimationFrame(draw);
    }, { passive: true });

    // Leaving the window: let the trail fade out rather than freeze mid-air
    document.addEventListener('pointerleave', () => { if (!raf && points.length) raf = requestAnimationFrame(draw); });
}
