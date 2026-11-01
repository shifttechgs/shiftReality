// ─── Critical path — no GSAP, runs immediately ───────────────────────────────
import { initScroll }  from './animations/scroll.js';
import { initMoments } from './animations/moments.js';
import { initAbout } from './animations/about.js';
import { initFocusPull } from './animations/focus-pull.js';
import { initCountUp } from './animations/count-up.js';
import { initLocalClock } from './local-clock.js';
import { initLazyEmbeds } from './lazy-embeds.js';
import { initBooking } from './booking.js';
import { initHeroBook } from './hero-book.js';
import { initNavMore } from './nav-more.js';
import { initExhibits } from './animations/exhibits.js';
import { prefersReducedMotion } from './utils/motion.js';

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
    // (the home FAQ opens as you scroll instead: initFaqScroll)
    const items = [...document.querySelectorAll('.faq:not([data-faq-scroll]) .faq__item')];
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

    document.querySelectorAll('.faq:not([data-faq-scroll])').forEach(list => {
        list.addEventListener('pointerleave', (e) => {
            if (e.pointerType === 'mouse') later(() => openOnly(null), 250);
        });
    });
}

// ─── FAQ (home): read down it ──────────────────────────────────────────────
// One answer open at a time: the first, until faq-pin.js (with GSAP) takes
// over and the scroll walks down them. Tapping a question goes to it.
// It's rendered all open: without JS, or with reduced motion, every answer
// simply shows.
function initFaqScroll() {
    const list = document.querySelector('[data-faq-scroll]');
    if (!list || prefersReducedMotion()) return;
    const items = [...list.querySelectorAll('.faq__item')];
    const only = (i) => items.forEach((item, k) => {
        item.classList.toggle('is-open', k === i);
        item.querySelector('.faq__question').setAttribute('aria-expanded', String(k === i));
    });
    list.closest('.faq-home')?.classList.add('faq-home--live');
    only(0);
    items.forEach((item, i) => item.querySelector('.faq__question').addEventListener('click', () => {
        if (list._faqGoTo) list._faqGoTo(i);
        else only(i);
    }));
}

// ─── Walk in: mark it live before GSAP arrives, so the stage doesn't jump ─────
function prepWalk() {
    const walk = document.querySelector('[data-walk]');
    if (walk && !prefersReducedMotion()) walk.classList.add('walk--live');
}

// ─── Boot critical path ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initNav();
    initNavMore();
    initFaq();
    initFaqScroll();
    initScroll();   // nav scroll class + parallax — no GSAP
    prepWalk();
    initMoments();
    initAbout();
    initFocusPull();
    initCountUp();
    initLocalClock();
    initLazyEmbeds();
    initBooking();
    initHeroBook();
    initExhibits();

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
            { initFooterMotion },
            { initSplitHeadings },
            { initScrubReveal },
            { initWalk },
            { initFaqPin },
        ] = await Promise.all([
            import('./animations/reveals.js'),
            import('./animations/rooms.js'),
            import('./animations/transitions.js'),
            import('./animations/footer-motion.js'),
            import('./animations/split-headings.js'),
            import('./animations/scrub-reveal.js'),
            import('./animations/walk.js'),
            import('./animations/faq-pin.js'),
        ]);

        initTransitions();
        initWalk();
        initRooms();
        initFaqPin();
        initFooterMotion();
        initSplitHeadings();
        initScrubReveal();
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
