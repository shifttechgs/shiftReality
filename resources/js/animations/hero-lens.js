import { isTouchDevice } from '../utils/motion.js';

/**
 * Funhouse Lens — a small curved glass lens over the hero photo.
 *
 * A WebGL canvas sits between the slides and the scrim, so it bends the
 * photo but never the headline. Each frame it samples the active slide's
 * <img>, matching its object-fit, slow zoom and parallax via
 * getBoundingClientRect, and only draws inside the lens (scissored).
 * It appears only while a mouse is over the hero and fades when it leaves:
 * no idle drift, and nothing on touch devices.
 */

const VERT = `
attribute vec2 a_pos;
void main() { gl_Position = vec4(a_pos, 0.0, 1.0); }
`;

const FRAG = `
precision mediump float;
uniform sampler2D u_tex;
uniform vec2  u_res;        // canvas size, device px
uniform vec2  u_mouse;      // lens centre, device px (top-left origin)
uniform float u_r;          // lens radius, device px
uniform vec2  u_imgOrigin;  // displayed image top-left, device px
uniform vec2  u_imgSize;    // displayed image size, device px
uniform float u_alpha;

vec2 warp(vec2 d, float t, float a) { return u_mouse + d * pow(t, a); }
vec4 tap(vec2 p) { return texture2D(u_tex, clamp((p - u_imgOrigin) / u_imgSize, 0.0, 1.0)); }

void main() {
    vec2 p = vec2(gl_FragCoord.x, u_res.y - gl_FragCoord.y);
    vec2 d = p - u_mouse;
    float t = length(d) / u_r;
    if (t > 1.0) discard;

    // magnify toward the centre; each channel bends a touch differently (glass)
    vec3 col = vec3(
        tap(warp(d, t, 0.84)).r,
        tap(warp(d, t, 0.90)).g,
        tap(warp(d, t, 0.96)).b
    );

    // darker rim + thin highlight, so it reads as a piece of glass
    col *= 1.0 - smoothstep(0.82, 1.0, t) * 0.4;
    col += vec3(0.24, 0.45, 0.96) * smoothstep(0.955, 0.975, t) * (1.0 - smoothstep(0.975, 0.995, t)) * 0.55;

    gl_FragColor = vec4(col, (1.0 - smoothstep(0.99, 1.0, t)) * u_alpha);
}
`;

