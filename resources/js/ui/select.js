/**
 * The site's own dropdown, over a real <select data-dropdown>: a button
 * showing the picked option and a listbox panel styled like the rest of
 * the site (a native select's open menu belongs to the OS and can't be).
 * The select stays in the form, hidden, and keeps the value: picking an
 * option sets it and fires its 'change', so other scripts and the submit
 * work as before. If the select's options are swapped (the hero's times
 * follow its day), the list rebuilds itself.
 *
 * Keys: Enter / Space / ↓ open, ↑ ↓ Home End move, Enter picks, Esc closes,
 * typing jumps to the first match. Opens upward when there's no room below.
 */
let uid = 0;

export function initDropdowns(root = document) {
    root.querySelectorAll('select[data-dropdown]').forEach(enhance);
}

function enhance(select) {
    if (select.dataset.dropdownReady) return;
    select.dataset.dropdownReady = '1';
    const id = `dd-${++uid}`;

    const wrap = document.createElement('div');
    wrap.className = 'dropdown';
    select.parentNode.insertBefore(wrap, select);
    wrap.append(select);
    select.classList.add('dropdown__native');
    select.tabIndex = -1;
    select.setAttribute('aria-hidden', 'true');

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'dropdown__button';
    button.setAttribute('aria-haspopup', 'listbox');
    button.setAttribute('aria-expanded', 'false');
    button.setAttribute('aria-controls', `${id}-list`);
    // The field's own label names the button
    const label = select.closest('label')?.querySelector('.walk__field-label, [data-dropdown-label]');
    if (label) {
        label.id ||= `${id}-label`;
        button.setAttribute('aria-labelledby', `${label.id} ${id}-value`);
    }
    const value = document.createElement('span');
    value.className = 'dropdown__value';
    value.id = `${id}-value`;
    button.append(value);

    const list = document.createElement('ul');
    list.className = 'dropdown__list';
    list.id = `${id}-list`;
    list.setAttribute('role', 'listbox');
    list.tabIndex = -1;
    list.hidden = true;
    if (label) list.setAttribute('aria-labelledby', label.id);

    wrap.append(button, list);

    // A label wrapping the select would send its clicks to the hidden select
    select.closest('label')?.addEventListener('click', (e) => {
        if (!wrap.contains(e.target)) { e.preventDefault(); button.focus(); }
    });

    let active = -1;
    const items = () => [...list.children];

    const build = () => {
        list.replaceChildren(...[...select.options].map((opt, i) => {
            const li = document.createElement('li');
            li.id = `${id}-o${i}`;
            li.className = 'dropdown__option';
            li.setAttribute('role', 'option');
            li.dataset.value = opt.value;
            li.textContent = opt.textContent.trim();
            if (opt.disabled) li.setAttribute('aria-disabled', 'true');
            return li;
        }));
        sync();
    };

    const sync = () => {
        const i = select.selectedIndex;
        value.textContent = select.options[i]?.textContent.trim() ?? '';
        items().forEach((li, n) => li.setAttribute('aria-selected', String(n === i)));
    };

    const highlight = (i) => {
        const all = items();
        if (!all.length) return;
        active = Math.max(0, Math.min(all.length - 1, i));
        all.forEach((li, n) => li.classList.toggle('is-active', n === active));
        list.setAttribute('aria-activedescendant', all[active].id);
        all[active].scrollIntoView({ block: 'nearest' });
    };

    const open = () => {
        if (!list.hidden) return;
        // Up if the panel wouldn't fit below (it's at most 16rem tall)
        const r = button.getBoundingClientRect();
        const need = Math.min(list.scrollHeight || 256, 256) + 12;
        wrap.classList.toggle('is-up', window.innerHeight - r.bottom < need && r.top > need);
        list.hidden = false;
        wrap.classList.add('is-open');
        button.setAttribute('aria-expanded', 'true');
        highlight(select.selectedIndex);
        list.focus({ preventScroll: true });
        document.addEventListener('pointerdown', outside, true);
    };

    const close = (refocus = true) => {
        if (list.hidden) return;
        list.hidden = true;
        wrap.classList.remove('is-open');
        button.setAttribute('aria-expanded', 'false');
        list.removeAttribute('aria-activedescendant');
        document.removeEventListener('pointerdown', outside, true);
        if (refocus) button.focus({ preventScroll: true });
    };

    const outside = (e) => { if (!wrap.contains(e.target)) close(false); };

    const pick = (i) => {
        const li = items()[i];
        if (!li || li.getAttribute('aria-disabled') === 'true') return;
        if (select.selectedIndex !== i) {
            select.selectedIndex = i;
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }
        sync();
        close();
    };

    button.addEventListener('click', () => (list.hidden ? open() : close()));
    button.addEventListener('keydown', (e) => {
        if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(e.key)) { e.preventDefault(); open(); }
    });

    let typed = '', typedAt = 0;
    list.addEventListener('keydown', (e) => {
        const all = items();
        if (e.key === 'ArrowDown') { e.preventDefault(); highlight(active + 1); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); highlight(active - 1); }
        else if (e.key === 'Home') { e.preventDefault(); highlight(0); }
        else if (e.key === 'End') { e.preventDefault(); highlight(all.length - 1); }
        else if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pick(active); }
        else if (e.key === 'Escape') { e.preventDefault(); close(); }
        else if (e.key === 'Tab') close(false);
        else if (e.key.length === 1) {
            const now = performance.now();
            typed = (now - typedAt > 700 ? '' : typed) + e.key.toLowerCase();
            typedAt = now;
            const hit = all.findIndex((li) => li.textContent.toLowerCase().startsWith(typed));
            if (hit >= 0) highlight(hit);
        }
    });
    list.addEventListener('pointermove', (e) => {
        const li = e.target.closest('.dropdown__option');
        if (li) highlight(items().indexOf(li));
    });
    list.addEventListener('click', (e) => {
        const li = e.target.closest('.dropdown__option');
        if (li) pick(items().indexOf(li));
    });

    select.addEventListener('change', sync);
    new MutationObserver(build).observe(select, { childList: true });
    build();
}
