/**
 * Exhibits (/experience): the rooms as a scroll story, no GSAP.
 *
 * Desktop: as each room's words reach the middle of the screen, it becomes
 * the active room. Its photo wipes up over the last in the pinned frame
 * (CSS transitions on clip-path), the counter and the names under the frame
 * follow, and a name click scrolls to its room.
 * Also: the "Also inside" row can be dragged sideways with a mouse.
 */
import { prefersReducedMotion } from '../utils/motion.js';

export function initExhibits() {
    initRoomStory();
    initDragRows();
}

function initRoomStory() {
    const section = document.querySelector('[data-ex-rooms]');
    if (!section || !('IntersectionObserver' in window)) return;

    const rooms = [...section.querySelectorAll('[data-ex-room]')];
    const imgs  = [...section.querySelectorAll('[data-ex-img]')];
    const tabs  = [...section.querySelectorAll('[data-ex-tab]')];
    const count = section.querySelector('[data-ex-count]');
    let active  = -1;

    const show = (i) => {
        if (i === active) return;
        active = i;
        // every photo up to the active one is in; later ones wait below,
        // so scrolling back retracts them in turn
        imgs.forEach((img, k) => img.classList.toggle('is-shown', k <= i));
        rooms.forEach((room, k) => room.classList.toggle('is-active', k === i));
        tabs.forEach((tab, k) => tab.classList.toggle('is-active', k === i));
        if (count) count.textContent = String(i + 1);
    };

    section.classList.add('is-live');
    show(0);

    // a room is active while its words cross a thin band at mid-screen
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) show(Number(entry.target.dataset.exRoom));
        });
    }, { rootMargin: '-45% 0px -45% 0px' });
    rooms.forEach((room) => io.observe(room));

    // the names under the frame: go to that room (through Lenis when it's running)
    tabs.forEach((tab) => {
        tab.addEventListener('click', async () => {
            const room = rooms[Number(tab.dataset.exTab)];
            const body = room.querySelector('.ex-room__body');
            const y = body.getBoundingClientRect().top + window.scrollY - window.innerHeight * 0.38;
            try {
                const { default: lenis } = await import('../utils/lenis.js');
                lenis.scrollTo(y, { immediate: prefersReducedMotion() });
            } catch {
                window.scrollTo({ top: y, behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
            }
        });
    });
}

/** [data-drag-row]: a sideways row a mouse can drag (touch already swipes) */
function initDragRows() {
    document.querySelectorAll('[data-drag-row]').forEach((row) => {
        let startX = 0, startLeft = 0, moved = false, down = false;

        row.addEventListener('pointerdown', (e) => {
            if (e.pointerType !== 'mouse' || e.button !== 0) return;
            down = true; moved = false;
            startX = e.clientX; startLeft = row.scrollLeft;
        });

        row.addEventListener('pointermove', (e) => {
            if (!down) return;
            const dx = e.clientX - startX;
            if (!moved && Math.abs(dx) > 4) {
                moved = true;
                row.classList.add('is-dragging');
                row.setPointerCapture(e.pointerId);
            }
            if (moved) row.scrollLeft = startLeft - dx;
        });

        const end = () => {
            if (!down) return;
            down = false;
            row.classList.remove('is-dragging');
        };
        row.addEventListener('pointerup', end);
        row.addEventListener('pointercancel', end);
    });
}
