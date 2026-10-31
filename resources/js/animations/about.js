import { prefersReducedMotion, clamp } from '../utils/motion.js';

/**
 * The About section in motion. The photo opens out of a small triangle on
 * the picture's logo (the figure's data-reveal-at, as % of the frame) until
 * it fills its frame, as the section comes up the screen — tied to the
 * scroll and smoothed, closing again going back up — then drifts inside its
 * frame as you scroll past. The words come in once, in order: the intro
 * rises line by line from behind masks, the pill,
 * the stats' hairline draws across and the numbers (count-up.js) follow;
 * the map pin drops in by the address.
 * Reduced motion / no JS: all simply there.
 */
const OPEN   = [0.92, 0.3];   // the photo opens as its top goes from 92% to 30% of the screen…
const START  = 0.09;          // …from a triangle this big (its circumradius, a share of the frame's width)
const ZOOM   = [1.3, 1.1];    // the picture settles from this scale to this as it opens
const DRIFT  = 0.05;          // then drifts this much inside the frame (a share of its height)
const FOLLOW = 0.1;           // how fast it catches up with the scroll, per frame
const SHOWN  = 2600;          // ms after the words come in that the intro goes back to plain text

const easeInOut = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

export function initAbout() {
    const root = document.querySelector('.about');
    if (!root || prefersReducedMotion()) return;
    root.classList.add('about--live');

    // ── The intro, split into its lines (each rises from behind a mask) ──
    const text = root.querySelector('.about__text');
    const plain = text.innerHTML;
    let shown = false;
    const splitLines = () => {
        text.innerHTML = plain;
        const words = text.textContent.trim().split(/\s+/);
        text.textContent = '';
        const spans = words.map((w, i) => {
            const s = document.createElement('span');
            s.textContent = w;
            text.append(s, i < words.length - 1 ? ' ' : '');
            return s;
        });
        const lines = [];
        let top = null;
        spans.forEach((s) => {
            if (s.offsetTop !== top) { lines.push([]); top = s.offsetTop; }
            lines[lines.length - 1].push(s.textContent);
        });
        text.textContent = '';
        lines.forEach((line, i) => {
            const row = document.createElement('span');
            row.className = 'about__row';
            const inner = document.createElement('span');
            inner.style.setProperty('--i', String(i));
            inner.textContent = line.join(' ') + (i < lines.length - 1 ? ' ' : '');
            row.append(inner);
            text.append(row);
        });
    };
    splitLines();
    let resizeT = 0;
    window.addEventListener('resize', () => {
        if (shown) return;
        clearTimeout(resizeT);
        resizeT = setTimeout(splitLines, 150);
    });

    new IntersectionObserver(([e], io) => {
        if (!e.isIntersecting) return;
        io.disconnect();
        shown = true;
        root.classList.add('is-in');
        // once they're up, plain text again (the lines were cut for this width)
        setTimeout(() => { text.innerHTML = plain; }, SHOWN);
    }, { threshold: 0.35 }).observe(root.querySelector('.about__main'));

    // ── The photo, opening out of a triangle on its logo ──
    const photo = root.querySelector('.about__photo');
    const img = photo?.querySelector('img');
    if (!img) return;
    const [ax, ay] = (photo.dataset.revealAt || '50 40').split(' ').map((v) => Number(v) / 100);

    let q = 0, raf = 0, seen = false;
    const draw = () => {
        raf = 0;
        const r = photo.getBoundingClientRect(), vh = window.innerHeight;
        const to = clamp((vh * OPEN[0] - r.top) / (vh * (OPEN[0] - OPEN[1])), 0, 1);
        q += (to - q) * FOLLOW;
        if (Math.abs(to - q) < 0.0005) q = to;
        const e = easeInOut(q);

        const w = r.width, h = r.height;
        const cx = ax * w, cy = ay * h;
        // grows (evenly, as if coming closer) until it holds the whole frame
        const r0 = START * w, rEnd = 2.2 * Math.hypot(Math.max(cx, w - cx), Math.max(cy, h - cy));
        const rad = r0 * Math.pow(rEnd / r0, e);
        img.style.clipPath = e >= 1 ? '' : `polygon(${cx.toFixed(1)}px ${(cy - rad).toFixed(1)}px, ${(cx + 0.866 * rad).toFixed(1)}px ${(cy + rad / 2).toFixed(1)}px, ${(cx - 0.866 * rad).toFixed(1)}px ${(cy + rad / 2).toFixed(1)}px)`;

        const pass = clamp((vh - r.top) / (vh + h), 0, 1) - 0.5;   // -0.5 → 0.5 as it scrolls past
        const zoom = ZOOM[0] + (ZOOM[1] - ZOOM[0]) * e;
        img.style.transformOrigin = `${(ax * 100).toFixed(1)}% ${(ay * 100).toFixed(1)}%`;
        img.style.transform = `translateY(${(-pass * DRIFT * 100).toFixed(2)}%) scale(${zoom.toFixed(4)})`;

        if (seen) raf = requestAnimationFrame(draw);
    };
    new IntersectionObserver(([e]) => {
        seen = e.isIntersecting;
        if (seen && !raf) raf = requestAnimationFrame(draw);
    }, { rootMargin: '15% 0px' }).observe(photo);
    draw();
}
