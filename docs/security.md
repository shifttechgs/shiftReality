# ShiftReality — Security Policy

## Principles

- Follow Laravel security best practices by default
- Never expose secrets, credentials, or internal paths
- Validate at system boundaries only
- Minimal attack surface — no unnecessary packages or endpoints

## Environment Variables

- All secrets live in `.env` — never committed to version control
- `.env` is in `.gitignore` by default in Laravel
- Use `.env.example` to document required variables without values
- Never hardcode credentials in code, config files, or Blade templates

**Required .env variables:**
```
APP_KEY=        — Auto-generated; must be set before deploying
APP_ENV=        — production / local
APP_DEBUG=      — false in production
APP_URL=        — Full URL including scheme

DB_*            — Only when database is added (future phase)
MAIL_*          — Only when email is configured (future phase)
```

## Laravel Security Defaults (in place)

- [x] CSRF protection — all web forms use `@csrf` automatically via Laravel
- [x] XSS protection — Blade `{{ }}` escapes output by default; `{!! !!}` is used nowhere
- [x] SQL injection — no raw queries; Eloquent ORM will be used (future)
- [x] Clickjacking — set `X-Frame-Options: SAMEORIGIN` via middleware
- [x] Content type sniffing — set via `X-Content-Type-Options: nosniff`
- [x] Route protection — only named routes used; no dynamic route construction

## HTTP Security Headers (to configure before production)

Add the following to `app/Http/Middleware/` or via server config:

```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
Content-Security-Policy: [configure per environment]
Strict-Transport-Security: max-age=31536000; includeSubDomains (HTTPS only)
```

Recommended package: `spatie/laravel-csp` for CSP management.

## Production Checklist

Before going live:

- [ ] `APP_DEBUG=false` in production `.env`
- [ ] `APP_ENV=production`
- [ ] Generated `APP_KEY` (php artisan key:generate)
- [ ] HTTPS enforced (SSL certificate installed)
- [ ] Error pages do not expose stack traces or internal paths
- [ ] `vendor/` not publicly accessible
- [ ] `storage/` not publicly accessible
- [ ] `bootstrap/cache/` not publicly accessible
- [ ] `.env` not publicly accessible
- [ ] HTTP security headers configured
- [ ] `robots.txt` blocks admin/internal paths ✓ (done)
- [ ] No debug packages in production (`laravel/telescope` etc. dev-only)

## Front-End Security

- All user-facing output is escaped via Blade `{{ }}` — no raw HTML injection
- No `eval()` in JavaScript
- No `innerHTML` assignment with untrusted content
- GSAP and canvas animations use no external data sources
- Bunny Fonts loaded via standard `<link>` — no JS font loaders
- No third-party script injection (GTM placeholder is commented out until configured)

## Future Phases — Security Requirements

### When booking system is added:
- HTTPS required for payment flows
- PCI-DSS compliance for any payment card handling (use Stripe/Paynow hosted forms)
- Rate limiting on booking endpoints
- Input validation on all form fields
- CSRF tokens on all forms

### When admin dashboard is added:
- Authentication required (Laravel Auth or Fortify)
- Route middleware: `auth`, `verified`
- Role-based access control if multiple staff users
- Admin routes prefixed and separated from public routes
- Session timeout for admin users

### When email is added:
- SPF, DKIM, DMARC DNS records configured
- No sensitive data in email bodies

## Dependency Security

- Keep Composer and npm packages updated
- Run `composer audit` periodically to check for known vulnerabilities
- Run `npm audit` periodically
- Pin major versions in `composer.json` and `package.json`

## Reporting Security Issues

Internal policy: Report security concerns directly to the development lead before any public disclosure.
