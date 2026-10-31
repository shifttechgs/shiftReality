/**
 * A calendar over a real <select data-datepicker> whose options are dates
 * (value "Y-m-d"; disabled = closed; data-today="1" marks today). The field
 * stays a button like the site's dropdown; opening it shows those dates,
 * Mon … Sun, four weeks to a page from today's week (‹ › turn the page; the
 * months in view are named over it, and over each month's 1st), so today
 * and the weeks after it are always one click away. Picking sets the
 * select and fires its 'change', so the form and other scripts work as
 * before.
 *
 * Keys: Enter / Space / ↓ open; ← → move a day, ↑ ↓ a week, Home / End the
 * week's ends; Enter picks; Esc closes. Opens upward when there's no room.
 */
const WEEKDAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
let uid = 0;

export function initDatePickers(root = document) {
    root.querySelectorAll('select[data-datepicker]').forEach(enhance);
}

function enhance(select) {
    if (select.dataset.datepickerReady) return;
    select.dataset.datepickerReady = '1';
    const id = `dp-${++uid}`;

    const wrap = document.createElement('div');
    wrap.className = 'dropdown datepicker';
    select.parentNode.insertBefore(wrap, select);
    wrap.append(select);
    select.classList.add('dropdown__native');
    select.tabIndex = -1;
    select.setAttribute('aria-hidden', 'true');

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'dropdown__button';
    button.setAttribute('aria-haspopup', 'dialog');
    button.setAttribute('aria-expanded', 'false');
    button.setAttribute('aria-controls', `${id}-panel`);
    const label = select.closest('label')?.querySelector('.walk__field-label, [data-dropdown-label]');
    if (label) {
        label.id ||= `${id}-label`;
        button.setAttribute('aria-labelledby', `${label.id} ${id}-value`);
    }
    const value = document.createElement('span');
    value.className = 'dropdown__value';
    value.id = `${id}-value`;
    button.append(value);

    const panel = document.createElement('div');
    panel.className = 'dropdown__list datepicker__panel';
    panel.id = `${id}-panel`;
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-label', 'Choose a day');
    panel.hidden = true;
    wrap.append(button, panel);

    select.closest('label')?.addEventListener('click', (e) => {
        if (!wrap.contains(e.target)) { e.preventDefault(); button.focus(); }
    });

    // The dates, as they lie in the week (Mon = 0)
    const dates = [...select.options].map((opt, i) => {
        const [y, m, d] = opt.value.split('-').map(Number);
        const date = new Date(y, m - 1, d);
        return {
            i, opt, date,
            col: (date.getDay() + 6) % 7,
            off: opt.disabled,
            today: opt.dataset.today === '1',
            long: date.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long' }),
        };
    });

    // Pages of four weeks from today's week (so today and the weeks after it
    // are one click away), each a grid: weekday heads, then the dates in place
    const PAGE = 28;
    const lead = dates[0]?.col ?? 0;
    const pageOf = (n) => Math.floor((lead + n) / PAGE);
    const pageCount = dates.length ? pageOf(dates.length - 1) + 1 : 1;

    const head = document.createElement('div');
    head.className = 'datepicker__head';
    const prev = document.createElement('button');
    const next = document.createElement('button');
    [prev, next].forEach((btn, k) => {
        btn.type = 'button';
        btn.className = 'datepicker__page';
        btn.innerHTML = k ? '&rsaquo;' : '&lsaquo;';
        btn.setAttribute('aria-label', k ? 'Later dates' : 'Earlier dates');
    });
    const title = document.createElement('span');
    title.className = 'datepicker__title';
    title.setAttribute('aria-live', 'polite');
    head.append(prev, title, next);
    if (pageCount < 2) { prev.hidden = true; next.hidden = true; }

    const pages = Array.from({ length: pageCount }, (_, k) => {
        const grid = document.createElement('div');
        grid.className = 'datepicker__grid';
        grid.setAttribute('role', 'grid');
        WEEKDAYS.forEach((w, c) => {
            const s = document.createElement('span');
            s.className = 'datepicker__wd' + (c > 4 ? ' is-weekend' : '');
            s.textContent = w.slice(0, 2);
            s.setAttribute('aria-hidden', 'true');
            grid.append(s);
        });
        if (k === 0) for (let c = 0; c < lead; c++) grid.append(document.createElement('span'));
        grid.hidden = true;
        return grid;
    });

    const cells = dates.map((d, n) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'datepicker__day' + (d.col > 4 ? ' is-weekend' : '') + (d.today ? ' is-today' : '');
        b.tabIndex = -1;
        b.dataset.index = String(n);
        // the month's name over the first date in view on each page, and over each 1st
        const first = n === 0 || d.date.getDate() === 1 || pageOf(n) !== pageOf(n - 1);
        b.innerHTML = (first ? `<span class="datepicker__month">${d.date.toLocaleDateString('en-GB', { month: 'short' })}</span>` : '')
            + `<span class="datepicker__n">${d.date.getDate()}</span>`
            + (d.today ? '<span class="datepicker__dot" aria-hidden="true"></span>' : '');
        b.setAttribute('aria-label', `${d.today ? 'Today, ' : ''}${d.long}${d.off ? ', closed' : ''}`);
        if (d.off) { b.disabled = true; b.classList.add('is-off'); }
        pages[pageOf(n)].append(b);
        return b;
    });

    let page = -1;
    const showPage = (k) => {
        k = Math.max(0, Math.min(pageCount - 1, k));
        if (k === page) return;
        page = k;
        pages.forEach((g, j) => { g.hidden = j !== k; });
        const inView = dates.filter((_, n) => pageOf(n) === k);
        title.textContent = [...new Set(inView.map((d) => d.date.toLocaleDateString('en-GB', { month: 'long' })))].join(' to ');
        prev.disabled = k === 0;
        next.disabled = k === pageCount - 1;
    };
    prev.addEventListener('click', () => showPage(page - 1));
    next.addEventListener('click', () => showPage(page + 1));

    const foot = document.createElement('p');
    foot.className = 'datepicker__foot';
    foot.textContent = 'Greyed-out days are closed.';
    panel.append(head, ...pages);
    if (dates.some((d) => d.off)) panel.append(foot);

    let focusAt = -1;

    const sync = () => {
        const i = select.selectedIndex;
        value.textContent = select.options[i]?.textContent.trim() ?? '';
        cells.forEach((c, n) => c.setAttribute('aria-pressed', String(n === i)));
    };

    const focusCell = (n) => {
        if (!cells[n]) return;
        showPage(pageOf(n));   // stepping past a page's end turns the page
        if (cells[focusAt]) cells[focusAt].tabIndex = -1;
        focusAt = n;
        cells[n].tabIndex = 0;
        cells[n].focus({ preventScroll: true });
    };

    // Step through the dates, skipping closed ones, staying inside the window
    const move = (from, step) => {
        let n = from + step;
        while (cells[n] && cells[n].disabled) n += Math.sign(step);
        return cells[n] ? n : from;
    };

    const open = () => {
        if (!panel.hidden) return;
        const r = button.getBoundingClientRect();
        const need = 340;
        wrap.classList.toggle('is-up', window.innerHeight - r.bottom < need && r.top > need);
        panel.hidden = false;
        wrap.classList.add('is-open');
        button.setAttribute('aria-expanded', 'true');
        focusCell(select.selectedIndex >= 0 ? select.selectedIndex : move(-1, 1));
        document.addEventListener('pointerdown', outside, true);
    };

    const close = (refocus = true) => {
        if (panel.hidden) return;
        panel.hidden = true;
        wrap.classList.remove('is-open');
        button.setAttribute('aria-expanded', 'false');
        document.removeEventListener('pointerdown', outside, true);
        if (refocus) button.focus({ preventScroll: true });
    };

    const outside = (e) => { if (!wrap.contains(e.target)) close(false); };

    const pick = (n) => {
        if (!cells[n] || cells[n].disabled) return;
        if (select.selectedIndex !== n) {
            select.selectedIndex = n;
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }
        sync();
        close();
    };

    button.addEventListener('click', () => (panel.hidden ? open() : close()));
    button.addEventListener('keydown', (e) => {
        if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(e.key)) { e.preventDefault(); open(); }
    });

    panel.addEventListener('click', (e) => {
        const cell = e.target.closest('.datepicker__day');
        if (cell) pick(Number(cell.dataset.index));
    });
    panel.addEventListener('keydown', (e) => {
        if (e.target.closest('.datepicker__page')) {   // ‹ › keep their own Enter / Space
            if (e.key === 'Escape') { e.preventDefault(); close(); }
            else if (e.key === 'Tab' && !e.shiftKey && e.target === next) close(false);
            return;
        }
        const n = focusAt;
        const keys = {
            ArrowRight: () => move(n, 1),
            ArrowLeft: () => move(n, -1),
            ArrowDown: () => move(n, 7),
            ArrowUp: () => move(n, -7),
            Home: () => move(Math.max(-1, n - dates[n].col - 1), 1),
            End: () => move(Math.min(cells.length, n + (6 - dates[n].col) + 1), -1),
        };
        if (keys[e.key]) { e.preventDefault(); focusCell(keys[e.key]()); }
        else if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pick(n); }
        else if (e.key === 'Escape') { e.preventDefault(); close(); }
        else if (e.key === 'Tab') close(false);
    });

    select.addEventListener('change', sync);
    sync();
}
