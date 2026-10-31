import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { clamp, lerp } from '../utils/motion.js';
import { createStairwell } from './stairwell.js';

/**
 * The hero. You land on the slides after arlink28.com (with two or more:
 * blinds, rolling captions, thumbnails with progress lines, pause): for
 * now one, a stairwell that never ends (stairwell.js), drifting slowly
 * down it behind the copy and the booking card. Scrolling pins the stage,
 * lifts the copy away and takes you down floor after floor, its centre coming
 * to the middle; then that frame splits (after Framer's Scroll Split
 * Cards): cut into a slice per room card, the slices draw apart and fly
 * to the cards' places in the row, the stairwell broken across them, and
 * flip over one after another into the cards. The rooms sit pulled up
 * under the hero's screen and held in place while the pin runs, so when
 * it lets go, the row is simply there.
 *
 * Reduced motion / no JS: no pin — just the landing.
 */

// Scroll beats, in screens of scroll, then as 0–1 through the pin
const PIN = 2.05;              // the pin's length, in screens
const at  = (screens) => screens / PIN;
const LEAVE  = at(0.05);                 // the landing copy lifts away
const FLY    = [0, 1];                   // down the stairwell, slow and steady, all the way through the split…
const GLIDE  = [0, at(1.05)];            // …its centre coming to the middle, where the frame splits…
const SPLIT  = [at(1.05), at(1.65)];       // …the frame splits, the slices flying to the cards…
const FLIP   = (i) => [at(1.5 + i * 0.1), at(1.75 + i * 0.1)];   // …and each flips over into its card (the last at the pin's end)
const JUMP   = 0.3;            // …unless it's this far behind (of the pin): then it lands at once
const CHASE  = 0.12;           // how fast the stairwell, the split and the flips catch up with the scroll, per frame
const FLOORS = 2.4;            // floors descended with the scroll
const DRIFT  = 1 / 30;         // …and on their own, behind the copy (floors per second)
const SLIDE_MS  = 7000;        // each landing slide's turn
const BANDS     = 30;          // the switch: horizontal blinds…
const BAND_MS   = 300;         // …each opening upwards this long…
const BLINDS_MS = 600;         // …the top one done this long after the bottom one starts
const WELL_GLOW = [0.44, 0.4]; // the stairwell's opacity on the navy (wide, narrow): the picture is bright, and the white copy needs ≥ 4.5:1 (3:1 for the large accent)

const span = (p, [a, b]) => clamp((p - a) / (b - a), 0, 1);
const easeInOut = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);
const easeOut = (t) => 1 - Math.pow(1 - t, 3);
const easeSine = (t) => (1 - Math.cos(Math.PI * t)) / 2;

