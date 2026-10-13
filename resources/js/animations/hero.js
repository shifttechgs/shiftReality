import { gsap } from 'gsap';
import { prefersReducedMotion, lerp } from '../utils/motion.js';
import lenis from '../utils/lenis.js';

const SCRAMBLE = '!<>_\\/[]{}=+*^?#%@0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

function randChar() {
    return SCRAMBLE[Math.floor(Math.random() * SCRAMBLE.length)];
}

/**
 * Replace a .hero__line's text with per-character <span> pairs.
 * Each char gets an overflow-hidden wrapper for the clip-reveal animation.
 */
function splitChars(el) {
    const text = el.textContent;
    el.innerHTML = '';

    for (const ch of text) {
        if (ch === ' ') {
            const sp = document.createElement('span');
            sp.className = 'hero__char-space';
            sp.textContent = ' ';
            el.appendChild(sp);
        } else {
            const wrap = document.createElement('span');
            wrap.className = 'hero__char-wrap';

            const inner = document.createElement('span');
            inner.className = 'hero__char';
            inner.dataset.final = ch;
            inner.textContent = ch;

            wrap.appendChild(inner);
            el.appendChild(wrap);
        }
    }
}

/**
 * Run a text-scramble on a char element, resolving to its final value
 * after `frames` animation frames. Starts after `delayMs` ms.
 */
function scrambleChar(el, delayMs) {
    const final = el.dataset.final;
    setTimeout(() => {
        let frame = 0;
        const tick = () => {
            if (++frame >= 14) {
                el.textContent = final;
            } else {
                el.textContent = randChar();
                requestAnimationFrame(tick);
            }
        };
        requestAnimationFrame(tick);
    }, delayMs);
}

/**
 * Perspective-grid canvas — cursor-reactive depth illusion.
 */
class HeroCanvas {
    constructor(canvas) {
        this.canvas  = canvas;
        this.ctx     = canvas.getContext('2d');
        this.mouse   = { x: 0.5, y: 0.5 };
        this.target  = { x: 0.5, y: 0.5 };
        this.raf     = null;
        this.active  = true;
        this.lastTs  = 0;
        this.reduced = prefersReducedMotion();

        this.resize();
        this.bindEvents();
        this.watchVisibility();
        this.animate();
    }

    resize() {
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        this.canvas.width  = window.innerWidth  * dpr;
        this.canvas.height = window.innerHeight * dpr;
        this.canvas.style.width  = `${window.innerWidth}px`;
        this.canvas.style.height = `${window.innerHeight}px`;
        this.ctx.scale(dpr, dpr);
    }

    bindEvents() {
        window.addEventListener('resize', () => this.resize(), { passive: true });
        if (!this.reduced) {
            document.addEventListener('mousemove', (e) => {
                this.target.x = e.clientX / window.innerWidth;
                this.target.y = e.clientY / window.innerHeight;
            }, { passive: true });
        }
    }

    watchVisibility() {
        const observer = new IntersectionObserver(
            ([entry]) => { this.active = entry.isIntersecting; },
            { threshold: 0 }
        );
        observer.observe(this.canvas);
    }

    draw() {
        const { ctx } = this;
        const W = window.innerWidth;
        const H = window.innerHeight;
        ctx.clearRect(0, 0, W, H);

        if (!this.reduced) {
            this.mouse.x = lerp(this.mouse.x, this.target.x, 0.04);
            this.mouse.y = lerp(this.mouse.y, this.target.y, 0.04);
        }

        const vpX  = W * (0.5 + (this.mouse.x - 0.5) * 0.12);
        const vpY  = H * (0.42 + (this.mouse.y - 0.5) * 0.08);
        const maxR = Math.hypot(W, H) * 0.75;

        const numLines = 20;
        ctx.lineWidth = 0.5;
        for (let i = 0; i < numLines; i++) {
            const angle = (i / numLines) * Math.PI * 2;
            const alpha = 0.035 + Math.sin(i * 1.3) * 0.01;
            ctx.strokeStyle = `rgba(228, 184, 76, ${alpha})`;
            ctx.beginPath();
            ctx.moveTo(vpX, vpY);
            ctx.lineTo(vpX + Math.cos(angle) * maxR, vpY + Math.sin(angle) * maxR);
            ctx.stroke();
        }

        const numRings = 9;
        for (let r = 1; r <= numRings; r++) {
            const t      = r / numRings;
            const radius = t * maxR * 0.55;
            const scaleY = 0.28 + t * 0.25 + this.mouse.y * 0.06;
            const alpha  = (1 - t) * 0.06;

            ctx.strokeStyle = `rgba(228, 184, 76, ${alpha})`;
            ctx.lineWidth   = 0.5;
            ctx.beginPath();
            ctx.save();
            ctx.translate(vpX, vpY);
            ctx.scale(1, scaleY);
            ctx.arc(0, 0, radius, 0, Math.PI * 2);
            ctx.restore();
            ctx.stroke();
        }
    }

