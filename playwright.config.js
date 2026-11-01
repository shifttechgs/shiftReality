import { defineConfig, devices } from '@playwright/test';

/**
 * Browser tests (tests/e2e). They run against the local site at
 * http://localhost:8000; if nothing is serving there, `php artisan serve`
 * is started for the run. Run: npx playwright test
 *
 * The booking send test emails for real through the SMTP settings in .env,
 * so it is tagged @smtp: skip it with  npx playwright test --grep-invert @smtp
 */
export default defineConfig({
    testDir: './tests/e2e',
    timeout: 60_000,
    fullyParallel: false,
    workers: 1,
    reporter: [['list']],
    use: {
        baseURL: 'http://localhost:8000',
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure',
    },
    projects: [
        { name: 'desktop', use: { ...devices['Desktop Chrome'], viewport: { width: 1440, height: 900 } } },
        { name: 'phone', use: { ...devices['Pixel 7'] } },
    ],
    webServer: {
        command: 'php artisan serve --port=8000',
        url: 'http://localhost:8000',
        reuseExistingServer: true,
        timeout: 60_000,
    },
});
