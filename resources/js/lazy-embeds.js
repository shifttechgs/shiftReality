/**
 * Third-party embeds (e.g. the Google map) load only when they come within
 * about a screen of view: iframe[data-lazy-src] gets its src then.
 */
export function initLazyEmbeds() {
    const frames = [...document.querySelectorAll('iframe[data-lazy-src]')];
    if (!frames.length) return;

    const load = (frame) => {
        frame.src = frame.dataset.lazySrc;
        frame.removeAttribute('data-lazy-src');
    };

    if (!('IntersectionObserver' in window)) { frames.forEach(load); return; }

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            io.unobserve(entry.target);
            load(entry.target);
        });
    }, { rootMargin: '100% 0px' });
    frames.forEach((f) => io.observe(f));
}
