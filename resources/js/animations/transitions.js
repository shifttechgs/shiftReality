import { gsap } from 'gsap';
import lenis from '../utils/lenis.js';

const SELECTOR = '.page-transition';
const DURATION = 0.65;
const EASE     = 'expo.inOut';

function isInternal(href) {
    if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return false;
    try {
        return new URL(href, window.location.origin).hostname === window.location.hostname;
    } catch {
        return false;
    }
}

// Called on every page load — sweeps the overlay upward, revealing the page
function enterTransition() {
    const overlay = document.querySelector(SELECTOR);
    if (!overlay) return;

    gsap.fromTo(overlay,
        { clipPath: 'inset(0% 0 0% 0)' },
        {
            clipPath: 'inset(100% 0 0% 0)',
            duration: DURATION,
            ease: EASE,
            onComplete: () => { overlay.style.pointerEvents = 'none'; },
        }
    );
}

// Called on link click — sweeps the overlay down, covering the page, then navigates
function exitTransition(href) {
    const overlay = document.querySelector(SELECTOR);
    if (!overlay) { window.location.href = href; return; }

    lenis.stop();
    overlay.style.pointerEvents = 'all';

    gsap.fromTo(overlay,
        { clipPath: 'inset(0% 0 100% 0)' },
        {
            clipPath: 'inset(0% 0 0% 0)',
            duration: DURATION,
            ease: EASE,
            onComplete: () => { window.location.href = href; },
        }
    );
}

export function initTransitions() {
    // No enter transition — page is visible immediately on load.
    // Exit transition only: covers screen when navigating away.
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[href]');
        if (!link || link.target === '_blank') return;

        const href = link.getAttribute('href');
        if (!isInternal(href)) return;

        e.preventDefault();
        exitTransition(href);
    });
}
