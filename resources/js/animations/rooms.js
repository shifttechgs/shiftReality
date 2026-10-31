import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { prefersReducedMotion } from '../utils/motion.js';
import upsideDown from '../rooms/upside-down.js';
import mirror     from '../rooms/mirror.js';
import infinity   from '../rooms/infinity.js';
import beuchet    from '../rooms/beuchet.js';

const ILLUSIONS = { 'upside-down': upsideDown, mirror, infinity, beuchet };

/**
 * The rooms, as a row of cards. Each card's stage holds that room's
 * illusion and gets a 0→1 progress as the stage travels up the screen
 * (0.5 = centred): the room turns over, the chair lines up. The loops
 * (mirror, infinity) play while their card is hovered — the whole set is
 * on screen at once, so they'd all run together otherwise; without hover
 * (touch) they run while their card is on screen.
 * Reduced motion: illusions shown in their finished state.
 */
export function initRooms() {
    const root = document.querySelector('[data-rooms]');
    if (!root) return;

    const reduced = prefersReducedMotion();
    const cards   = [...root.querySelectorAll('[data-room]')];

    const illusions = cards.map((card) => {
        const make = !card.hasAttribute('data-room-photo') && ILLUSIONS[card.dataset.room];   // a real photo has no drawing to run
        return make ? make(card.querySelector('[data-room-stage]'), { reduced }) : null;
    });

    const hover = window.matchMedia('(hover: hover)').matches;

    const io = new IntersectionObserver((entries) => {
        for (const entry of entries) {
            const illusion = illusions[cards.indexOf(entry.target)];
            if (!illusion) continue;
            if (!entry.isIntersecting) illusion.stop();
            else if (!hover) illusion.start();
        }
    }, { threshold: 0.1 });
    cards.forEach((card) => io.observe(card));

    if (hover && !reduced) {
        cards.forEach((card, i) => {
            card.addEventListener('pointerenter', () => illusions[i]?.start());
            card.addEventListener('pointerleave', () => illusions[i]?.stop());
        });
    }

    if (reduced) {
        illusions.forEach((illusion) => illusion?.setProgress(1));
        return;
    }

    cards.forEach((card, i) => {
        ScrollTrigger.create({
            trigger: card.querySelector('[data-room-stage]'),
            start: 'top bottom',
            end: 'bottom top',
            onUpdate: (self) => illusions[i]?.setProgress(self.progress),
        });
    });
}