export function initHeroLens() {
    if (isTouchDevice()) return;   // no cursor to follow

    const stage = document.querySelector('[data-hero-slider]');
    const slidesEl = stage?.querySelector('.wh-hero__slides');
    if (!stage || !slidesEl) return;

    const canvas = document.createElement('canvas');
    canvas.className = 'wh-hero__lens';
    canvas.setAttribute('aria-hidden', 'true');
    const gl = canvas.getContext('webgl', { alpha: true, premultipliedAlpha: false, antialias: false });
    if (!gl) return;   // no WebGL: the hero is unchanged
    slidesEl.after(canvas);

    // ── GL setup ──
    const shader = (type, src) => {
        const s = gl.createShader(type);
        gl.shaderSource(s, src);
        gl.compileShader(s);
        return s;
    };
    const prog = gl.createProgram();
    gl.attachShader(prog, shader(gl.VERTEX_SHADER, VERT));
    gl.attachShader(prog, shader(gl.FRAGMENT_SHADER, FRAG));
    gl.linkProgram(prog);
    if (!gl.getProgramParameter(prog, gl.LINK_STATUS)) { canvas.remove(); return; }
    gl.useProgram(prog);

    gl.bindBuffer(gl.ARRAY_BUFFER, gl.createBuffer());
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
    const aPos = gl.getAttribLocation(prog, 'a_pos');
    gl.enableVertexAttribArray(aPos);
    gl.vertexAttribPointer(aPos, 2, gl.FLOAT, false, 0, 0);

    const u = {};
    ['u_res', 'u_mouse', 'u_r', 'u_imgOrigin', 'u_imgSize', 'u_alpha']
        .forEach((name) => { u[name] = gl.getUniformLocation(prog, name); });

    gl.enable(gl.SCISSOR_TEST);
    gl.clearColor(0, 0, 0, 0);

    // One texture per slide, uploaded once its image has decoded
    const imgs = [...stage.querySelectorAll('.wh-hero__slide-img')];
    const textures = new Map();
    const upload = (img) => {
        if (textures.has(img) || !img.complete || !img.naturalWidth) return;
        const tex = gl.createTexture();
        gl.bindTexture(gl.TEXTURE_2D, tex);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, img);
        textures.set(img, tex);
    };
    imgs.forEach((img) => {
        upload(img);
        img.addEventListener('load', () => upload(img));
    });

    // ── Size ──
    let W = 0, H = 0, dpr = 1, R = 0;
    const resize = () => {
        const rect = stage.getBoundingClientRect();
        dpr = Math.min(1.5, window.devicePixelRatio || 1);
        W = canvas.width  = Math.max(1, Math.round(rect.width * dpr));
        H = canvas.height = Math.max(1, Math.round(rect.height * dpr));
        R = Math.min(90, Math.max(60, Math.min(rect.width, rect.height) * 0.1)) * dpr;   // ~120–180px across
        gl.viewport(0, 0, W, H);
    };
    resize();
    new ResizeObserver(resize).observe(stage);

    // ── Steering: the mouse only ──
    const pos = { x: 0, y: 0 };
    const target = { x: 0, y: 0 };
    let pointerInside = false;

    const fromEvent = (e) => {
        const r = stage.getBoundingClientRect();
        target.x = (e.clientX - r.left) * dpr;
        target.y = (e.clientY - r.top) * dpr;
    };
    stage.addEventListener('pointermove', (e) => { if (e.pointerType === 'mouse') fromEvent(e); }, { passive: true });
    stage.addEventListener('pointerenter', (e) => {
        if (e.pointerType !== 'mouse') return;
        fromEvent(e);
        pos.x = target.x;   // appear at the cursor, don't glide in from the last spot
        pos.y = target.y;
        pointerInside = true;
        document.body.classList.add('is-lensing');   // the lens replaces the cursor ring here
    });
    stage.addEventListener('pointerleave', () => {
        pointerInside = false;
        document.body.classList.remove('is-lensing');
    });

    // ── Frame ──
    let visible = true;
    new IntersectionObserver(([entry]) => { visible = entry.isIntersecting; }).observe(stage);

    let activeImg = null, switchedAt = 0, alpha = 0;

    const frame = (now) => {
        requestAnimationFrame(frame);
        if (!visible || document.hidden) return;

        // follow the slider: fade in on each new slide once its wipe is under way
        const img = stage.querySelector('.wh-hero__slide.is-active .wh-hero__slide-img');
        if (img !== activeImg) { activeImg = img; switchedAt = now; }
        const tex = textures.get(activeImg);

        // follow the cursor with a slight lag
        pos.x += (target.x - pos.x) * 0.2;
        pos.y += (target.y - pos.y) * 0.2;

        const show = tex && pointerInside;
        const wipe = Math.min(1, (now - switchedAt) / 1100);
        alpha += ((show ? wipe : 0) - alpha) * 0.12;

        gl.scissor(0, 0, W, H);
        gl.clear(gl.COLOR_BUFFER_BIT);
        if (!tex || alpha < 0.01) return;

        // where the active image is actually drawn (object-fit: cover + its transforms)
        const sr = stage.getBoundingClientRect();
        const ir = activeImg.getBoundingClientRect();
        const s  = Math.max(ir.width / activeImg.naturalWidth, ir.height / activeImg.naturalHeight);
        const dw = activeImg.naturalWidth * s, dh = activeImg.naturalHeight * s;

        gl.bindTexture(gl.TEXTURE_2D, tex);
        gl.uniform2f(u.u_res, W, H);
        gl.uniform2f(u.u_mouse, pos.x, pos.y);
        gl.uniform1f(u.u_r, R);
        gl.uniform2f(u.u_imgOrigin, (ir.left - sr.left + (ir.width - dw) / 2) * dpr, (ir.top - sr.top + (ir.height - dh) / 2) * dpr);
        gl.uniform2f(u.u_imgSize, dw * dpr, dh * dpr);
        gl.uniform1f(u.u_alpha, alpha);

        // only shade the lens's square
        const x0 = Math.max(0, Math.floor(pos.x - R)), y0 = Math.max(0, Math.floor(H - pos.y - R));
        gl.scissor(x0, y0, Math.ceil(R * 2) + 1, Math.ceil(R * 2) + 1);
        gl.drawArrays(gl.TRIANGLES, 0, 3);
    };
    requestAnimationFrame(frame);
}
