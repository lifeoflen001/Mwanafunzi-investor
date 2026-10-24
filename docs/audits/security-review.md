# Security review — source-backed release notes

This is a source and automated-test review, not a penetration test. The supplied pre-launch audit did not inspect source or submit requests. Findings below are limited to controls evidenced in the current Laravel code and tests.

## Controls verified

- **Authentication:** Laravel session authentication, password hashing, verification flows, session invalidation and safe post-authentication continuation are covered by `ClientAuthenticationExperienceTest`, `GoogleAuthenticationTest` and `SecurityHardeningTest`.
- **Authorization:** Admin middleware, client ownership checks, signed previews, customer session ownership, private downloads and final-administrator safeguards are covered by `PlatformRoutesTest`, `CommerceTest` and `ClientAccountEnhancementsTest`.
- **Admin MFA:** Production-required configuration, TOTP challenge, one-time recovery codes and rate limiting are covered by `SecurityHardeningTest`.
- **Client MFA:** setup is secret-safe and enabled MFA requires a challenge/recovery path before workspace access; covered by `ClientAccountEnhancementsTest`.
- **Input/output safety:** Blade output is escaped by default; rich text is passed through `RichText`; comments are tested against unsafe markup; contact messages have bounded input and a honeypot/optional reCAPTCHA path.
- **Abuse controls:** contact, newsletter, comments, reactions and authentication-sensitive routes have throttles; the security tests confirm rate-limit behavior for selected paths.
- **Commerce integrity:** server-side prices, currency/reference validation, signed webhook verification, replay/idempotency protection and refund/entitlement effects are covered by `CommerceTest`.
- **File privacy:** private product assets are not served through the public media route; download access requires an entitlement. Media deletion protects referenced files.
- **Secrets and settings:** reCAPTCHA secrets are encrypted in the admin settings path and the security settings panel is admin-only.
- **Cookie controls:** optional categories are gated by consent-aware script loading; policy and preference entry points are separate behaviors in the current source.
- **Supply chain:** `composer audit --no-interaction` and `npm audit --omit=dev` both reported no vulnerabilities on 24 October 2026.

## Risks requiring deployment evidence

1. Current local `php artisan about` reports `environment=local`, `APP_DEBUG=enabled`, `APP_URL=http://localhost`, database-backed queue/session/cache and no scheduled tasks. This is not a production configuration.
2. Local database records include demo accounts and demo operational records. They are clearly marked and guarded by seeders, but production identity/data cleanup is still an operator decision.
3. Live mail, queue workers, OAuth credentials, payment credentials, webhook reachability, HTTPS/proxy configuration, backups and monitoring were not verified.
4. The application has broad admin functionality. Automated tests cover important boundaries, but a complete direct-request role matrix for all route mutations remains necessary before production.
5. A public external Lodgix URL is stored in the current project record. Its deployment, data and access policy must be reviewed by its owner before promotion.

## Phase 2 verification evidence — 24 October 2026

- Local admin/public route browser checks completed without exposing a Laravel exception page on the exercised routes.
- Local client authentication reached the MFA challenge before the account workspace; no bypass or recovery code was attempted.
- The local queue worker exposed a real lifecycle-mail defect: a legacy subscriber with a null encrypted `unsubscribe_token` caused URL generation to fail. The model now backfills a random token and matching hash at the mail boundary; the regression test passes and the two failed local jobs were recovered.
- The demo-data command is restricted to `local`, `testing` and `staging`, uses exact allowlists, reports before mutation and refuses production. It was not run with `--apply`.
- Dependency audits remain clean. This is not a substitute for a penetration test or staging authorization matrix.
- A repository-wide Codex Security Standard scan was started against the current worktree. Preflight passed and a provisional threat model was recorded, but the source-review result was not finalized in this phase; therefore no “no vulnerabilities found” claim is made from that scan.

## Required production checks

- set `APP_ENV=production`, `APP_DEBUG=false`, a canonical HTTPS `APP_URL`, secure session cookies and trusted proxy settings;
- use a production database and reviewed migration plan; do not run local/demo seeders;
- point the web server document root at Laravel `public/` and keep `.env`, storage-private files and source outside public access;
- configure and verify queue workers, scheduler/cron, mail SPF/DKIM/DMARC, OAuth, reCAPTCHA, payment callbacks, log rotation and alerts;
- rehearse backup restore, migration rollback and incident contact ownership;
- execute the direct-request authorization, accessibility and browser matrix in a staging environment.

The local environment cannot verify backup restoration, production TLS/proxy behavior, external credential ownership, third-party callbacks, or operational alert delivery. Those remain release blockers.
