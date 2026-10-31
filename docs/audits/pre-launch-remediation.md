# Mwanafunzi Investor — Pre-launch remediation register

**Review date:** 24 October 2026
**Source audit:** `C:/Users/Admin/Downloads/Mwanafunzi_Investor_Prelaunch_Audit_2026-10-24.md`
**Current codebase:** local Laravel application at `C:/xampp/htdocs/Mwanafunzi-investor-fresh`

## Evidence boundary

The supplied audit observed only an authenticated admin dashboard and one public Digital Software project page. It did not submit forms, inspect the database, run tests, inspect source, or perform browser automation. The classifications below therefore distinguish source-confirmed defects from release decisions and unexecuted operational checks. A passing automated test is not represented as a production or external-provider verification.

## Finding register

| ID | Status after source review | Evidence and action |
|---|---|---|
| MI-001 | **Deployment decision required** | Local data contains demo administrators and clearly marked demo records. `DatabaseSeeder` limits its demo accounts to local, while `DevelopmentDemoSeeder` refuses production and marks fixtures. Existing local rows must be isolated or replaced with verified production identities before launch; they were not deleted because the instruction is to preserve legitimate data. |
| MI-002 | **Partially verified; full role matrix remains required** | Existing feature tests cover admin boundaries, customer ownership, client MFA, customer IDOR, admin MFA and signed previews. A complete direct-request matrix for every admin role and every mutating endpoint was not run in this environment. |
| MI-003 | **Sandbox lifecycle verified; production payment launch remains blocked** | `CommerceTest` covers server-side price snapshots, unavailable products, idempotent sandbox payment, amount/currency/reference rejection, signed Flutterwave webhook replay protection, provider failure, refunds and entitlement revocation. Live provider credentials, real callbacks, concurrency and reconciliation were not verified. |
| MI-004 | **Fixed and verified** | The dashboard counted unread `new` and `in_progress` messages but linked only to `status=new`. `AdminController` now links to `admin.messages?unread=1`; the inbox validates and applies `whereNull('read_at')`. `CurrentAuditFixesTest` proves an unread in-progress message is included and a read in-progress message is excluded. |
| MI-005 | **Deployment/business decision required** | The local catalogue has five products and zero `available` products; availability is `coming_soon`, `waitlist`, or `draft`. Checkout correctly refuses unavailable products. Supply approved prices/files and publish only intended products, or label the catalogue pre-launch before enabling commerce. |
| MI-006 | **Source mitigation verified; abuse testing remains required** | Contact input is capped at 5,000 characters, rate-limited, optionally reCAPTCHA-protected, and `ContactMessage::preview()` normalizes line breaks, strips tags and squishes whitespace before display. The observed URL-heavy record is a spam signal, not proof of compromise. Add staging abuse/load testing before launch. |
| MI-007 | **Partially verified** | Public draft exclusion and sitemap exclusion are covered by `PlatformRoutesTest` and `SeoTest`. Scheduling, timezone, cancellation, revision history and cache invalidation were not browser-tested and remain release checks. |
| MI-008 | **Code-level queue blocker fixed; external delivery remains required** | Newsletter confirmation, pending state, duplicates, unsubscribe tokens, rate limiting, admin authorization and public footer controls are covered by `NewsletterCookieConsentTest`. A local worker exposed a legacy-row failure when `unsubscribe_token` was null; the mail boundary now backfills a token, the regression test passes, and both failed local jobs were retried successfully. SMTP delivery and real sandbox confirmation remain unverified. |
| MI-009 | **Source and feature-test coverage present; browser consent verification remains required** | Cookie categories, switch semantics, modal controls and script gating are implemented and rendered by tests. Cross-session persistence and withdrawal must still be verified in a real browser with each enabled third-party service. |
| MI-010 | **Fixed in current source** | The footer link has `data-cookie-preferences`; `resources/js/app.js` intercepts it and opens the preference modal, while the policy remains a separate route. `NewsletterCookieConsentTest` covers both controls and policy rendering. |
| MI-011 | **Verified intentional alias** | `/admin/journal` and `/admin/articles` share the article controller and preserve their distinct named routes. Existing admin route smoke coverage renders the static destinations. |
| MI-012 | **Resolved in current source** | The current Digital Software project view does not contain the editor-facing “Evidence source” string. The Lodgix record is currently published in the local database; the live URL remains an external deployment decision. |
| MI-013 | **Verified as current design** | The project detail template intentionally renders related published Digital Software work when available. This is covered by the project route tests and is not a defect in the current implementation. |
| MI-014 | **Deployment decision required** | The current local Lodgix record is `published` with `https://lodgix.up.railway.app` as its live URL. The application cannot verify the external deployment’s status, data, access controls or suitability for production; label and expose it only after an owner verifies that deployment. |
| MI-015 | **Partially verified** | Liveness/readiness endpoints exist and are covered by `SecurityHardeningTest`; the admin shell still presents “System online” as a UI status, not as a live health probe. Integrate or relabel it before relying on it operationally. |
| MI-016 | **Partially verified; local browser pass completed** | Server-rendered admin/public routes, form validation, authorization, focus-related cookie modal behavior and responsive primitives have automated coverage. A local browser pass rendered all major public groups and authenticated admin destinations, exercised FAQ create/edit, empty contact validation and persistence, blog comment validation, and the client MFA gate. Full device matrix, keyboard/zoom/screen-reader pass and staging acceptance remain open. |

