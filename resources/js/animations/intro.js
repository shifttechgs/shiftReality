import { gsap }               from 'gsap';
import { prefersReducedMotion } from '../utils/motion.js';
import lenis                    from '../utils/lenis.js';

/**
 * Full-screen intro sequence. Plays once per page load.
 * "SHIFT" reveals from below, rule draws across, "REALITY" follows.
 * Screen then tears apart — top up, bottom down — revealing the hero.
 * Calls onComplete when the hero is fully exposed.
 */
export function initIntro(onComplete) {
    if (prefersReducedMotion()) {
        onComplete();
        return;
    }

    const el = document.getElementById('intro-screen');
    if (!el) {
        onComplete();
        return;
    }

    // Lock scroll — nothing should move while the curtain is closed
    lenis.stop();
    document.body.style.overflow = 'hidden';

    const shiftChars   = [...el.querySelectorAll('.intro-screen__word--shift   .intro-screen__char')];
    const realityChars = [...el.querySelectorAll('.intro-screen__word--reality .intro-screen__char')];
    const rule         = el.querySelector('.intro-screen__rule');
    const sub          = el.querySelector('.intro-screen__sub');
    const wordmark     = el.querySelector('.intro-screen__wordmark');

    // Set sub starting position for its slide-in
    if (sub) gsap.set(sub, { y: 8 });

    const tl = gsap.timeline({
        onComplete: () => {
            el.remove();
            document.body.style.overflow = '';
            lenis.start();
            onComplete();
        },
    });

    // ── SHIFT reveals per character, clipping from beneath ────────────────────
    tl.to(shiftChars, {
        y: '0%',
        duration: 0.85,
        ease: 'expo.out',
        stagger: 0.042,
    }, 0.1);

    // ── Rule draws left → right ───────────────────────────────────────────────
    tl.to(rule, {
        scaleX: 1,
        duration: 0.55,
        ease: 'power3.out',
    }, '>-0.45');

    // ── REALITY reveals per character (starts with rule) ──────────────────────
    tl.to(realityChars, {
        y: '0%',
        duration: 0.85,
        ease: 'expo.out',
        stagger: 0.038,
    }, '<');

    // ── Sub label slides up ────────────────────────────────────────────────────
    if (sub) {
        tl.to(sub, {
            opacity: 1,
            y: 0,
            duration: 0.38,
            ease: 'power2.out',
        }, '>-0.08');
    }

    // ── Brief hold — let the logo breathe ─────────────────────────────────────
    tl.to({}, { duration: 0.32 });

    // ── Wordmark fades out first, then screen tears ───────────────────────────
    tl.to(wordmark, {
        opacity: 0,
        duration: 0.18,
        ease: 'power1.in',
    });

    // ── Screen tears: top panel slides up, bottom slides down ────────────────
    // Uses clip-path: inset collapses to nothing from the horizontal center.
    // inset(top right bottom left) — top grows to 50%, bottom grows to 50%
    tl.to(el, {
        clipPath: 'inset(50% 0% 50% 0%)',
        duration: 0.72,
        ease: 'expo.inOut',
    }, '>-0.04');
}
