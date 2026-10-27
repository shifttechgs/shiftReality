/**
 * The header's "More" panel (after Artora's "Pages" menu). A click toggles it;
 * with a mouse, hovering "More" opens it and leaving the header (after a short
 * grace) closes it. Esc, a click outside and focus moving away also close it.
 */
export function initNavMore() {
    const button = document.querySelector('[data-nav-more]');
    const panel  = document.querySelector('[data-nav-more-panel]');
    const nav    = button?.closest('.nav');
    if (!button || !panel || !nav) return;

    let timer = 0;
    let hoverOpenedAt = 0;   // a click right after hover opened it shouldn't shut it again
    const set = (open) => {
        clearTimeout(timer);
        panel.hidden = !open;
        button.setAttribute('aria-expanded', String(open));
        nav.classList.toggle('is-more-open', open);
    };
    const isOpen = () => !panel.hidden;

    button.addEventListener('click', () => {
        if (isOpen() && performance.now() - hoverOpenedAt < 600) return;
        set(!isOpen());
    });

    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        button.addEventListener('pointerenter', (e) => {
            if (e.pointerType !== 'mouse' || isOpen()) return;
            hoverOpenedAt = performance.now();
            set(true);
        });
        nav.addEventListener('pointerleave', (e) => {
            if (e.pointerType === 'mouse' && isOpen()) timer = setTimeout(() => set(false), 220);
        });
        nav.addEventListener('pointerenter', () => clearTimeout(timer));
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen()) { set(false); button.focus(); }
    });
    document.addEventListener('click', (e) => { if (isOpen() && !nav.contains(e.target)) set(false); });
    nav.addEventListener('focusout', (e) => { if (isOpen() && !nav.contains(e.relatedTarget)) set(false); });
    window.addEventListener('scroll', () => { if (isOpen() && !nav.matches(':hover')) set(false); }, { passive: true });
}
