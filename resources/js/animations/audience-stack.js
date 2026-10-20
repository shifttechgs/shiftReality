import { prefersReducedMotion, clamp } from '../utils/motion.js';

/**
 * Card pile depth for the "Who It's For" stack.
 * The pile itself is pure CSS (position: sticky); this only writes
 *   --p  0 → 1, how far the next card has slid over this one (darkens it)
 *   --d  depth in the pile, the sum of every cover above it (shrinks it)
 * Without JS the cards still stack, just flat.
 */
export function initAudienceStack() {
    const stack = document.querySelector('[data-audience-stack]');
    if (!stack || prefersReducedMotion()) return;

    const items = [...stack.children];
    const cards = items.map((item) => item.querySelector('[data-audience-card]'));
    if (items.length < 2) return;

    // Where each item sticks (its CSS `top`) — only changes on resize
    let stuckAt = [];
    const measure = () => {
        stuckAt = items.map((item) => parseFloat(getComputedStyle(item).top) || 0);
    };

    let queued = false;
    const update = () => {
        queued = false;
        // cover[j]: how far card j has slid over card j - 1
        const cover = items.map((item, j) => {
            if (j === 0) return 0;
            const height = cards[j - 1].offsetHeight || 1;
            return clamp(1 - (item.getBoundingClientRect().top - stuckAt[j]) / height, 0, 1);
        });
        // --d: how deep in the pile a card sits (sum of every cover above it)
        let depth = 0;
        for (let i = cards.length - 1; i >= 0; i--) {
            cards[i].style.setProperty('--p', (cover[i + 1] ?? 0).toFixed(3));
            cards[i].style.setProperty('--d', depth.toFixed(3));
            depth += cover[i];
        }
    };
    const queue = () => {
        if (queued) return;
        queued = true;
        requestAnimationFrame(update);
    };

    // Only listen while the stack is near the viewport
    let listening = false;
    const io = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting === listening) return;
        listening = entry.isIntersecting;
        window[listening ? 'addEventListener' : 'removeEventListener']('scroll', queue, { passive: true });
        if (listening) queue();
    }, { rootMargin: '25% 0px' });

    measure();
    io.observe(stack);
    window.addEventListener('resize', () => { measure(); queue(); }, { passive: true });
}
