import { prefersReducedMotion, clamp } from '../utils/motion.js';

/**
 * Section headings [data-split]: the footer watermark's motion, for headings.
 * - Scroll: letters rise out of each word's mask one after another as the
 *   heading comes up the screen, and sink back if you scroll above it.
 * - Cursor: letters near the pointer lift slightly.
 * The heading is split into word masks and letters; screen readers get the
 * original text from a visually hidden copy. Reduced motion: no split at all.
 * data-split="hover": cursor lift only, no scroll rise (for headings that
 * already have their own entrance, like the hero).
 */

// Wrap each word in a mask and each letter in a span, keeping <br> and <em>
function split(node) {
    for (const child of [...node.childNodes]) {
        if (child.nodeType === Node.ELEMENT_NODE) {
            if (child.tagName !== 'BR') split(child);
            continue;
        }
        if (child.nodeType !== Node.TEXT_NODE || !child.textContent.trim()) continue;

        const frag = document.createDocumentFragment();
        for (const part of child.textContent.split(/(\s+)/)) {
            if (!part) continue;
            if (/^\s+$/.test(part)) { frag.append(' '); continue; }
            const word = document.createElement('span');
            word.className = 'split-w';
            for (const ch of part) {
                const l = document.createElement('span');
                l.className = 'split-l';
                l.textContent = ch;
                word.append(l);
            }
            frag.append(word);
        }
        child.replaceWith(frag);
    }
}

export function initSplitHeadings() {
    if (prefersReducedMotion()) return;

    const headings = [...document.querySelectorAll('[data-split]')].map((el) => {
        const text = el.innerText.replace(/\s+/g, ' ').trim();   // innerText turns <br> into a space
        const inner = document.createElement('span');
        inner.setAttribute('aria-hidden', 'true');
        inner.append(...el.childNodes);
        split(inner);
        const sr = document.createElement('span');
        sr.className = 'sr-only';
        sr.textContent = text;
        el.append(sr, inner);
        el.classList.add('is-split');

        const letters = [...inner.querySelectorAll('.split-l')];
        const hoverOnly = el.dataset.split === 'hover';
        const h = { el, letters, hoverOnly, state: letters.map(() => ({ rise: hoverOnly ? 1 : 0, lift: 0, drawn: hoverOnly })), visible: false, pointerX: null };

        el.addEventListener('pointermove', (e) => { if (e.pointerType === 'mouse') h.pointerX = e.clientX; }, { passive: true });
        el.addEventListener('pointerleave', () => { h.pointerX = null; });
        return h;
    });
    if (!headings.length) return;

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            const h = headings.find((x) => x.el === entry.target);
            if (h) h.visible = entry.isIntersecting;
        });
    }, { rootMargin: '10% 0px' });
    headings.forEach((h) => io.observe(h.el));

    const ease = (t) => 1 - Math.pow(1 - t, 3);

    const frame = () => {
        requestAnimationFrame(frame);
        const vh = window.innerHeight;

        for (const h of headings) {
            if (!h.visible) continue;

            // 0 as the heading enters at the bottom, 1 once it's 45% of the way up
            const top = h.el.getBoundingClientRect().top;
            const p = h.hoverOnly ? 1 : clamp((vh - top) / (vh * 0.45), 0, 1);
            const n = h.letters.length;

            h.letters.forEach((el, i) => {
                const st = h.state[i];
                const target = ease(clamp((p - (i / n) * 0.45) / 0.55, 0, 1));

                let lift = 0;
                if (h.pointerX !== null) {
                    const r = el.getBoundingClientRect();
                    lift = Math.max(0, 1 - Math.abs(r.left + r.width / 2 - h.pointerX) / 140);
                }

                const rise = st.rise + (target - st.rise) * 0.14;
                const lifted = st.lift + (lift - st.lift) * 0.15;
                if (st.drawn && Math.abs(rise - st.rise) < 0.0005 && Math.abs(lifted - st.lift) < 0.0005) return;   // settled
                st.drawn = true;
                st.rise = rise;
                st.lift = lifted;
                el.style.transform = `translate3d(0, ${((1 - rise) * 105 - lifted * 9).toFixed(2)}%, 0)`;
            });
        }
    };
    requestAnimationFrame(frame);
}