export function initWalk() {
    const root = document.querySelector('[data-walk]');
    // walk--live is set in app.js's critical path, unless motion is reduced
    if (!root || !root.classList.contains('walk--live')) return;

    const stage    = root.querySelector('.walk__stage');
    // What comes next sits pulled up under the hero's last screen (see
    // pullNext), its room cards waiting for the slices
    const next     = root.nextElementSibling;
    const cards    = next ? [...next.querySelectorAll('[data-walk-card]')] : [];
    const slices   = cards.map(() => {
        const el = document.createElement('div');
        el.className = 'walk__slice';
        el.setAttribute('aria-hidden', 'true');
        const cv = document.createElement('canvas');   // its piece of the stairwell, still going down
        el.append(cv);
        stage.append(el);
        return { el, cv, ctx: cv.getContext('2d') };
    });
    const navy = getComputedStyle(stage).backgroundColor;
    const slides   = [...root.querySelectorAll('[data-walk-slide]')];

    let last = 0, seen = true;

    // The stairwell (the large photo everywhere: phones magnify it most)
    const wellEl = root.querySelector('[data-walk-well]');
    // (the page passes what was measured on the picture, and its opacity)
    const wellOpts = JSON.parse(wellEl.dataset.well || '{}');
    const well = createStairwell(wellEl, wellEl.dataset.src, wellOpts);
    const glows = JSON.parse(wellEl.dataset.glow || 'null') ?? WELL_GLOW;

    // Geometry, re-measured on refresh: the stage, and the well's centre
    let S = { w: 1, h: 1 };
    let WELL_AT = [[0, 0], [0, 0]];      // the well's centre: landing → the middle, where the frame splits
    let wellGlow = glows[0];
    const measure = () => {
        const s = stage.getBoundingClientRect();
        S = { w: s.width, h: s.height };

        // The well's centre sits up and to the right of the copy on landing
        // (high up on phones)
        const wide = s.width > 900;
        WELL_AT = [wide ? [s.width * 0.62, s.height * 0.42] : [s.width * 0.5, s.height * 0.3], [s.width / 2, s.height / 2]];
        // A tunnel ('zoom') keeps its vanishing point where the picture has it,
        // so it covers the screen at the least magnification
        if (wellOpts.mode === 'zoom') {
            const vp = [s.width * wellOpts.well[0], s.height * wellOpts.well[1]];
            WELL_AT = [vp, vp];
        }
        wellGlow = glows[wide ? 0 : 1];

        well.resize(s.width, s.height);

        // Each room card's box on screen when the pin lets go (the rooms'
        // top is then the screen's), and their corners
        const n = next?.getBoundingClientRect();
        CARDS = cards.map((el) => {
            el.style.transform = '';
            const r = el.getBoundingClientRect();
            return { x: r.left, y: r.top - n.top, w: r.width, h: r.height };
        });
        radius = cards.length ? parseFloat(getComputedStyle(cards[0]).borderTopLeftRadius) || 0 : 0;
        // a slice's canvas: its size at the start (its largest), at most 1.5×
        const r = Math.min(window.devicePixelRatio || 1, 1.5);
        slices.forEach(({ cv }) => {
            cv.width = Math.round((S.w / Math.max(1, slices.length)) * r);
            cv.height = Math.round(S.h * r);
        });
    };
    let CARDS = [], radius = 0;

    // Each slice shows the stairwell as it's drawn, so the descent carries
    // on in the pieces (the navy under it, as on the stage): just its own
    // piece (src: the part of the stage it shows, set by renderSplit),
    // stretched over its canvas, which fills the slice
    const paintSlices = () => {
        const R = wellEl.width / S.w;
        slices.forEach(({ el, cv, ctx, src }) => {
            if (el.style.visibility !== 'visible' || !src) return;
            ctx.globalAlpha = 1;
            ctx.fillStyle = navy;
            ctx.fillRect(0, 0, cv.width, cv.height);
            ctx.globalAlpha = wellGlow;
            ctx.drawImage(wellEl, src[0] * R, src[1] * R, src[2] * R, src[3] * R, 0, 0, cv.width, cv.height);
        });
    };

    const pullNext = () => { if (next) next.style.marginTop = `-${stage.offsetHeight}px`; };
    let pinLen = 0;   // the pin's scroll length, px

    // The landing's slides, after arlink28.com: each runs SLIDE_MS while its
    // thumbnail's line fills, then the next background opens over it
    // through BANDS horizontal blinds, bottom band first, and its copy and
    // caption roll in. Only on the landing; going on, it's back to the
    // stairwell (it's what splits)
    const bgs    = [...root.querySelectorAll('[data-walk-bg]')];
    const thumbs = [...root.querySelectorAll('[data-walk-thumb]')];
    const bars   = thumbs.map((t) => t.querySelector('[data-walk-progress]'));
    const rolls  = [...root.querySelectorAll('[data-walk-roll]')];
    const pauseBtn = root.querySelector('[data-walk-pause]');
    let slide = 0, elapsed = 0, paused = false, blinds = null;

    const blindsMask = (t) => {
        const step = (BLINDS_MS - BAND_MS) / (BANDS - 1), h = 100 / BANDS, stops = [];
        for (let i = 0; i < BANDS; i++) {
            const a = i * h, f = a + h * easeOut(clamp((t - i * step) / BAND_MS, 0, 1));
            stops.push(`#000 ${a.toFixed(3)}%`, `#000 ${f.toFixed(3)}%`, `transparent ${f.toFixed(3)}%`, `transparent ${(a + h).toFixed(3)}%`);
        }
        return `linear-gradient(0deg, ${stops.join(', ')})`;
    };
    const setMask = (el, m) => { el.style.maskImage = m; el.style.webkitMaskImage = m; };

    // Rise from below: park an item under its mask before it comes on
    const rise = (el) => {
        el.style.transition = 'none';
        el.style.translate = '0 115%';
        void el.offsetWidth;
        el.style.transition = '';
        el.style.translate = '';
    };

    const showSlide = (next) => {
        if (next === slide) return;
        const was = slide;
        slides[next].classList.add('is-before');
        void slides[next].offsetWidth;   // park its rows below before they rise
        slides[next].classList.remove('is-before');
        slides[was].classList.remove('is-on');
        slides[next].classList.add('is-on');
        if (next > 0) slides[next].removeAttribute('aria-hidden');   // the h1 (slide 1) is never hidden
        if (was > 0) slides[was].setAttribute('aria-hidden', 'true');

        rolls.forEach((r) => [...r.children].forEach((item, n) => {
            item.classList.remove('is-prev');
            if (n === next) { rise(item); item.classList.add('is-on'); }
            else item.classList.remove('is-on');
            if (n === was) item.classList.add('is-prev');
        }));
        thumbs.forEach((t, n) => t.classList.toggle('is-on', n === next));
        bars.forEach((bar) => { bar.style.transform = 'scaleX(0)'; });

        if (blinds) { setMask(blinds.el, ''); blinds.under.classList.remove('is-prev'); }
        bgs.forEach((bg, n) => {
            bg.classList.toggle('is-on', n === next);
            bg.classList.toggle('is-prev', n === was);
        });
        setMask(bgs[next], blindsMask(0));
        blinds = { el: bgs[next], under: bgs[was], t0: performance.now() };

        slide = next;
        elapsed = 0;
        wakeWell();
    };

    const runSlides = (now, dt) => {
        if (blinds) {
            const t = now - blinds.t0;
            if (t >= BLINDS_MS) { setMask(blinds.el, ''); blinds.under.classList.remove('is-prev'); blinds = null; }
            else setMask(blinds.el, blindsMask(t));
        }
        if (slides.length < 2) return;
        if (last > LEAVE) { showSlide(0); return; }
        if (!paused) {
            elapsed += dt;
            if (elapsed >= SLIDE_MS) { showSlide((slide + 1) % slides.length); return; }
        }
        bars[slide].style.transform = `scaleX(${(elapsed / SLIDE_MS).toFixed(4)})`;
    };

    thumbs.forEach((t, n) => t.addEventListener('click', () => showSlide(n)));
    pauseBtn?.addEventListener('click', () => {
        paused = !paused;
        pauseBtn.setAttribute('aria-pressed', String(paused));
        pauseBtn.setAttribute('aria-label', paused ? 'Play slides' : 'Pause slides');
    });

    // The stairwell drifts down on its own while it's on screen (the same
    // loop runs the slides' clock and blinds)
    let drift = 0, raf = 0, prev = 0;
    const tick = (now) => {
        const dt = Math.min(50, now - prev);
        prev = now;
        drift += (dt / 1000) * DRIFT;
        drawWell(q);
        runSlides(now, dt);
        raf = 0;
        wakeWell();
    };
    const wakeWell = () => {
        if (!raf && seen && !document.hidden && (q < 1 || blinds)) {
            prev = performance.now();
            raf = requestAnimationFrame(tick);
        }
    };
    const drawWell = (p) => {
        if (p >= 1) return;
        const split = p >= SPLIT[0];   // from there, the slices carry it
        wellEl.style.opacity = split ? '0' : wellGlow.toFixed(3);
        const g = easeInOut(span(p, GLIDE));
        well.render(drift + FLOORS * span(p, FLY),
            lerp(WELL_AT[0][0], WELL_AT[1][0], g), lerp(WELL_AT[0][1], WELL_AT[1][1], g));
        if (split) paintSlices();
    };

    // The rooms are held where they'll be when the pin lets go (this has to
    // match the scroll exactly); the rest follows it a little behind
    const render = (p) => {
        if (next) next.style.transform = p < 1 ? `translate3d(0, ${(-(1 - p) * pinLen).toFixed(1)}px, 0)` : '';
        root.classList.toggle('is-walking', p > LEAVE);
        follow();
    };

    // The stairwell, the split and the flips follow the scroll a little
    // behind, so they glide rather than step
    let q = 0, qRaf = 0;
    const chase = () => {
        qRaf = 0;
        q += (last - q) * CHASE;
        // A jump (a link, a reload part way down, the hero off screen) lands at
        // once: gliding there would replay the whole split over the page
        if (Math.abs(last - q) < 0.0004 || Math.abs(last - q) > JUMP || !seen) q = last;
        renderSplit(q);
        drawWell(q);
        wakeWell();
        if (q !== last) qRaf = requestAnimationFrame(chase);
    };
    const follow = () => { if (!qRaf) qRaf = requestAnimationFrame(chase); };

    const renderSplit = (p) => {
        const split = p >= SPLIT[0] && CARDS.length > 0;

        // 0 — down the stairwell, its centre coming to the middle (drawWell)
        // 1 — the split: the frame, cut into a slice per card, the slices
        // drawing apart to the cards' boxes, the picture shrinking with them
        // (each keeps its own piece of it, centred, still going down), then
        // each turns its first half-flip…
        stage.classList.toggle('is-split', split);
        const e = easeInOut(span(p, SPLIT));
        const a = S.w / slices.length;
        slices.forEach((sl, i) => {
            const { el } = sl;
            const f = easeSine(span(p, FLIP(i)));
            const on = split && f < 0.5;
            el.style.visibility = on ? 'visible' : 'hidden';
            if (!on) return;
            const to = CARDS[i];
            const x = lerp(i * a, to.x, e), y = lerp(0, to.y, e);
            const w = lerp(a, to.w, e), h = lerp(S.h, to.h, e);
            const z = lerp(1, Math.min(1, to.w / a), e);   // never magnified (phones' cards are wider than their slices)
            el.style.width = `${w.toFixed(1)}px`;
            el.style.height = `${h.toFixed(1)}px`;
            el.style.borderRadius = `${(radius * e).toFixed(1)}px`;
            // the part of the stage it shows: its own piece, centred, at z
            sl.src = [(i + 0.5) * a - w / 2 / z, S.h / 2 - h / 2 / z, w / z, h / z];
            el.style.transform = `translate(${x.toFixed(1)}px, ${y.toFixed(1)}px) ${turnover(f)}`;
        });

        // 2 — …and its card turns the second half. Till then the cards are
        // hidden; the rooms are held where they'll be when the pin lets go
        cards.forEach((el, i) => {
            const f = easeSine(span(p, FLIP(i)));
            el.style.opacity = f > 0.5 || p >= 1 ? '' : '0';
            el.style.transform = f > 0.5 && f < 1 ? turnover(f) : '';
        });

        // Done: the stage is gone, and lets clicks through to the rooms
        stage.style.visibility = p >= 1 ? 'hidden' : '';
        root.style.pointerEvents = p >= 1 ? 'none' : '';   // its scroll wrapper still lies over the rooms
    };
    // A turn f (0–1) of the flip: the slice's half, then the card's, lifting
    // a touch towards you at the edge-on middle
    const turnover = (f) => `perspective(1600px) rotateY(${((f < 0.5 ? f : f - 1) * 180).toFixed(2)}deg) scale(${(1 + 0.05 * Math.sin(Math.PI * f)).toFixed(4)})`;

    new IntersectionObserver(([e]) => { seen = e.isIntersecting; render(last); }).observe(stage);
    document.addEventListener('visibilitychange', () => render(last));

    pullNext();
    measure();
    pinLen = Math.round(window.innerHeight * PIN);
    ScrollTrigger.addEventListener('refreshInit', pullNext);
    render(0);
    renderSplit(0);

    ScrollTrigger.create({
        trigger: root,
        start: 'top top',
        end: () => '+=' + Math.round(window.innerHeight * PIN),
        pin: stage,
        scrub: true,
        onUpdate: (self) => { last = self.progress; render(last); },
        onRefresh: (self) => { last = q = self.progress; pinLen = self.end - self.start; measure(); render(last); renderSplit(q); },
    });
}
