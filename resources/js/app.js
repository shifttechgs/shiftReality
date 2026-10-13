// ─── Critical path — no GSAP, runs immediately ───────────────────────────────
import { initScroll }  from './animations/scroll.js';
import { isTouchDevice } from './utils/motion.js';

// ─── Custom Cursor (no GSAP — pure rAF) ──────────────────────────────────────
function initCursor() {
    if (isTouchDevice()) return;

    const dot  = document.querySelector('.cursor');
    const ring = document.querySelector('.cursor-ring');
    const glow = document.querySelector('.cursor-glow');
    if (!dot || !ring) return;

    let dotX = 0, dotY = 0, ringX = 0, ringY = 0;
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

        dot.style.transform  = `translate(${dotX}px,${dotY}px) translate(-50%,-50%)`;
        ring.style.transform = `translate(${ringX}px,${ringY}px) translate(-50%,-50%)`;
        if (glow) glow.style.transform = `translate(${glowX}px,${glowY}px) translate(-50%,-50%)`;

        requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);

    document.querySelectorAll('a, button, [data-magnetic], .faq__question').forEach(el => {
        el.addEventListener('mouseenter', () => document.body.classList.add('cursor--hovering'),    { passive: true });
        el.addEventListener('mouseleave', () => document.body.classList.remove('cursor--hovering'), { passive: true });
    });
}

// ─── Nav overlay (no GSAP) ────────────────────────────────────────────────────
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
        overlay.setAttribute('aria-hidden', String(!open));
    };

    toggle.addEventListener('click', () => setOpen(!open));

    const closeBtn = overlay.querySelector('.nav-overlay__close');
    if (closeBtn) closeBtn.addEventListener('click', () => setOpen(false));

    overlay.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && open) setOpen(false); });
}

// ─── FAQ accordion (no GSAP) ─────────────────────────────────────────────────
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

// ─── Top banner dismiss ───────────────────────────────────────────────────────
function initBanner() {
    const banner = document.getElementById('top-banner');
    const btn    = document.getElementById('top-banner-close');
    if (!banner || !btn) return;

    btn.addEventListener('click', () => {
        banner.classList.add('is-hidden');
        sessionStorage.setItem('banner-dismissed', '1');
    });

    if (sessionStorage.getItem('banner-dismissed')) {
        banner.classList.add('is-hidden');
    }
}

// ─── Boot critical path ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initBanner();
    initCursor();
    initNav();
    initFaq();
    initScroll();   // nav scroll class + parallax — no GSAP

    // ── Defer GSAP bundle until after page is visible and idle ────────────────
    const loadAnimations = async () => {
        const { gsap }          = await import('gsap');
        const { ScrollTrigger } = await import('gsap/ScrollTrigger');
        const { default: lenis } = await import('./utils/lenis.js');

        gsap.registerPlugin(ScrollTrigger);
        gsap.ticker.add((time) => lenis.raf(time * 1000));
        gsap.ticker.lagSmoothing(0);
        lenis.on('scroll', ScrollTrigger.update);

        const [
            { initReveals },
            { initMagnetic },
            { initHorizontal },
            { initTransitions },
        ] = await Promise.all([
            import('./animations/reveals.js'),
            import('./animations/magnetic.js'),
            import('./animations/horizontal.js'),
            import('./animations/transitions.js'),
        ]);

        initTransitions();
        initMagnetic();
        initHorizontal();
        initReveals();
    };

    // requestIdleCallback fires when the browser has spare cycles
    if ('requestIdleCallback' in window) {
        requestIdleCallback(loadAnimations, { timeout: 2000 });
    } else {
        setTimeout(loadAnimations, 200);
    }
});
