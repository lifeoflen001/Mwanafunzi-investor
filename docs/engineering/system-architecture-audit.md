# System architecture audit

Audit date: 2026-10-15
Environment inspected: Windows/XAMPP local checkout (`C:\xampp\htdocs\Mwanafunzi-investor-fresh`)

## Executive summary

Mwanafunzi Investor is a conventional Laravel 12 monolith with server-rendered Blade views, a Vite/Tailwind frontend, and a relational database. That is an appropriate architecture for the current public website, CMS, customer portal, and small commerce workflow. The application does not currently justify microservices, Kubernetes, Redis, Kafka, Elasticsearch, or a distributed database.

The repository already contains meaningful security and commerce controls: separate customer/admin authentication, admin MFA, CSRF protection, route authorization, signed/private downloads, validated uploads, payment idempotency, webhook signature checks, pagination, indexes, and automated feature coverage. The main operational gaps are deployment-owned: CI was absent, production monitoring/alerts are not configured in the repository, and backup/restore execution remains a hosting responsibility.

## Actual architecture

| Layer | Current implementation | Evidence |
| --- | --- | --- |
| Runtime | PHP 8.2.12 locally; Composer requires PHP ^8.2 | `composer.json`, `php -v` |
| Backend | Laravel 12.69.2, MVC controllers/services/models | `composer.lock`, `app/`, `routes/` |
| Rendering | Blade server-rendered HTML; Vite 7 and Tailwind 4 for assets | `resources/views/`, `package.json`, `vite.config.js` |
| Database | MySQL/MariaDB for XAMPP; SQLite in automated tests | `.env`, `phpunit.xml`, `database/migrations/` |
| Sessions/cache/queue | Database-backed by default; array/sync in tests | `config/session.php`, `config/cache.php`, `config/queue.php`, `.env.example` |
| Storage | Public media disk plus private commerce disk | `app/Services/MediaService.php`, `app/Services/DownloadService.php` |
| Authentication | Cookie sessions, customer/admin boundaries, email verification, admin TOTP MFA | `routes/web.php`, `app/Http/Middleware/`, auth controllers |
| Integrations | Flutterwave gateway, optional reCAPTCHA, mail, optional CDN | `app/Services/Payments/`, `app/Services/RecaptchaService.php`, `config/` |
| Operations | Apache/XAMPP locally; Apache or Nginx compatible deployment documented | `HOSTING.md`, `public/.htaccess` |

## Request and data flow

1. Apache sends requests to `public/index.php`.
2. Laravel boots middleware, including request correlation and security headers.
3. Routes dispatch to public, customer, admin, CMS, or commerce controllers.
4. Services perform validation, authorization, transactions, external calls, and persistence.
5. Blade renders the response; Vite produces immutable production assets.
6. Database queue rows support asynchronous mail/work when a worker is configured.

## Findings and decisions

| Finding | Risk | Decision |
| --- | --- | --- |
| No repository CI workflow existed | Medium | Added `.github/workflows/ci.yml` for locked dependency installation, formatting, tests, build, and dependency audits. |
| Health endpoint checked database but did not distinguish process liveness from dependency readiness | Medium | Preserved `/health` and added `/health/live` and `/health/ready`. |
| Logs had no application-level request correlation field | Medium | Added `RequestContext` middleware with validated `X-Request-Id` propagation and log context. The context records the route name/template rather than raw token-bearing URLs. |
| Production backups, alert routing, queue workers, and TLS are host-owned | High if omitted in production | Documented as deployment gates; no fake infrastructure was added. |
| Single relational application is the current architecture | Low | Keep the monolith. Revisit distributed infrastructure only after measured workload evidence. |

## Verification baseline

- `php artisan test`: 92 tests, 913 assertions passed in 46.38 seconds.
- `composer audit --no-interaction --format=summary`: no security vulnerability advisories.
- `npm audit --omit=dev --audit-level=high`: 0 vulnerabilities.
- `npm run build`: passed locally.
- `git diff --check`: passed before this audit implementation.

These are local verification results, not production availability or load guarantees.

## Residual risks

- There is no repository-managed production backup target or restore test; configure and test this with the host before launch.
- There is no external metrics/alerting backend; operational alert ownership must be agreed before production.
- Apache, PHP-FPM/worker capacity, TLS, DNS, SMTP, Flutterwave production credentials, and file permissions remain deployment concerns.
- Performance numbers for real traffic, P95/P99 latency, queue depth, and Core Web Vitals still require a representative staging environment.
