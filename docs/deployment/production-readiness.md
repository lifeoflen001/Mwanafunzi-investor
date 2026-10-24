# Production readiness checklist

**Current decision: NOT READY FOR PRODUCTION — VERIFIED.**

The codebase passes its current automated suite and dependency audits, but the local environment and external operational dependencies do not satisfy the release gate.

## Current local baseline

Captured with `php artisan about`, `migrate:status` and `schedule:list`:

- Laravel 12.70.0 / PHP 8.2.12;
- `environment=local`, debug enabled and `APP_URL=http://localhost`;
- MySQL configured; database-backed cache, queue and sessions;
- migrations are up to date in the local database;
- no scheduled tasks are defined;
- storage link exists;
- five products exist locally and none is `available`;
- demo/admin fixture records are present locally.

This is suitable for development verification, not a production launch.

## Release gates

| Gate | Current state | Required evidence before go-live |
|---|---|---|
| Code and migrations | PASS locally | Review migration SQL, rehearse forward/rollback on staging, then take a production backup |
| Automated regression | PASS locally — 152 tests / 1,365 assertions | Run in CI against the deployment commit |
| Dependencies | PASS — Composer and npm audits report zero vulnerabilities | Repeat in CI and lock deployment artifacts |
| Asset build | PASS — `npm run build` | Build in CI/release pipeline and publish `public/build` |
| Environment | BLOCKED | Production `.env`, HTTPS, `APP_DEBUG=false`, canonical URL, secure cookies and trusted proxies |
| Demo data/admins | BLOCKED | Replace/disable demo identities, isolate fixture rows, verify production administrators and MFA |
| Commerce | BLOCKED if enabled | Approved product catalogue/prices/files plus successful, failed, replayed and refunded provider-sandbox flows |
| Mail and queue | PARTIAL locally | Local database worker drained 3 jobs after a newsletter legacy-token fix; no failed jobs remain. SMTP delivery, failure alerting and production supervision remain required |
| External services | NOT VERIFIED | OAuth, payment webhook, CAPTCHA, Tawk/widget and external project links |
| Backups/restore | BLOCKED | `mysqldump`/`mysql` are unavailable in the local environment and no restore rehearsal was performed; use a staging/production backup target |
| Monitoring | PARTIAL | Health probes, 404/500/payment/mail alerts, logs and incident owner |
| Browser/accessibility | PARTIAL | Staging click-through at mobile/tablet/desktop, keyboard, zoom and screen-reader checks |

## Safe deployment sequence

1. Create a database and storage backup and verify that it can be restored.
2. Configure production environment values and secrets; never copy local demo credentials.
3. Deploy source and built assets with the web root set to `public/`.
4. Run `php artisan migrate --force` after reviewing the migration plan.
5. Run `php artisan storage:link` if required and warm config/routes/views only after production values are loaded.
6. Start queue workers and a scheduler/cron process if scheduled publication, mail or maintenance jobs are enabled.
7. Execute health, authentication, contact/newsletter, commerce sandbox and admin authorization smoke checks.
8. Monitor logs, queue failures, mail delivery, payment callbacks and 404/500 rates; keep rollback artifacts available.

Do not run `DevelopmentDemoSeeder` or local demo account seeding against production. Do not publish products, testimonials or project claims without approved source material and ownership verification.

## Phase 2 operational evidence — 24 October 2026

- `php artisan about`: local environment, debug enabled, `APP_URL=http://localhost`, MySQL, database cache/queue/session, local log mailer, and linked `public/storage`. This confirms the environment is not production-ready configuration.
- `php artisan migrate:status`: all migrations reported `Ran` in the local database.
- `php artisan schedule:list`: no scheduled tasks are defined. No cron process was invented or enabled.
- `php artisan queue:monitor default --max=1`: after recovery, database/default reports size 0 and status OK.
- `php artisan app:demo-data cleanup --json`: exact allowlisted local fixtures were inventoried in dry-run mode. No rows were removed.
- Browser evidence and remaining manual coverage are recorded in `docs/audits/functional-test-matrix.md`.

The final release gate remains blocked until staging proves the production configuration, backup restoration, worker/scheduler supervision, external integrations, security matrix and critical browser workflows.
