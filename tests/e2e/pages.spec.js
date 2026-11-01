import { test, expect } from '@playwright/test';

// The content pages: each opens, nothing spills sideways, and the links work.
for (const path of ['/experience', '/visit', '/faq', '/groups', '/pricing', '/book']) {
    test(`${path} renders without sideways scroll`, async ({ page }) => {
        await page.goto(path);
        await expect(page.locator('h1')).toBeVisible();
        const overflow = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
        expect(overflow).toBeLessThanOrEqual(0);
    });
}

test('FAQ opens one answer at a time', async ({ page }) => {
    await page.goto('/faq');
    const questions = page.locator('.faq__question');
    await expect(questions.first()).toHaveAttribute('aria-expanded', 'true');
    await questions.nth(2).click();
    await expect(questions.nth(2)).toHaveAttribute('aria-expanded', 'true');
    await expect(questions.first()).toHaveAttribute('aria-expanded', 'false');
});

test('a group enquiry without the basics comes back with messages', async ({ page }) => {
    await page.goto('/groups');
    await page.getByText('Birthdays', { exact: true }).last().click();
    await page.getByRole('button', { name: 'Send enquiry' }).click();
    await expect(page).toHaveURL(/#enquire$/);
    await expect(page.locator('.gp-form__errors')).toContainText('Tell us roughly how many are coming.');
    await expect(page.locator('.gp-form__errors')).toContainText('Tell us who to ask for.');
    // the chosen occasion is kept
    await expect(page.locator('input[name="occasion"][value="Birthdays"]')).toBeChecked();
});

// Emails for real through the SMTP settings in .env: npm run test:e2e:send
test('a group enquiry is sent @smtp', async ({ page }, info) => {
    test.skip(info.project.name !== 'desktop', 'one real email is enough');
    await page.goto('/groups');
    await page.getByText('Team Building', { exact: true }).last().click();
    await page.locator('input[name="size"]').fill('24');
    await page.locator('input[name="name"]').fill('Playwright Test');
    await page.locator('input[name="organisation"]').fill('ShiftTech');
    await page.locator('input[name="phone"]').fill('077 123 4567');
    await page.locator('input[name="email"]').fill('shifttechgs1@gmail.com');
    await page.locator('textarea[name="note"]').fill('Automated Playwright test enquiry. Please ignore.');
    await page.getByRole('button', { name: 'Send enquiry' }).click();
    await expect(page.locator('.gp-done')).toContainText('Thanks, Playwright.');
});

test('Exhibits: the pinned photo follows the room in view, and the names jump to a room', async ({ page }, info) => {
    test.skip(info.project.name !== 'desktop', 'phones get a swipe row instead');
    await page.goto('/experience');
    const count = page.locator('[data-ex-count]');
    await expect(count).toHaveText('1');
    await page.locator('[data-ex-tab="2"]').click();
    await expect(count).toHaveText('3', { timeout: 5000 });
    await expect(page.locator('[data-ex-img="2"]')).toHaveClass(/is-shown/);
    await expect(page.locator('[data-ex-img="3"]')).not.toHaveClass(/is-shown/);
});