    animate(ts = 0) {
        this.raf = requestAnimationFrame((ts) => this.animate(ts));
        if (!this.active) return;           // paused when hero off-screen
        if (ts - this.lastTs < 33) return;  // cap at ~30fps
        this.lastTs = ts;
        this.draw();
    }

    destroy() {
        cancelAnimationFrame(this.raf);
    }
}

/**
 * Phase 1: set up canvas + split chars + set initial hidden states.
 * Safe to call immediately on DOMContentLoaded, even before intro completes.
 */
export function initHeroCanvas() {
    const canvas = document.getElementById('hero-canvas');
    if (canvas) new HeroCanvas(canvas);

    // Scroll progress counter
    const progressEl = document.querySelector('.hero__progress-value');
    if (progressEl) {
        lenis.on('scroll', ({ progress }) => {
            progressEl.textContent = String(Math.round(progress * 100)).padStart(2, '0');
        });
    }

    if (prefersReducedMotion()) return;

    // Split headline lines into individual character spans
    document.querySelectorAll('.hero__line').forEach(splitChars);

    // Hide all hero animated elements — revealHero() will bring them in
    gsap.set([...document.querySelectorAll('.hero__char')], { y: '110%' });
    gsap.set(['.hero__eyebrow', '.hero__footer', '.hero__marquee'], { opacity: 0 });
}

/**
 * Phase 2: run the hero reveal timeline.
 * Called by initIntro's onComplete callback so it fires the moment
 * the intro screen finishes tearing open.
 */
export function revealHero() {
    if (prefersReducedMotion()) {
        document.querySelectorAll('.hero__line, .hero__eyebrow, .hero__footer, .hero__marquee').forEach(el => {
            el.style.opacity   = '1';
            el.style.transform = 'none';
        });
        return;
    }

    const charEls = [...document.querySelectorAll('.hero__char')];
    if (!charEls.length) return;

    const total          = charEls.length;
    const STAGGER_WINDOW = 0.9;

    // ── Eyebrow fade ─────────────────────────────────────────────────────────
    gsap.to('.hero__eyebrow', {
        opacity: 1,
        duration: 0.7,
        ease: 'power2.out',
    });

    // ── Per-character clip reveal + scramble ─────────────────────────────────
    charEls.forEach((el, i) => {
        const charDelay = 0.15 + (i / Math.max(total - 1, 1)) * STAGGER_WINDOW;

        gsap.to(el, {
            y: '0%',
            duration: 1.1,
            ease: 'expo.out',
            delay: charDelay,
            onStart: () => scrambleChar(el, 220),
        });
    });

    // ── Footer + marquee follow after chars ──────────────────────────────────
    const footerDelay = 0.15 + STAGGER_WINDOW * 0.6;
    gsap.to('.hero__footer',  { opacity: 1, duration: 0.9, delay: footerDelay,       ease: 'power3.out' });
    gsap.to('.hero__marquee', { opacity: 1, duration: 0.6, delay: footerDelay + 0.1, ease: 'power2.out' });

    // ── Cursor parallax on headline block ────────────────────────────────────
    if (!('ontouchstart' in window)) {
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth  - 0.5) * 8;
            const y = (e.clientY / window.innerHeight - 0.5) * 4;
            gsap.to('.hero__headline', { x, y, duration: 1.4, ease: 'power2.out' });
        }, { passive: true });
    }
}
