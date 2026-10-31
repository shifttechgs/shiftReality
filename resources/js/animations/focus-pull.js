import { prefersReducedMotion, clamp } from '../utils/motion.js';

/**
 * Focus pull on a frosted picture ([data-focus-pull], its img dimmed in
 * CSS): heavily frosted as it comes on screen, it sharpens as its band
 * nears the middle of the screen, as if your eyes were adjusting, and
 * frosts again as it leaves. Tied to the scroll, smoothed. It never goes
 * fully sharp: the words over it keep their contrast.
 * Reduced motion: it stays at the CSS frost.
 */
const FROST = [16, 3];   // blur (px): at the screen's edge → with the band centred
const SCALE = [1.14, 1.06];
const FOLLOW = 0.1;

const ease = (t) => 1 - Math.pow(1 - t, 3);

export function initFocusPull() {
    if (prefersReducedMotion()) return;

    document.querySelectorAll('[data-focus-pull]').forEach((box) => {
        const img = box.querySelector('img');
        const band = box.parentElement;
        if (!img) return;

        let f = 0, raf = 0, seen = false;
        const draw = () => {
            raf = 0;
            const r = band.getBoundingClientRect(), vh = window.innerHeight;
            // 1 with the band's middle on the screen's, 0 a half screen (plus half the band) away
            const off = Math.abs(r.top + r.height / 2 - vh / 2) / (vh / 2 + r.height / 2);
            const to = ease(clamp(1 - off * 1.35, 0, 1));
            f += (to - f) * FOLLOW;
            if (Math.abs(to - f) < 0.001) f = to;
            img.style.filter = `blur(${(FROST[0] + (FROST[1] - FROST[0]) * f).toFixed(2)}px)`;
            img.style.scale = (SCALE[0] + (SCALE[1] - SCALE[0]) * f).toFixed(4);
            if (seen) raf = requestAnimationFrame(draw);
        };
        new IntersectionObserver(([e]) => {
            seen = e.isIntersecting;
            if (seen && !raf) raf = requestAnimationFrame(draw);
        }, { rootMargin: '10% 0px' }).observe(band);
        draw();
    });
}
