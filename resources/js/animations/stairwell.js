/**
 * The stairwell that never ends: a photo looking down an oval stairwell,
 * drawn inside itself — each copy F× smaller, centred on the well, its
 * edge feathered into the floor above — so zooming into the centre by a
 * factor of 1/F lands exactly where it started. `depth` counts floors
 * descended (any real number; up the stairs is negative).
 *
 * Any picture that looks down a well or a tunnel works: the page passes
 * what was measured on it (its centre, its step in, its outer loop); the
 * defaults are our stairwell's (images/walk/stairwell-2560.webp). walk.js
 * dims it on the navy with the canvas's opacity so the white copy reads.
 * A picture that shows its way in only once (a tunnel to one vanishing
 * point) can't be drawn inside itself: mode 'zoom' flies straight into its
 * centre instead, magnified as little as can be: from just over cover to
 * 1/f of that, where the next step (faded in from the middle, its border
 * feathered off screen) has grown to take over.
 */
const ZOOM_BLEED = 1.15;     // 'zoom': drawn this much over cover, so a step's feathered border is off screen
const ZOOM_BAND  = 0.06;     // …that border, as a share of each side
const WELL = [0.48, 0.465];   // the well's centre, as a share of the photo
const F = 0.55;              // one floor down, the stairwell is this much smaller
const LAYERS = 7;            // copies drawn, from a floor above the screen down
const EDGE = [0.44, 0.47];   // the outer loop's half-size, as a share of the photo
const FEATHER = 0.72;        // the copy is solid inside this share of that loop

export function createStairwell(canvas, src, { well = WELL, f = F, edge = EDGE, mode = 'nest' } = {}) {
    const ctx = canvas.getContext('2d');
    const img = new Image();
    img.decoding = 'async';
    img.src = src;

    let w = 0, h = 0, masked = null, faded = null;

    // The photo with its edge faded out round the outer loop, made once
    const makeMasked = () => {
        const c = document.createElement('canvas');
        c.width = img.naturalWidth;
        c.height = img.naturalHeight;
        const m = c.getContext('2d');
        m.drawImage(img, 0, 0);
        m.globalCompositeOperation = 'destination-in';
        const [cx, cy] = [well[0] * c.width, well[1] * c.height];
        const [rx, ry] = [edge[0] * c.width, edge[1] * c.height];
        m.translate(cx, cy);
        m.scale(1, ry / rx);
        const g = m.createRadialGradient(0, 0, 0, 0, 0, rx);
        g.addColorStop(0, '#000');
        g.addColorStop(FEATHER, '#000');
        g.addColorStop(1, 'rgba(0,0,0,0)');
        m.fillStyle = g;
        m.fillRect(-cx, -cy * (rx / ry), c.width, c.height * (rx / ry));
        return c;
    };

    // For 'zoom': the picture with its border feathered out
    const makeFaded = () => {
        const c = document.createElement('canvas');
        const W = (c.width = img.naturalWidth), H = (c.height = img.naturalHeight);
        const m = c.getContext('2d');
        m.drawImage(img, 0, 0);
        m.globalCompositeOperation = 'destination-out';
        const bx = W * ZOOM_BAND, by = H * ZOOM_BAND;
        const side = (x0, y0, x1, y1, rx, ry, rw, rh) => {
            const g = m.createLinearGradient(x0, y0, x1, y1);
            g.addColorStop(0, '#000');
            g.addColorStop(1, 'rgba(0,0,0,0)');
            m.fillStyle = g;
            m.fillRect(rx, ry, rw, rh);
        };
        side(0, 0, bx, 0, 0, 0, bx, H);
        side(W, 0, W - bx, 0, W - bx, 0, bx, H);
        side(0, 0, 0, by, 0, 0, W, by);
        side(0, H, 0, H - by, 0, H - by, W, by);
        return c;
    };

    return {
        get ready() { return img.complete && img.naturalWidth > 0; },

        resize(width, height) {
            w = width;
            h = height;
            const dpr = Math.min(window.devicePixelRatio || 1, 2);
            canvas.width = Math.round(w * dpr);
            canvas.height = Math.round(h * dpr);
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        },

        /** depth: floors descended; ax, ay: where the well's centre sits (px).
            Drawn opaque: dim it with the canvas's own opacity */
        render(depth, ax, ay) {
            ctx.clearRect(0, 0, w, h);
            if (!this.ready) return;
            masked ??= makeMasked();

            const iw = img.naturalWidth, ih = img.naturalHeight;
            // the smallest scale at which the photo still covers the screen from (ax, ay)
            const cover = Math.max(
                ax / (well[0] * iw), (w - ax) / ((1 - well[0]) * iw),
                ay / (well[1] * ih), (h - ay) / ((1 - well[1]) * ih),
            );
            const floor = Math.floor(depth), frac = depth - floor;
            const place = (source, n, a) => {
                const s = cover * Math.pow(f, n - frac);
                ctx.globalAlpha = a;
                ctx.drawImage(source, ax - well[0] * iw * s, ay - well[1] * ih * s, iw * s, ih * s);
            };

            if (mode === 'zoom') {
                // this step growing from just over cover to 1/f of it, and the
                // next fading in over it from the middle, to take over at that size
                faded ??= makeFaded();
                const t = frac * frac * (3 - 2 * frac);
                const at = (source, s, a) => {
                    ctx.globalAlpha = a;
                    ctx.drawImage(source, ax - well[0] * iw * s, ay - well[1] * ih * s, iw * s, ih * s);
                };
                ctx.imageSmoothingQuality = 'high';
                at(img, cover * ZOOM_BLEED * Math.pow(f, -frac), 1);
                at(faded, cover * ZOOM_BLEED * Math.pow(f, 1 - frac), t);
                ctx.globalAlpha = 1;
                return;
            }

            // a floor above, whole; then this floor, its feathered edge
            // filling in as it grows to replace the one above; then the floors below
            place(img, -1, 1);
            place(masked, 0, 1);
            place(img, 0, frac);
            for (let n = 1; n < LAYERS; n++) place(masked, n, 1);
            ctx.globalAlpha = 1;
        },
    };
}
