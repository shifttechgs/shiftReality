import { gsap } from 'gsap';
import { prefersReducedMotion, isTouchDevice } from '../utils/motion.js';

/**
 * Wrap the first text node of an element in a .btn__label span so inner
 * elements can move independently from the button container.
 */
function wrapLabel(el) {
    for (const node of [...el.childNodes]) {
        if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
            const span = document.createElement('span');
            span.className = 'btn__label';
            span.textContent = node.textContent;
            el.replaceChild(span, node);
            break;
        }
    }
}

/**
 * Magnetic pull with parallax depth: the button container, inner label,
 * and arrow all move at different strengths — creating a 3-layer effect.
 */
class MagneticButton {
    constructor(el, options = {}) {
        this.el       = el;
        this.strength = options.strength ?? 0.38;
        this.enabled  = !prefersReducedMotion() && !isTouchDevice();

        if (this.enabled) {
            wrapLabel(el);
            this.bind();
        }
    }

    bind() {
        this.el.addEventListener('mousemove',  (e) => this.onMove(e),  { passive: true });
        this.el.addEventListener('mouseleave', ()  => this.onLeave(),  { passive: true });
    }

    onMove(e) {
        const r = this.el.getBoundingClientRect();
        const x = (e.clientX - r.left - r.width  / 2) * this.strength;
        const y = (e.clientY - r.top  - r.height / 2) * this.strength;

        // Container: full magnetic pull
        gsap.to(this.el, { x, y, duration: 0.4, ease: 'power2.out' });

        // Label: gentler movement — lags behind the button
        const label = this.el.querySelector('.btn__label');
        if (label) gsap.to(label, { x: x * 0.35, y: y * 0.35, duration: 0.55, ease: 'power2.out' });

        // Arrow: stronger pull — leads the cursor
        const arrow = this.el.querySelector('.arrow');
        if (arrow) gsap.to(arrow, { x: x * 0.9, y: y * 0.9, duration: 0.25, ease: 'power2.out' });
    }

    onLeave() {
        gsap.to(this.el, { x: 0, y: 0, duration: 0.7, ease: 'elastic.out(1, 0.45)' });

        const label = this.el.querySelector('.btn__label');
        const arrow = this.el.querySelector('.arrow');
        if (label) gsap.to(label, { x: 0, y: 0, duration: 0.7, ease: 'elastic.out(1, 0.45)' });
        if (arrow) gsap.to(arrow, { x: 0, y: 0, duration: 0.7, ease: 'elastic.out(1, 0.45)' });
    }
}

export function initMagnetic(selector = '[data-magnetic]') {
    document.querySelectorAll(selector).forEach(el => new MagneticButton(el));
}
