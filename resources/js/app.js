// ─── Critical path — no GSAP, runs immediately ───────────────────────────────
import { initScroll }  from './animations/scroll.js';
import { initCountUp } from './animations/count-up.js';
import { initLocalClock } from './local-clock.js';
import { initLazyEmbeds } from './lazy-embeds.js';
import { initBooking } from './booking.js';
import { isTouchDevice } from './utils/motion.js';

// ─── Custom Cursor (no GSAP — pure rAF) ──────────────────────────────────────
function initCursor() {
    if (isTouchDevice()) return;

    const dot  = document.querySelector('.cursor');
    const ring = document.querySelector('.cursor-ring');
    if (!dot || !ring) return;

    let dotX = 0, dotY = 0, ringX = 0, ringY = 0;
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

        dot.style.transform  = `translate(${dotX}px,${dotY}px) translate(-50%,-50%)`;
        ring.style.transform = `translate(${ringX}px,${ringY}px) translate(-50%,-50%)`;

        requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);

    document.querySelectorAll('a, button, .faq__question').forEach(el => {
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
    const items = [...document.querySelectorAll('.faq__item')];
    if (!items.length) return;

    // Opens one item (or none), closing the rest
    const openOnly = (target) => {
        items.forEach(i => {
            const open = i === target;
            i.classList.toggle('is-open', open);
            i.querySelector('.faq__question').setAttribute('aria-expanded', String(open));
        });
    };

    // Click / tap / keyboard: toggle
    items.forEach(item => {
        item.querySelector('.faq__question').addEventListener('click', () => {
            openOnly(item.classList.contains('is-open') ? null : item);
        });
    });

    // Mouse: hovering a question opens its answer. Opening one row shifts the
    // others under a still cursor, so only real pointer movement counts, after
    // a short rest; leaving the list closes it again.
    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;

    let timer;
    const later = (fn, ms) => { clearTimeout(timer); timer = setTimeout(fn, ms); };

    items.forEach(item => {
        item.addEventListener('pointermove', (e) => {
            if (e.pointerType !== 'mouse' || (!e.movementX && !e.movementY)) return;
            if (item.classList.contains('is-open')) { clearTimeout(timer); return; }
            later(() => openOnly(item), 140);
        });
    });

    document.querySelectorAll('.faq').forEach(list => {
        list.addEventListener('pointerleave', (e) => {
            if (e.pointerType === 'mouse') later(() => openOnly(null), 250);
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
    initCountUp();
    initLocalClock();
    initLazyEmbeds();
    initBooking();

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
            { initRooms },
            { initTransitions },
            { initAudienceStack },
            { initBrandBanner },
            { initFooterMotion },
            { initSplitHeadings },
            { initScrubReveal },
            { initHeroParallax },
        ] = await Promise.all([
            import('./animations/reveals.js'),
            import('./animations/rooms.js'),
            import('./animations/transitions.js'),
            import('./animations/audience-stack.js'),
            import('./animations/brand-banner.js'),
            import('./animations/footer-motion.js'),
            import('./animations/split-headings.js'),
            import('./animations/scrub-reveal.js'),
            import('./animations/hero-parallax.js'),
        ]);

        initTransitions();
        initRooms();
        initAudienceStack();
        initBrandBanner();
        initFooterMotion();
        initSplitHeadings();
        initScrubReveal();
        initHeroParallax();
        initReveals();

        // Arriving with a #hash (e.g. "Pricing" from another page): jump only now,
        // once the pinned sections have added their height, so the target stays put
        const target = location.hash.length > 1 && document.getElementById(location.hash.slice(1));
        if (target) {
            // measure against the browser's real scroll position, not Lenis's (stale after a refresh)
            // and leave room for the fixed bar only when the section's own padding can't
            const jump = () => {
                const bar = document.querySelector('.nav')?.getBoundingClientRect().bottom ?? 0;
                const room = parseFloat(getComputedStyle(target).paddingTop) || 0;
                const y = target.getBoundingClientRect().top + window.scrollY - Math.max(0, bar + 16 - room);
                lenis.scrollTo(y, { immediate: true, force: true });
            };
            ScrollTrigger.refresh();
            jump();
            // late layout shifts (fonts, lazy images, pin refresh) can nudge it: settle once more
            setTimeout(() => { ScrollTrigger.refresh(); jump(); }, 450);
        }
    };

    // requestIdleCallback fires when the browser has spare cycles
    if ('requestIdleCallback' in window) {
        requestIdleCallback(loadAnimations, { timeout: 2000 });
    } else {
        setTimeout(loadAnimations, 200);
    }
});
