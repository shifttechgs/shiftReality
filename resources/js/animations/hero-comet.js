import { prefersReducedMotion } from '../utils/motion.js';

/**
 * Home hero: a shooting-star trail behind the mouse. The last few hundred ms of
 * pointer positions are drawn as one tapering streak — soft blue at the tail,
 * a small white head — that fades as it ages. Mouse only, hero only; the loop
 * stops as soon as the trail has faded. Off for reduced motion and touch.
 */
const LIFE_MS = 320;     // how long a point stays in the trail
const WIDTH   = 3.2;     // px at the head
const TAIL    = [83, 130, 199];   // the palette's soft blue (#5382C7)

export function initHeroComet() {
    const canvas = document.querySelector('[data-hero-comet]');
    const hero   = canvas?.closest('.wh-hero');
    if (!canvas || !hero || prefersReducedMotion()) return;
    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;

    const ctx = canvas.getContext('2d');
    let points = [];
    let raf = 0;
    let w = 0, h = 0;

    const resize = () => {
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        w = hero.clientWidth;
        h = hero.clientHeight;
        canvas.width  = Math.round(w * dpr);
        canvas.height = Math.round(h * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    };
    resize();   // size it now: drawing on the default 300×150 canvas would stretch every stroke
    new ResizeObserver(resize).observe(hero);

    // 1 = fresh … 0 = gone. Clamped: a frame's timestamp can trail the event's,
    // which would otherwise read as "fresher than new" and fatten the stroke.
    const lifeOf = (p, now) => Math.min(1, Math.max(0, 1 - (now - p.t) / LIFE_MS));

    const draw = () => {
        const now = performance.now();   // the same clock the points are stamped with
        points = points.filter((p) => now - p.t < LIFE_MS);
        ctx.clearRect(0, 0, w, h);

        if (points.length > 1) {
            ctx.lineCap = 'round';
            // Segment by segment, older = thinner and fainter
            for (let i = 1; i < points.length; i++) {
                const a = points[i - 1], b = points[i];
                const life = lifeOf(b, now);
                const along = i / (points.length - 1);       // 0 tail … 1 head
                const mix = along * along;
                const r = Math.round(TAIL[0] + (255 - TAIL[0]) * mix);
                const g = Math.round(TAIL[1] + (255 - TAIL[1]) * mix);
                const bl = Math.round(TAIL[2] + (255 - TAIL[2]) * mix);
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
            ctx.fillStyle = `rgba(255, 255, 255, ${(0.18 * life).toFixed(3)})`;
            ctx.beginPath();
            ctx.arc(head.x, head.y, 7, 0, Math.PI * 2);
            ctx.fill();
            ctx.fillStyle = `rgba(255, 255, 255, ${life.toFixed(3)})`;
            ctx.beginPath();
            ctx.arc(head.x, head.y, 2, 0, Math.PI * 2);
            ctx.fill();
        }

        raf = points.length ? requestAnimationFrame(draw) : 0;
    };

    hero.addEventListener('pointermove', (e) => {
        if (e.pointerType !== 'mouse') return;
        const r = hero.getBoundingClientRect();
        points.push({ x: e.clientX - r.left, y: e.clientY - r.top, t: performance.now() });
        if (!raf) raf = requestAnimationFrame(draw);
    }, { passive: true });
}
