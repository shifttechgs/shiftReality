import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { prefersReducedMotion } from '../utils/motion.js';
import upsideDown from '../rooms/upside-down.js';
import mirror     from '../rooms/mirror.js';
import infinity   from '../rooms/infinity.js';
import beuchet    from '../rooms/beuchet.js';

const ILLUSIONS = { 'upside-down': upsideDown, mirror, infinity, beuchet };

/**
 * The four rooms.
 *
 * Desktop: the dark frame opens like a portal as it arrives, then pins while
 * the track scrolls sideways. Each room gets a 0→1 progress as it crosses the
 * screen (0.5 = centred) which drives its illusion; titles rise in as it lands.
 *
 * Narrower screens stack the rooms vertically with the same progress model.
 * Reduced motion: no pin, illusions shown in their finished state.
 */
export function initRooms() {
    const root = document.querySelector('[data-rooms]');
    if (!root) return;

    const reduced = prefersReducedMotion();
    const frame   = root.querySelector('[data-rooms-frame]');
    const track   = root.querySelector('[data-rooms-track]');
    const panels  = [...root.querySelectorAll('[data-room]')];
    const current = root.querySelector('[data-rooms-current]');
    const bar     = root.querySelector('[data-rooms-progress]');

    const illusions = panels.map((panel) => {
        const make = ILLUSIONS[panel.dataset.room];
        return make ? make(panel.querySelector('[data-room-stage]'), { reduced }) : null;
    });

    // Continuous loops (mirror, infinity) only run while their room is on screen
    const io = new IntersectionObserver((entries) => {
        for (const entry of entries) {
            const illusion = illusions[panels.indexOf(entry.target)];
            if (!illusion) continue;
            entry.isIntersecting ? illusion.start() : illusion.stop();
        }
    }, { threshold: 0.1 });
    panels.forEach((panel) => io.observe(panel));

    if (reduced) {
        illusions.forEach((illusion) => illusion?.setProgress(1));
        panels.forEach((panel) => panel.classList.add('is-active'));
        return;
    }

    const mm = gsap.matchMedia();

    // ── Desktop: portal + pinned horizontal track ───────────────────────────
    mm.add('(min-width: 1024px)', () => {
        root.classList.add('is-horizontal');

        gsap.fromTo(frame,
            { clipPath: 'inset(7% 5% 7% 5% round 8px)' },   // matches --radius-card
            {
                clipPath: 'inset(0% 0% 0% 0% round 0px)',
                ease: 'none',
                scrollTrigger: { trigger: root, start: 'top 90%', end: 'top top', scrub: true },
            },
        );

        const distance = () => track.scrollWidth - window.innerWidth;

        const slide = gsap.to(track, {
            x: () => -distance(),
            ease: 'none',
            scrollTrigger: {
                trigger: root,
                start: 'top top',
                end: () => `+=${distance()}`,
                pin: true,
                scrub: 0.9,
                anticipatePin: 1,
                invalidateOnRefresh: true,
                onUpdate: (self) => {
                    bar.style.transform = `scaleX(${self.progress})`;

                    // Counter follows whichever room spans the centre of the screen
                    const centre = self.progress * distance() + window.innerWidth / 2;
                    const index  = Math.max(0, panels.findLastIndex((p) => p.offsetLeft <= centre));
                    const label  = String(index + 1).padStart(2, '0');
                    if (current.textContent !== label) current.textContent = label;
                },
            },
        });

        panels.forEach((panel, i) => {
            const shared = { trigger: panel, containerAnimation: slide };

            ScrollTrigger.create({
                ...shared,
                start: 'left right',
                end: 'right left',
                onUpdate: (self) => illusions[i]?.setProgress(self.progress),
            });

            ScrollTrigger.create({
                ...shared,
                start: 'left 65%',
                end: 'right 35%',
                toggleClass: { targets: panel, className: 'is-active' },
            });

            gsap.fromTo(panel.querySelector('[data-room-number]'),
                { xPercent: 35 },
                { xPercent: -35, ease: 'none', scrollTrigger: { ...shared, start: 'left right', end: 'right left', scrub: true } },
            );
        });

        return () => {
            root.classList.remove('is-horizontal');
            panels.forEach((panel) => panel.classList.remove('is-active'));
            gsap.set([track, frame], { clearProps: 'all' });
        };
    });

    // ── Narrower screens: stacked rooms, same progress model ────────────────
    mm.add('(max-width: 1023px)', () => {
        const triggers = panels.flatMap((panel, i) => [
            ScrollTrigger.create({
                trigger: panel.querySelector('[data-room-stage]'),
                start: 'top bottom',
                end: 'bottom top',
                onUpdate: (self) => illusions[i]?.setProgress(self.progress),
            }),
            ScrollTrigger.create({
                trigger: panel,
                start: 'top 75%',
                onEnter: () => panel.classList.add('is-active'),
            }),
        ]);

        return () => triggers.forEach((t) => t.kill());
    });
}
