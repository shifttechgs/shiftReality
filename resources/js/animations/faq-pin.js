import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { prefersReducedMotion } from '../utils/motion.js';
import lenis from '../utils/lenis.js';

/**
 * The home FAQ, read in place: the section holds still (pinned, centred
 * on the screen) while you scroll through it, and the scroll walks down
 * the questions one at a time: the one you're on opens, the one before
 * closes. Tapping a question scrolls to its turn, so the two never
 * disagree. When the section can't fit the screen (short screens), it
 * isn't pinned: the question nearest the middle of the screen is the
 * open one instead.
 * Reduced motion / no JS: every answer shows (it's rendered all open).
 */
const STEP = 0.42;   // screens of scroll per question

export function initFaqPin() {
    const list = document.querySelector('[data-faq-scroll]');
    if (!list || prefersReducedMotion()) return;
    const pin = list.closest('[data-faq-pin]');
    const items = [...list.querySelectorAll('.faq__item')];
    const n = items.length;

    let current = -1;
    const show = (i) => {
        if (i === current) return;
        current = i;
        items.forEach((item, k) => {
            item.classList.toggle('is-open', k === i);
            item.querySelector('.faq__question').setAttribute('aria-expanded', String(k === i));
        });
    };
    const at = (p) => Math.min(n - 1, Math.max(0, Math.floor(p * n)));

    let st = null;
    const fits = () => pin.offsetHeight < window.innerHeight - 40;

    // Pinned: the scroll through the pin picks the question
    const build = () => {
        st?.kill(true);
        st = null;
        if (!fits()) return;
        st = ScrollTrigger.create({
            trigger: pin,
            start: 'center 54%',   // a touch below centre: clear of the header bar
            end: () => '+=' + Math.round(window.innerHeight * STEP * n),
            pin: true,
            onUpdate: (self) => show(at(self.progress)),
        });
        show(at(st.progress));
    };

    // Not pinned: the question nearest the screen's middle
    const nearest = () => {
        if (st) return;
        const mid = window.innerHeight / 2;
        let best = 0, d = Infinity;
        items.forEach((item, k) => {
            const r = item.getBoundingClientRect();
            const dk = Math.abs(r.top + Math.min(r.height, 80) / 2 - mid);
            if (dk < d) { d = dk; best = k; }
        });
        show(best);
    };
    lenis.on('scroll', nearest);

    // Tapping a question goes to its turn (pinned), or just opens it
    list._faqGoTo = (i) => {
        if (!st) { show(i); return; }
        const y = st.start + (st.end - st.start) * ((i + 0.5) / n);
        lenis.scrollTo(y, { duration: 0.9 });
    };

    // measured once the other answers have finished closing (they animate shut)
    setTimeout(() => { build(); ScrollTrigger.refresh(); }, 800);
    let t = 0;
    window.addEventListener('resize', () => { clearTimeout(t); t = setTimeout(() => { build(); ScrollTrigger.refresh(); }, 200); });
}
