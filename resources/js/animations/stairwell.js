/**
 * The stairwell that never ends: a photo looking down an oval stairwell,
 * drawn inside itself — each copy F× smaller, centred on the well, its
 * edge feathered into the floor above — so zooming into the centre by a
 * factor of 1/F lands exactly where it started. `depth` counts floors
 * descended (any real number; up the stairs is negative).
 *
 * The photo is toned to the palette offline (images/walk/stairwell-*.webp).
 * ⚠ Stand-in: Wolfgang Weiser on Unsplash — swap for a real stairwell or
 * the venue's own optics before launch.
 */

const WELL = [0.49, 0.48];   // the well's centre, as a share of the photo
const F = 0.6;               // one floor down, the stairwell is this much smaller
const LAYERS = 7;            // copies drawn, from a floor above the screen down
const EDGE = [0.44, 0.47];   // the outer loop's half-size, as a share of the photo
const FEATHER = 0.72;        // the copy is solid inside this share of that loop

export function createStairwell(canvas, src) {
    const ctx = canvas.getContext('2d');
    const img = new Image();
    img.decoding = 'async';
    img.src = src;

    let w = 0, h = 0, masked = null;

    // The photo with its edge faded out round the outer loop, made once
    const makeMasked = () => {
        const c = document.createElement('canvas');
        c.width = img.naturalWidth;
        c.height = img.naturalHeight;
        const m = c.getContext('2d');
        m.drawImage(img, 0, 0);
        m.globalCompositeOperation = 'destination-in';
        const [cx, cy] = [WELL[0] * c.width, WELL[1] * c.height];
        const [rx, ry] = [EDGE[0] * c.width, EDGE[1] * c.height];
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
                ax / (WELL[0] * iw), (w - ax) / ((1 - WELL[0]) * iw),
                ay / (WELL[1] * ih), (h - ay) / ((1 - WELL[1]) * ih),
            );
            const floor = Math.floor(depth), frac = depth - floor;
            const place = (source, n, a) => {
                const s = cover * Math.pow(F, n - frac);
                ctx.globalAlpha = a;
                ctx.drawImage(source, ax - WELL[0] * iw * s, ay - WELL[1] * ih * s, iw * s, ih * s);
            };

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
