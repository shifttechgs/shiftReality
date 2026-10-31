import { initDropdowns } from './ui/select.js';
import { initDatePickers } from './ui/date-picker.js';

/**
 * The home hero's booking card (partials/hero-book): the time list follows
 * the picked day (each day option carries its open slots), − / + keep the
 * guests within 1 … max, and the total follows. Day opens as a calendar of
 * the bookable fortnight (ui/date-picker.js), Time as the site's own
 * dropdown (ui/select.js). The server checks the pick either
 * way (BookingController@send).
 */
export function initHeroBook() {
    const form = document.querySelector('[data-hero-book]');
    if (!form) return;

    const day    = form.querySelector('[data-hero-day]');
    const time   = form.querySelector('[data-hero-time]');
    const people = form.querySelector('[data-hero-people]');
    const total  = form.querySelector('[data-hero-total]');
    if (!day || !time || !people) return;

    const price = parseFloat(form.dataset.price) || 0;
    const min   = parseInt(people.min, 10) || 1;
    const max   = parseInt(people.max, 10) || 99;
    const money = (n) => '$' + (Number.isInteger(n) ? n : n.toFixed(2));
    const count = () => Math.min(max, Math.max(min, parseInt(people.value, 10) || min));

    // Keep the picked time if the new day has it, else its first slot
    const fillTimes = () => {
        const slots = JSON.parse(day.selectedOptions[0]?.dataset.slots || '[]');
        const kept  = time.value;
        time.replaceChildren(...slots.map((slot) => new Option(slot, slot, false, slot === kept)));
    };

    const update = () => { if (total) total.textContent = money(price * count()); };

    day.addEventListener('change', fillTimes);
    people.addEventListener('input', update);
    people.addEventListener('change', () => { people.value = count(); update(); });
    form.querySelectorAll('[data-hero-count]').forEach((btn) => btn.addEventListener('click', () => {
        people.value = Math.min(max, Math.max(min, count() + parseInt(btn.dataset.heroCount, 10)));
        update();
    }));

    initDatePickers(form);
    initDropdowns(form);
}
