/**
 * Live venue-local time for <time data-local-clock data-tz="Africa/Harare">.
 * The server renders the time on load; this keeps it ticking, on the minute.
 */
export function initLocalClock() {
    const clocks = [...document.querySelectorAll('[data-local-clock]')];
    if (!clocks.length) return;

    const tick = () => {
        const now = new Date();
        clocks.forEach((el) => {
            try {
                el.textContent = new Intl.DateTimeFormat('en-GB', {
                    timeZone: el.dataset.tz, hour: '2-digit', minute: '2-digit', hour12: false,
                }).format(now);
                el.dateTime = now.toISOString();
            } catch { /* unknown time zone — keep the server's time */ }
        });
        // next tick lands just after the minute turns
        setTimeout(tick, 60000 - (now.getSeconds() * 1000 + now.getMilliseconds()) + 50);
    };
    tick();
}