## Change made in this remediation pass

- `app/Http/Controllers/AdminController.php`: changed both dashboard “Unread enquiries” destinations to use an explicit unread filter and added boolean validation/query handling for `unread`.
- `tests/Feature/CurrentAuditFixesTest.php`: added regression coverage for unread `in_progress` and already-read `in_progress` messages.
- `app/Models/NewsletterSubscriber.php`: added a guarded legacy-token backfill for subscribers whose encrypted unsubscribe token is missing.
- `app/Mail/NewsletterConfirmationMail.php` and `app/Mail/NewsletterConfirmedMail.php`: lifecycle links now use the ensured token instead of generating an invalid route.
- `tests/Feature/NewsletterCookieConsentTest.php`: added regression coverage for rendering a confirmation mail from a legacy pending subscriber.
- `app/Console/Commands/DemoDataCommand.php`: added an environment-guarded, exact-selector inventory/dry-run cleanup command for local, testing and staging fixtures. It was only run in report/dry-run mode; no rows were deleted.
- `tests/Feature/DemoDataCommandTest.php`: verifies exact-selector reporting is non-mutating and production cleanup is refused.
- `bootstrap/app.php`: registers the demo-data command in the application console bootstrap.

No demo or legitimate database records were deleted, and no product was made purchasable without approved commercial data.

## Verification commands

```text
php artisan test --no-ansi                 PASS — 152 tests, 1,365 assertions
composer audit --no-interaction             PASS — no security vulnerability advisories
npm audit --omit=dev                         PASS — 0 vulnerabilities
npm run build                                PASS — Vite production build
php artisan optimize                         PASS locally; caches cleared afterward
git diff --check                             PASS
php artisan route:list --except-vendor      PASS — 327 registered routes
```

The full route and workflow matrix is in `docs/audits/functional-test-matrix.md`. Deployment blockers and operator actions are in `docs/deployment/production-readiness.md`.

## Phase 2 verification evidence — 24 October 2026

### Outstanding-issues register outcome

| Area | Phase 2 result | Remaining dependency |
|---|---|---|
| Reproducible code blockers | Newsletter queue URL-generation blocker fixed and regression-tested; full suite passes | None for this local defect |
| Browser workflows | Local browser checks completed for public journeys, admin read-only surfaces, FAQ CRUD, contact validation/persistence, blog comment validation and client MFA gate | Staging browser/device matrix and full mutation matrix |
| Demo data | `php artisan app:demo-data cleanup --json` reported exact allowlisted fixtures; no cleanup applied | Owner-approved staging/production fixture isolation |
| Scheduler | `php artisan schedule:list` reports no scheduled tasks | Define and operate cron only if scheduled features are enabled |
| Queue/failures | Queue worker initially exposed two failed newsletter jobs; after fix, retry completed and `queue:failed` is empty; `queue:monitor` reports 0 pending | Production worker supervision and alerting |
| Storage/migrations | Migrations up to date; `public/storage` link exists | Production permissions and deployment rehearsal |
| Backups/restore | Local backup tooling is unavailable (`mysqldump`/`mysql` not present); no restore rehearsal performed | Staging/production database and storage backup target |
| External integrations | Mail is local `log`, queue is database, and no live provider credentials were used | SMTP, OAuth, payment callbacks, CAPTCHA, Tawk and monitoring credentials/configuration |

### Browser evidence

- Admin routes `/admin`, `/admin/messages`, `/admin/studio/inquiries`, `/admin/faqs`, `/admin/media`, `/admin/courses`, `/admin/products`, `/admin/articles`, `/admin/pages`, `/admin/customers` and `/admin/newsletter` rendered without a 500/error page.
- Public routes `/`, `/financial-academy`, `/financial-academy/courses`, `/financial-academy/tools`, `/digital-software`, `/creative-studio`, `/creative-studio/services`, `/blog`, `/about`, `/contact` and all listed legal pages rendered with expected headings. The former `/digital-software/hospitality` page was removed from the public route inventory.
- FAQ create and edit completed in the browser with success feedback; no record was deleted.
- Contact form empty-submit validation fired in the browser. A controlled `.test` contact submission persisted as `new` with consent in the local database; immediate browser confirmation was inconclusive after a browser protocol timeout, so this is not claimed as a browser success message.
- Blog comment empty-submit validation fired; no comment was submitted.
- Client login reached the existing MFA verification gate; no MFA bypass was attempted.

### Safe fixture inventory

The dry-run inventory reported: users 4, services 2, projects 1, testimonials 23, courses 1, articles 1, products 1, orders 1, contact messages 2, newsletter subscribers 2, Studio inquiries 1 and pages 1. The selectors are explicit and the command refuses production; legitimate records were not deleted.
