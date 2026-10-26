import { prefersReducedMotion } from './utils/motion.js';

/**
 * Booking form (/book): one question at a time — day, time, guests, name —
 * with Continue / Back, a progress count, and the visit pass beside it filling
 * in as each answer comes. The form works without it (all four questions show
 * and the server checks everything on submit).
 */
export function initBooking() {
    const form = document.querySelector('[data-booking]');
    if (!form) return;

    const panels = [...form.querySelectorAll('[data-step-panel]')];
    const price  = parseFloat(form.dataset.price) || 0;
    const people = form.querySelector('input[name="people"]');
    const name   = form.querySelector('input[name="name"]');
    const min    = parseInt(people.min, 10) || 1;
    const max    = parseInt(people.max, 10) || 99;
    const sum    = (key) => form.querySelector(`[data-sum="${key}"]`);
    const money  = (n) => '$' + (Number.isInteger(n) ? n : n.toFixed(2));
    const count  = () => Math.min(max, Math.max(min, parseInt(people.value, 10) || min));
    const picked = (field) => form.querySelector(`input[name="${field}"]:checked`);

    form.classList.add('is-stepped');

    // ── Summary + times ─────────────────────────────────────────────────────
    const update = () => {
        const date = picked('date');

        // Only the picked day's times are shown; a time from another day is dropped
        form.querySelectorAll('[data-times-for]').forEach((group) => {
            const active = group.dataset.timesFor === date?.value;
            group.classList.toggle('is-active', active);
            if (!active) group.querySelectorAll('input:checked').forEach((i) => { i.checked = false; });
        });

        const n = count();
        sum('day').textContent    = date?.dataset.label ?? 'Pick a day';
        sum('time').textContent   = picked('time')?.value ?? '—';
        sum('people').textContent = n;
        sum('name').textContent   = name.value.trim() || '—';
        sum('total').textContent  = money(n * price);
        form.querySelectorAll('[data-count]').forEach((b) => {
            b.disabled = (b.dataset.count < 0 && n <= min) || (b.dataset.count > 0 && n >= max);
        });
    };

    // ── Steps ───────────────────────────────────────────────────────────────
    // What each step needs before Continue; the last one is checked on submit
    const missing = [
        () => !picked('date') && 'Pick a day to continue.',
        () => !picked('time') && 'Pick a time to continue.',
        () => false,
        () => !name.value.trim() && 'Tell us whose name the booking is under.',
    ];

    let current = 0;

    const show = (index, { focus = true } = {}) => {
        current = Math.max(0, Math.min(panels.length - 1, index));
        panels.forEach((panel, i) => panel.classList.toggle('is-current', i === current));
        form.querySelector('[data-step-current]').textContent = String(current + 1).padStart(2, '0');
        form.querySelector('[data-step-bar]').style.transform = `scaleX(${(current + 1) / panels.length})`;

        if (!focus) return;
        // Bring the question into view (the banner can push it below the fold) and focus it
        const top = form.getBoundingClientRect().top;
        if (top < 0 || top > window.innerHeight * 0.6) {
            form.scrollIntoView({ behavior: prefersReducedMotion() ? 'auto' : 'smooth', block: 'start' });
        }
        panels[current].querySelector('legend').focus({ preventScroll: true });
    };

    const flag = (panel, message) => {
        let note = panel.querySelector('[data-step-error]');
        if (!message) { note?.remove(); return; }
        if (!note) {
            note = document.createElement('p');
            note.className = 'bk-step__error';
            note.dataset.stepError = '';
            note.setAttribute('role', 'alert');
            panel.querySelector('.bk-nav').before(note);
        }
        note.textContent = message;
    };

    form.querySelectorAll('[data-next]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const message = missing[current]();
            flag(panels[current], message);
            if (!message) show(current + 1);
        });
    });

    form.querySelectorAll('[data-prev]').forEach((btn) => {
        btn.addEventListener('click', () => show(current - 1));
    });

    form.addEventListener('submit', (e) => {
        const step = missing.findIndex((check) => check());
        if (step === -1) return;
        e.preventDefault();
        show(step);
        flag(panels[step], missing[step]());
    });

    // ── Inputs ──────────────────────────────────────────────────────────────
    form.querySelectorAll('[data-count]').forEach((btn) => {
        btn.addEventListener('click', () => {
            people.value = Math.min(max, Math.max(min, count() + Number(btn.dataset.count)));
            update();
        });
    });

    people.addEventListener('change', () => { people.value = count(); update(); });

    form.addEventListener('change', (e) => {
        update();
        const panel = e.target.closest('[data-step-panel]');
        if (panel) flag(panel, null);
    });
    form.addEventListener('input', (e) => {
        if (e.target === people || e.target === name) update();
        if (e.target === name && name.value.trim()) flag(panels[3], null);
    });

    // Enter on a pick moves on, like pressing Continue
    form.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter' || e.target.type !== 'radio') return;
        e.preventDefault();
        panels[current].querySelector('[data-next]')?.click();
    });

    update();
    // Coming back with a server error: open the first step that has one
    const errored = panels.findIndex((p) => p.querySelector('.bk-step__error'));
    show(errored === -1 ? 0 : errored, { focus: errored !== -1 });
}
