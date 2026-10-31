import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { clamp, lerp } from '../utils/motion.js';
import { createStairwell } from './stairwell.js';

/**
 * The hero, as a walk in. You land on two slides that take turns (after
 * arlink28.com: blinds, rolling captions, thumbnails with progress lines,
 * pause), each with its own copy: a stairwell that never ends
 * (stairwell.js), drifting slowly down it, and a mirrored hall. Scrolling
 * pins the stage, lifts the copy away and takes you down floor after floor
 * (scrolling up climbs back); out of the well comes the logo's impossible
 * triangle as a real 3D object (impossible.js) — three beams that plainly
 * don't meet. The camera turns until, from the one angle where it works,
 * they close into the logo; the flat mark takes over, its hole lights up
 * on the reception, and the camera flies through it, pushes in on the
 * desk, then through the desk sign's triangle onto the illusion entrance —
 * and on into the rooms. Each window is a clip-path triangle that tracks
 * the hole of the spinning mark exactly, so the photo always shows
 * *through* the A.
 *
 * Reduced motion / no JS: no pin — the headline, then the two photos and
 * their copy side by side (see .walk:not(.walk--live)).
 */

// The flat triangle (the inline svg.walk__mark, 280×242.48): its hole, in its own units
const MARK = { w: 280, h: 242.48 };
const HOLE = [[140, 79.8], [76.79, 189.28], [203.21, 189.28]];
const HOLE_C = [140, (79.8 + 189.28 + 189.28) / 3];   // centroid: the mark turns about it
const HOLE_R = Math.hypot(HOLE[0][0] - HOLE_C[0], HOLE[0][1] - HOLE_C[1]);
const HOLE_W = HOLE[2][0] - HOLE[1][0];
// Its three faces (the 3D object's, by the axis they face; see impossible.js)
const FACES = { x: '#3D74F5', y: '#F1F5FE', z: '#1855E8' };

const IMG = { w: 764, h: 1024 };     // both photos are this shape (±2px)
const DESK_SIGN = [0.849, 0.583];    // the triangle on the reception desk…
const SIGN_W = 0.088;                // …and its width, as a share of the photo's
const SIGN_HOLE = 76 / 270;          // the printed logo's hole, as a share of its width
const FOCUS = [0.52, 0.43];          // what the mark's window frames first: the desk and its people
const FOCUS_K = 0.36;                // …with the photo at this scale
const CORRIDOR  = [0.33, 0.6];       // the striped steps into the rooms

// Scroll beats (0–1 through the pin)
const LEAVE  = 0.015;          // the landing headline lifts away
const FLY    = [0, 0.22];      // down the stairwell…
const DIM    = [0.15, 0.25];   // …and it goes out
const EMERGE = [0.13, 0.24];   // the beams come out of the far end…
const TURN   = [0.13, 0.4];    // …and the camera turns into the one view
const SWAP   = [0.4, 0.43];    // the flat mark takes over; its hole lights up
const OPEN_1 = [0.46, 0.64];   // the A opens onto reception
const PUSH_1 = [0.62, 0.76];   // walk up to the desk
const OPEN_2 = [0.74, 0.89];   // the desk's A opens onto the rooms
const PUSH_2 = [0.87, 1];      // walk on into the corridor
const PHOTO  = 0.58;           // the reception has the screen: shade + copy
const SWITCH = 0.88;           // the copy moves on to the rooms (once the desk's A has opened)
const SOLID  = [0.29, 0.53];   // the impossible triangle's own line of copy
const TURN_FROM = { yaw: 1.2, pitch: -0.55 };   // radians off the one view, as it emerges
const FLOORS = 2.2;            // floors descended with the scroll
const DRIFT  = 1 / 30;         // …and on their own, behind the copy (floors per second)
const SLIDE_MS  = 7000;        // each landing slide's turn
const BANDS     = 30;          // the switch: horizontal blinds…
const BAND_MS   = 300;         // …each opening upwards this long…
const BLINDS_MS = 600;         // …the top one done this long after the bottom one starts
const WELL_GLOW = [1, 0.85];   // the stairwell's opacity on the navy (wide, narrow: the copy covers more of it)

const span = (p, [a, b]) => clamp((p - a) / (b - a), 0, 1);
const easeInOut = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);
const easeOut = (t) => 1 - Math.pow(1 - t, 3);

