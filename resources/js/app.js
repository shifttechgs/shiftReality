import { gsap }          from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import lenis              from './utils/lenis.js';
import { isTouchDevice }  from './utils/motion.js';

import { initHero }        from './animations/hero.js';
import { initReveals }     from './animations/reveals.js';
import { initScroll }      from './animations/scroll.js';
import { initMagnetic }    from './animations/magnetic.js';
import { initHorizontal }  from './animations/horizontal.js';
import { initTransitions } from './animations/transitions.js';

// ─── GSAP + Lenis integration ─────────────────────────────────────────────────
gsap.registerPlugin(ScrollTrigger);
gsap.ticker.add((time) => lenis.raf(time * 1000));
gsap.ticker.lagSmoothing(0);
lenis.on('scroll', ScrollTrigger.update);

// ─── Custom Cursor ────────────────────────────────────────────────────────────
function initCursor() {
    if (isTouchDevice()) return;

    const dot  = document.querySelector('.cursor');
    const ring = document.querySelector('.cursor-ring');
    const glow = document.querySelector('.cursor-glow');
    if (!dot || !ring) return;

    let dotX  = 0, dotY  = 0;
    let ringX = 0, ringY = 0;
    let glowX = window.innerWidth / 2, glowY = window.innerHeight / 2;
    let mouseX = 0, mouseY = 0;

    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
    }, { passive: true });

    const tick = () => {
        dotX   = mouseX;
        dotY   = mouseY;
        ringX += (mouseX - ringX) * 0.15;
        ringY += (mouseY - ringY) * 0.15;
        glowX += (mouseX - glowX) * 0.055;
        glowY += (mouseY - glowY) * 0.055;

        dot.style.transform  = `translate(${dotX}px,  ${dotY}px)  translate(-50%, -50%)`;
        ring.style.transform = `translate(${ringX}px, ${ringY}px) translate(-50%, -50%)`;
        if (glow) glow.style.transform = `translate(${glowX}px, ${glowY}px) translate(-50%, -50%)`;

        requestAnimationFrame(tick);
    };

    requestAnimationFrame(tick);

    document.querySelectorAll('a, button, [data-magnetic], .faq__question').forEach(el => {
        el.addEventListener('mouseenter', () => document.body.classList.add('cursor--hovering'),    { passive: true });
        el.addEventListener('mouseleave', () => document.body.classList.remove('cursor--hovering'), { passive: true });
    });
}

// ─── Navigation Overlay ───────────────────────────────────────────────────────
function initNav() {
    const nav     = document.querySelector('.nav');
    const toggle  = document.querySelector('.nav__toggle');
    const overlay = document.querySelector('.nav-overlay');
    if (!toggle || !overlay) return;

    let open = false;

    const setOpen = (state) => {
        open = state;
        nav.classList.toggle('is-open', open);
        overlay.classList.toggle('is-open', open);
        document.body.style.overflow = open ? 'hidden' : '';
        toggle.setAttribute('aria-expanded', String(open));
        overlay.setAttribute('aria-hidden',  String(!open));
        if (open) lenis.stop(); else lenis.start();
    };

    toggle.addEventListener('click', () => setOpen(!open));
    overlay.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && open) setOpen(false); });
}

// ─── FAQ Accordion ────────────────────────────────────────────────────────────
function initFaq() {
    document.querySelectorAll('.faq__question').forEach(btn => {
        btn.addEventListener('click', () => {
            const item   = btn.closest('.faq__item');
            const isOpen = item.classList.contains('is-open');

            document.querySelectorAll('.faq__item').forEach(i => {
                i.classList.remove('is-open');
                i.querySelector('.faq__question').setAttribute('aria-expanded', 'false');
            });

            if (!isOpen) {
                item.classList.add('is-open');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });
}

// ─── Boot ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initTransitions();
    initCursor();
    initNav();
    initFaq();
    initHero();
    initReveals();
    initScroll();
    initMagnetic();
    initHorizontal();
});
