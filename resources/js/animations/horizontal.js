import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

export function initHorizontal() {
    const section = document.querySelector('.shifts');
    const grid    = document.querySelector('.shifts__grid');
    if (!section || !grid) return;

    const mm = gsap.matchMedia();

    mm.add('(min-width: 769px)', () => {
        const getDistance = () => -(grid.scrollWidth - window.innerWidth);

        gsap.to(grid, {
            x: getDistance,
            ease: 'none',
            scrollTrigger: {
                trigger: section,
                start: 'top top',
                end: () => `+=${Math.abs(getDistance())}`,
                scrub: 1.2,
                pin: true,
                anticipatePin: 1,
                invalidateOnRefresh: true,
            },
        });

        return () => {
            gsap.set(grid, { x: 0 });
        };
    });
}