export function initWalk() {
    const root = document.querySelector('[data-walk]');
    // walk--live is set in app.js's critical path, unless motion is reduced
    if (!root || !root.classList.contains('walk--live')) return;

    const stage    = root.querySelector('.walk__stage');
    const frame    = root.querySelector('[data-walk-frame]');
    const [sceneA, sceneB] = root.querySelectorAll('[data-walk-scene]');
    const [imgA, imgB]     = [sceneA, sceneB].map((s) => s.querySelector('img'));
    const mark     = root.querySelector('[data-walk-mark]');
    const solidEl  = root.querySelector('[data-walk-solid]');
    const chapters = [...root.querySelectorAll('[data-walk-chapter]')];
    const slides   = [...root.querySelectorAll('[data-walk-slide]')];

    let last = 0, seen = true;

    // The stairwell (the large photo everywhere: phones magnify it most)
    const wellEl = root.querySelector('[data-walk-well]');
    const well = createStairwell(wellEl, wellEl.dataset.src);

    // The 3D object loads on its own (three.js stays off every other page)
    let solid = null;
    import('./impossible.js').then(({ createImpossible }) => {
        solid = createImpossible(solidEl, FACES);
        solid.resize(F.w, F.h);
        render(last);
    });

    // Geometry, re-measured on refresh: the frame inside the stage, and where
    // a point of the (object-fit: cover) photo lands inside the frame
    let F = { x: 0, y: 0, w: 1, h: 1 };
    let REST = { x: 0, y: 0, k: 0.5 };   // where the triangle comes to rest, in frame coords
    let POS = [0.5, 0.5];                // each photo's horizontal object-position
    let WELL_AT = [[0, 0], [0, 0]];      // the well's centre: landing → where the triangle rests
    let wellGlow = WELL_GLOW[0];
    const measure = () => {
        const s = stage.getBoundingClientRect();
        const f = frame.getBoundingClientRect();
        F = { x: f.left - s.left, y: f.top - s.top, w: f.width, h: f.height };

        // Tall screens crop the photos' sides: slide each one just enough
        // that the point we walk towards stays well inside the screen
        POS = [fitPos(DESK_SIGN[0], 0.22), fitPos(CORRIDOR[0], 0.3)];
        imgA.style.objectPosition = `${(POS[0] * 100).toFixed(2)}% 50%`;
        imgB.style.objectPosition = `${(POS[1] * 100).toFixed(2)}% 50%`;

        // The triangle settles right of centre, clear of the copy at the
        // bottom left; on narrow screens, centred above the copy
        const wide = s.width > 900;
        const k = wide
            ? Math.min(s.width * 0.34 / MARK.w, s.height * 0.5 / MARK.h)
            : Math.min(s.width * 0.7 / MARK.w, s.height * 0.34 / MARK.h);
        const box = wide ? [s.width * 0.6, s.height * 0.5] : [s.width * 0.5, s.height * 0.38];
        REST = { x: box[0] - F.x, y: box[1] + (HOLE_C[1] - MARK.h / 2) * k - F.y, k };

        // The well's centre sits up and to the right of the copy on landing
        // (high up on phones), and comes to the triangle's place as you go
        // down, so the beams come up out of it
        WELL_AT = [wide ? [s.width * 0.62, s.height * 0.42] : [s.width * 0.5, s.height * 0.3], [REST.x + F.x, REST.y + F.y]];
        wellGlow = WELL_GLOW[wide ? 0 : 1];

        well.resize(s.width, s.height);
        if (solid) solid.resize(F.w, F.h);
    };
    const cover = () => Math.max(F.w / IMG.w, F.h / IMG.h);
    const onPhoto = ([u, v], pos = 0.5) => {
        const s = cover();
        return [(F.w - IMG.w * s) * pos + u * IMG.w * s, (F.h - IMG.h * s) / 2 + v * IMG.h * s];
    };
    const fitPos = (u, keep) => {
        const W = IMG.w * cover();
        if (W - F.w < 1) return 0.5;
        const at = (pos) => (F.w - W) * pos + u * W;
        const lo = keep * F.w, hi = (1 - keep) * F.w;
        let pos = 0.5;
        if (at(pos) > hi) pos = (hi - u * W) / (F.w - W);
        if (at(pos) < lo) pos = (lo - u * W) / (F.w - W);
        return clamp(pos, 0, 1);
    };

    // A triangle window: the mark's hole at scale k, turned θ, centred (cx, cy) in the frame
    const windowAt = (cx, cy, k, deg) => {
        const a = (deg * Math.PI) / 180, cos = Math.cos(a), sin = Math.sin(a);
        return 'polygon(' + HOLE.map(([x, y]) => {
            const dx = (x - HOLE_C[0]) * k, dy = (y - HOLE_C[1]) * k;
            return `${(cx + dx * cos - dy * sin).toFixed(1)}px ${(cy + dx * sin + dy * cos).toFixed(1)}px`;
        }).join(',') + ')';
    };
    const placeMark = (cx, cy, k, deg, alpha) => {
        mark.style.opacity = alpha.toFixed(3);
        mark.style.transform =
            `translate(${(F.x + cx - HOLE_C[0]).toFixed(1)}px, ${(F.y + cy - HOLE_C[1]).toFixed(1)}px) rotate(${deg.toFixed(2)}deg) scale(${k.toFixed(4)})`;
    };

    // Opening: the hole grows exponentially (it reads as flying at it) until
    // its inscribed circle clears the frame's corners
    const opening = (t, k0) => {
        const kEnd = (2 * Math.hypot(F.w, F.h)) / (2 * HOLE_R) * 1.08;   // inradius = R/2 ≥ half-diagonal
        const e = easeInOut(t);
        return { k: k0 * Math.pow(kEnd / k0, e), e };
    };

    let chapter = -1;
    const setChapter = (i) => {
        if (i === chapter) return;
        chapter = i;
        chapters.forEach((c, n) => c.classList.toggle('is-on', n === i));
        const eyebrow = chapters[i]?.querySelector('[data-decode]');
        if (eyebrow) decode(eyebrow);
    };

    // The landing's slides, after arlink28.com: each runs SLIDE_MS while its
    // thumbnail's line fills, then the next background opens over it
    // through BANDS horizontal blinds, bottom band first, and its copy and
    // caption roll in. Only on the landing; going on, it's back to the
    // stairwell (the beams come up out of it)
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

    // The stairwell drifts down on its own while it's on screen and lit
    // (the same loop runs the slides' clock and blinds)
    let drift = 0, raf = 0, prev = 0;
    const tick = (now) => {
        const dt = Math.min(50, now - prev);
        prev = now;
        drift += (dt / 1000) * DRIFT;
        drawWell(last);
        runSlides(now, dt);
        raf = 0;
        wakeWell();
    };
    const wakeWell = () => {
        if (!raf && seen && !document.hidden && (last < DIM[1] || blinds)) {
            prev = performance.now();
            raf = requestAnimationFrame(tick);
        }
    };
    const drawWell = (p) => {
        const dim = 1 - span(p, DIM);
        wellEl.style.opacity = (wellGlow * dim).toFixed(3);
        if (dim <= 0) return;
        const g = easeInOut(span(p, FLY));
        well.render(drift + FLOORS * g,
            lerp(WELL_AT[0][0], WELL_AT[1][0], g), lerp(WELL_AT[0][1], WELL_AT[1][1], g));
    };

    const render = (p) => {
        const mid = [F.w / 2, F.h / 2];

        // 0 — down the stairwell, floor after floor, until the beams come up out of it
        drawWell(p);
        wakeWell();

        // 1 — the beams, turning into the one view; the flat mark takes over there
        const swap = easeInOut(span(p, SWAP));
        const em = easeOut(span(p, EMERGE));
        const turn = 1 - easeOut(span(p, TURN));
        solidEl.style.opacity = (em * (1 - swap)).toFixed(3);
        if (solid && em > 0 && swap < 1) {
            solid.render({
                x: REST.x, y: REST.y, k: REST.k * (0.55 + 0.45 * em),
                yaw: TURN_FROM.yaw * turn, pitch: TURN_FROM.pitch * turn,
            });
        }

        // 2 — the A opens onto the reception
        const t1 = span(p, OPEN_1);
        const k1 = REST.k;
        const o1 = opening(t1, k1);
        const spin1 = -360 * (1 - o1.e);              // one turn as it opens, landing upright
        const c1 = [lerp(REST.x, mid[0], o1.e), lerp(REST.y, mid[1], o1.e)];
        sceneA.style.opacity = swap.toFixed(3);
        sceneA.style.clipPath = t1 < 1 ? windowAt(c1[0], c1[1], o1.k, spin1) : 'none';

        // 3 — walk up to the desk. At first the photo is small and framed in
        // the window (the desk's people in the hole); opening, it grows to
        // the screen ahead of the window, then pushes in on the desk
        const desk  = onPhoto(DESK_SIGN, POS[0]);
        const focus = onPhoto(FOCUS, POS[0]);
        const z1 = 1 + 0.55 * easeInOut(span(p, PUSH_1)) + 0.9 * easeInOut(span(p, OPEN_2));
        const g  = 1 - Math.pow(1 - o1.e, 2);
        const m  = lerp(FOCUS_K, z1, g);
        const at = [lerp(REST.x, desk[0], g), lerp(REST.y, desk[1], g)];
        const of = [lerp(focus[0], desk[0], g), lerp(focus[1], desk[1], g)];
        imgA.style.transformOrigin = '0 0';
        imgA.style.transform = `translate(${(at[0] - m * of[0]).toFixed(1)}px, ${(at[1] - m * of[1]).toFixed(1)}px) scale(${m.toFixed(4)})`;

        // 4 — the desk sign's A opens onto the rooms, drifting to the centre as it grows
        const t2 = span(p, OPEN_2);
        const signK = (SIGN_W * SIGN_HOLE * IMG.w * cover() * 1.55) / HOLE_W;   // its hole on the printed sign's, once we're at the desk
        const o2 = opening(t2, signK);
        const spin2 = 360 * o2.e;                     // one turn the other way
        const c2 = [lerp(desk[0], mid[0], o2.e), lerp(desk[1], mid[1], o2.e)];
        sceneB.style.visibility = t2 > 0 ? 'visible' : 'hidden';
        sceneB.style.clipPath = t2 < 1 ? windowAt(c2[0], c2[1], o2.k, spin2) : 'none';

        // 5 — on into the corridor
        const hall = onPhoto(CORRIDOR, POS[1]);
        const z2 = 1.12 - 0.12 * easeInOut(t2) + 0.4 * easeInOut(span(p, PUSH_2));
        imgB.style.transformOrigin = `${hall[0]}px ${hall[1]}px`;
        imgB.style.transform = `scale(${z2.toFixed(4)})`;

        // The flat mark rides whichever window is opening, fading as it
        // passes the camera (a flat logo blown up past ~3× only goes soft)
        const fade = (o, k0) => clamp(1 - (Math.log(o.k / k0) / Math.log(3.2)), 0, 1);
        if (t2 > 0) placeMark(c2[0], c2[1], o2.k, spin2, fade(o2, signK) * clamp(t2 * 6, 0, 1));
        else        placeMark(c1[0], c1[1], o1.k, spin1, fade(o1, k1) * swap);

        root.classList.toggle('is-walking', p > LEAVE);
        root.classList.toggle('is-photo', p > PHOTO);
        setChapter(p > SOLID[0] && p < SOLID[1] ? 0 : p <= PHOTO ? -1 : p < SWITCH ? 1 : 2);
    };

    new IntersectionObserver(([e]) => { seen = e.isIntersecting; render(last); }).observe(stage);
    document.addEventListener('visibilitychange', () => render(last));

    measure();
    render(0);

    ScrollTrigger.create({
        trigger: root,
        start: 'top top',
        end: () => '+=' + Math.round(window.innerHeight * 5.2),
        pin: stage,
        scrub: true,
        onUpdate: (self) => { last = self.progress; render(last); },
        onRefresh: () => { measure(); render(last); },
    });
}

/**
 * Monolith-style label decode: resolves left to right in 400ms, trailing a
 * short band of random capitals. The final text is restored either way.
 */
function decode(el) {
    const text = el.dataset.decode || (el.dataset.decode = el.textContent);
    const ABC = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const t0 = performance.now(), dur = 400, band = 3.6;
    clearTimeout(el._decodeEnd);
    const step = (now) => {
        const done = Math.min(1, (now - t0) / dur) * text.length;
        let out = '';
        for (let i = 0; i < text.length; i++) {
            if (i < done || text[i] === ' ') out += text[i];
            else if (i < done + band) out += ABC[(Math.random() * 26) | 0];
        }
        el.textContent = out;
        if (done < text.length) el._decodeRaf = requestAnimationFrame(step);
    };
    cancelAnimationFrame(el._decodeRaf);
    el._decodeRaf = requestAnimationFrame(step);
    el._decodeEnd = setTimeout(() => { cancelAnimationFrame(el._decodeRaf); el.textContent = text; }, dur + 500);
}
