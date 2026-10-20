import { segment, pointerTracker } from './shared.js';

/**
 * Infinity Room — LED points strung around nested mirror frames, flying
 * endlessly toward the viewer. The pointer drags the vanishing point.
 */
const FRAMES   = 16;   // nested mirror frames in flight
const PER_SIDE = 9;    // LEDs per frame edge
const SPEED    = 0.9;  // frames travelled per second
const BLUES    = ['#1855e8', '#3d74f5'];   // the two palette blues
const GLINT    = '#ffffff';  // highlight frames

export default function infinity(stage, { reduced }) {
    const canvas  = stage.querySelector('[data-infinity]');
    const ctx     = canvas.getContext('2d');
    const pointer = pointerTracker(stage.closest('[data-room]') ?? stage);

    let w = 0, h = 0;
    let raf = 0;
    let intensity = reduced ? 1 : 0;
    const vp = { x: 0, y: 0 };

    const resize = () => {
        const r   = canvas.getBoundingClientRect();
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        w = r.width;
        h = r.height;
        canvas.width  = Math.round(w * dpr);
        canvas.height = Math.round(h * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    };

    const draw = (time) => {
        if (!w || !h) return;
        ctx.globalCompositeOperation = 'source-over';
        ctx.clearRect(0, 0, w, h);
        ctx.globalCompositeOperation = 'lighter';

        const reach = Math.max(w, h) * 0.78;

        for (let k = 0; k < FRAMES; k++) {
            const z     = ((k + time * SPEED) % FRAMES) / FRAMES;  // 0 = far, 1 = passing the camera
            const depth = 1 - z;
            const scale = 1 / (1 + depth * 7);
            const half  = reach * scale;

            // Far frames sit on the vanishing point, near frames on the stage centre
            const fx = w / 2 + vp.x * w * 0.2 * depth;
            const fy = h / 2 + vp.y * h * 0.2 * depth;

            const fade  = Math.min(1, z * 3) * Math.min(1, (1 - z) * 5);
            const alpha = fade * intensity;
            if (alpha <= 0.01) continue;

            const color  = k % 5 === 0 ? GLINT : BLUES[k % BLUES.length];
            const radius = Math.max(0.6, 3.4 * scale + 0.4);

            // Faint mirror edge
            ctx.globalAlpha = alpha * 0.07;
            ctx.strokeStyle = '#3d74f5';
            ctx.lineWidth   = 1;
            ctx.strokeRect(fx - half, fy - half, half * 2, half * 2);

            ctx.fillStyle = color;
            for (let side = 0; side < 4; side++) {
                for (let i = 0; i < PER_SIDE; i++) {
                    const u = (i / PER_SIDE) * 2 - 1;
                    const x = side === 0 ? u : side === 1 ? 1 : side === 2 ? -u : -1;
                    const y = side === 0 ? -1 : side === 1 ? u : side === 2 ? 1 : -u;
                    const px = fx + x * half;
                    const py = fy + y * half;

                    ctx.globalAlpha = alpha * 0.16;   // glow
                    ctx.beginPath();
                    ctx.arc(px, py, radius * 3.2, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.globalAlpha = alpha;          // core
                    ctx.beginPath();
                    ctx.arc(px, py, radius, 0, Math.PI * 2);
                    ctx.fill();
                }
            }
        }
        ctx.globalAlpha = 1;
    };

    const tick = (now) => {
        const target = pointer.state.active
            ? pointer.state
            : { x: Math.sin(now / 2400) * 0.5, y: Math.cos(now / 3100) * 0.35 };
        vp.x += (target.x - vp.x) * 0.05;
        vp.y += (target.y - vp.y) * 0.05;
        draw(now / 1000);
        raf = requestAnimationFrame(tick);
    };

    new ResizeObserver(() => { resize(); draw(performance.now() / 1000); }).observe(canvas);

    return {
        setProgress(p) {
            if (reduced) return;
            intensity = segment(p, 0.05, 0.38);
            if (!raf) draw(performance.now() / 1000);
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
