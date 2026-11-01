import { test, expect } from '@playwright/test';

/**
 * The booking feature end to end: the four steps on /book, their checks,
 * the home card's hand-off, and a real send (emailed to booking.email
 * through the SMTP settings in .env).
 */

const step = (page) => page.locator('.bk-step.is-current');
const next = (page) => step(page).locator('[data-next]').click();

/** Day → time → guests: the first open day, its first time, `guests` people. */
async function pickVisit(page, guests = 2) {
    await page.goto('/book');
    await expect(step(page).locator('legend')).toHaveText('Which day suits you?');
    await page.locator('.bk-cal-page:not([hidden]) label.bk-day').first().click();
    await next(page);

    await expect(step(page).locator('legend')).toHaveText('What time will you arrive?');
    await page.locator('.bk-times.is-active label.bk-time').first().click();
    await next(page);

    await expect(step(page).locator('legend')).toHaveText('How many guests?');
    for (let i = 1; i < guests; i++) await page.getByRole('button', { name: 'One more guest' }).click();
    await expect(page.locator('#bk-people')).toHaveValue(String(guests));
    await next(page);

    await expect(step(page).locator('legend')).toHaveText('How do we reach you?');
}

test.describe('Booking', () => {
    test('each step asks for its answer before moving on', async ({ page }) => {
        await page.goto('/book');
        await expect(page.locator('.bk-progress__count')).toContainText('01');

        // Time step without a time
        await page.locator('.bk-cal-page:not([hidden]) label.bk-day').first().click();
        await next(page);
        await next(page);
        await expect(step(page).locator('.bk-step__error')).toHaveText('Pick a time to continue.');
    });

    test('the pass totals the guests at the ticket price', async ({ page }) => {
        await pickVisit(page, 3);
        await expect(page.locator('[data-sum="people"]')).toHaveText('3');
        await expect(page.locator('[data-sum="total"]')).toHaveText('$45');
    });

    test('a phone number is required, and a bad one is refused by the server', async ({ page }) => {
        await pickVisit(page);
        await page.getByLabel('Full name').fill('Playwright Test');

        // Empty phone: stopped in the browser
        await page.getByRole('button', { name: 'Send booking request' }).click();
        await expect(step(page).locator('.bk-step__error')).toHaveText('Add a phone number so we can confirm your booking.');

        // Not a number: the browser lets it through, the server sends it back with the answers kept
        await page.locator('input[name="phone"]').fill('call me');
        await page.getByRole('button', { name: 'Send booking request' }).click();
        await expect(step(page).locator('legend')).toHaveText('How do we reach you?');
        await expect(page.locator('.bk-step__error')).toContainText('phone number doesn');
        await expect(page.getByLabel('Full name')).toHaveValue('Playwright Test');
    });

    test('the home card hands its answers to /book, on the contact step', async ({ page }, info) => {
        test.skip(info.project.name !== 'desktop', 'phones get the short card (next test)');
        // Wait for the hero to settle before typing (one run lost the typed name to a slow load)
        await page.goto('/', { waitUntil: 'networkidle' });
        const card = page.locator('[data-hero-book]');
        await card.locator('input[name="name"]').fill('Playwright Test');
        await card.getByRole('button', { name: 'Book My Spot' }).click();

        await expect(page).toHaveURL(/\/book\?date=\d{4}-\d{2}-\d{2}&time=\d{2}%3A\d{2}&people=1&name=Playwright/);
        await expect(step(page).locator('legend')).toHaveText('How do we reach you?');
        await expect(page.getByLabel('Full name')).toHaveValue('Playwright Test');
        await expect(page.locator('[data-sum="time"]')).not.toHaveText('Not picked');
    });

    test('on phones the short home card carries the day and guests to /book', async ({ page }, info) => {
        test.skip(info.project.name !== 'phone', 'the short card is the phone layout');
        await page.goto('/');
        const quick = page.locator('form.walk__quick');
        const date = await quick.locator('select[name="date"]').inputValue();
        await quick.getByRole('button', { name: 'Book My Spot' }).click();

        await expect(page).toHaveURL(new RegExp(`/book\\?date=${date}&people=1`));
        await expect(step(page).locator('legend')).toHaveText('Which day suits you?');
        await expect(page.locator(`input[name="date"][value="${date}"]`)).toBeChecked();
    });

    test('sending emails the request and shows the confirmation @smtp', async ({ page }, info) => {
        test.skip(info.project.name !== 'desktop', 'one real email per run is enough');
        test.setTimeout(120_000);

        await pickVisit(page, 2);
        await page.getByLabel('Full name').fill('Playwright Test');
        await page.locator('input[name="phone"]').fill('077 123 4567');
        await page.locator('input[name="email"]').fill('shifttechgs1@gmail.com');
        await page.locator('textarea[name="note"]').fill('Automated Playwright test booking. Please ignore.');
        await page.getByRole('button', { name: 'Send booking request' }).click();

        // A failed send comes back to the form with an error instead
        await expect(page.locator('.bk-done__label')).toHaveText('Request sent', { timeout: 90_000 });
        await expect(page.locator('.bk-done__title')).toHaveText("Thanks, Playwright. We've got it.");
        await expect(page.locator('.bk-done')).toContainText('077 123 4567');
        await expect(page.locator('.bk-done .bk-pass__amount')).toHaveText('$30');
    });
});
