# Mwanafunzi Investor architecture audit

**Audit date:** 2026-10-21  
**Application:** Laravel application at `C:\xampp\htdocs\Mwanafunzi-investor-fresh`  
**Baseline revision:** `28c9df6`  
**Working tree:** remediation changes are intentionally uncommitted; the pre-existing nested `push/` directory was not modified.

## Executive result

The application is operational locally and the prioritized remediation set is regression-clean. The final Laravel suite passes **127 tests / 1,173 assertions**. The production asset build, Blade view cache, Composer validation, route inventory, HTTP smoke checks and whitespace validation also pass.

The repository has **313 registered routes**. Twenty-one representative public, legal, system, academy, Studio and blog URLs returned HTTP 200 through the local server at `http://127.0.0.1:8000`.

## Prioritized changes implemented

| Area | Finding / risk | Change | Evidence |
|---|---|---|---|
| Commerce | Sandbox payment selection could fail open in an unsafe deployment | Added `COMMERCE_SANDBOX_ENABLED` and fail-closed gateway resolution; sandbox endpoints return 404 when disabled | `SecurityHardeningTest::test_sandbox_gateway_is_disabled_when_configuration_marks_it_unsafe`; `CommerceTest` all pass |
| Uploads | Studio reference files were limited per file but not in aggregate | Added configurable 32 MB combined request limit | `StudioInquiryTest::test_reference_files_have_a_combined_request_size_limit` |
| Sessions | Profile password changes did not revoke other sessions | Clear remember token, call `logoutOtherDevices`, regenerate current session for customer and admin profile changes | Full suite; authentication and portal tests pass |
| Output encoding | CMS values in JSON-LD lacked script-context hex escaping | Added `JSON_HEX_TAG`, `JSON_HEX_AMP`, `JSON_HEX_APOS`, `JSON_HEX_QUOT` | SEO and JSON-LD tests pass |
| Redirects | Rate-limit handling trusted a raw external `Referer` | Normalize to an internal path with a home fallback | `SecurityHardeningTest::test_newsletter_rate_limit_never_redirects_to_an_external_referer` |
| Cookies | `.env.example` explicitly disabled secure session cookies | Changed template default to `true` and documented the local HTTP override | Configuration review; local `.env` remains intentionally local HTTP-compatible |
| Privacy | Authenticated pages lacked explicit no-store policy | Added private no-store headers to account, admin, checkout, payment and download surfaces | `SecurityHardeningTest::test_authenticated_surfaces_are_not_cached` |
| Newsletter | Confirmation token was dropped by mass-assignment protection | Persist token with `forceFill` | `NewsletterCookieConsentTest::test_confirmed_newsletter_keeps_a_usable_unsubscribe_token` |
| Admin media performance | Media cards performed repeated reference-count queries | Bulk-loaded usage counts for the current page while retaining the legacy one-off method | `MediaPerformanceTest::test_media_usage_counts_are_bulk_loaded_without_changing_reference_totals` |

## Security review

A repository-wide Codex Security scan was completed against the baseline snapshot. Scan ID:

`8a812880-32b9-421c-a235-b6c82af8654b`

The scan reported nine source-backed occurrences across eight underlying issues: two high, four medium and three low severity. The baseline issues are the items listed above; the working-tree remediations were verified separately because the scan snapshot was captured before implementation. The sealed scan artifacts are stored by the security workbench, including SARIF and Markdown reports.

The review also confirmed no evidence of SQL injection, command injection, path traversal, committed secrets, obvious IDOR in the reviewed customer/admin flows, missing CSRF protection on normal web forms, or missing webhook signature verification.

## Database and performance review

- All migrations are applied locally through `2026_10_19_000001_add_status_to_media`.
- Existing ownership checks and pivot relationships were preserved; no production records were deleted or rewritten.
- The media index N+1 query pattern was removed without changing displayed usage totals.
- The application continues to use the existing database-backed session, cache and queue architecture; no schema rewrite was required for this audit.
- The main remaining performance opportunities are operational: add production query telemetry, review indexes with production cardinalities, and move large media processing to a queue-backed object-storage workflow before scale-up.

## Functional and route evidence

The following route families were exercised by the existing feature suite and local smoke checks:

- Public website, legal pages, blog, Academy and Creative Studio
- Authentication, password reset, MFA and authorization boundaries
- Customer portal, course progress, downloads and payment-return states
- Admin CMS, pages, media, users, enquiries, newsletter, SEO and settings
- Commerce checkout, entitlement, refund, duplicate webhook and private download paths
- Health, robots and sitemap endpoints

The full route inventory is covered by `RouteInventoryTest`; the local HTTP smoke set covered 21 URLs with 21 successful responses.

## Files changed

Application changes:

- `.env.example`
- `app/Http/Controllers/AdminProfileController.php`
- `app/Http/Controllers/CommerceController.php`
- `app/Http/Controllers/CustomerAccountController.php`
- `app/Http/Controllers/MediaController.php`
- `app/Http/Controllers/StudioInquiryController.php`
- `app/Http/Middleware/SecurityHeaders.php`
- `app/Models/Media.php`
- `app/Services/NewsletterSubscriptionService.php`
- `app/Services/Payments/PaymentGatewayManager.php`
- `bootstrap/app.php`
- `config/commerce.php`
- `config/security.php`
- `resources/views/admin/media/index.blade.php`
- `resources/views/layouts/public.blade.php`
- `resources/views/public/journal/show.blade.php`
- `resources/views/public/tools/show.blade.php`

Regression coverage:

- `tests/Feature/MediaPerformanceTest.php`
- `tests/Feature/NewsletterCookieConsentTest.php`
- `tests/Feature/SecurityHardeningTest.php`
- `tests/Feature/StudioInquiryTest.php`

## Verification commands

```text
php artisan test                                      PASS: 127 tests, 1,173 assertions
php artisan test --filter='...focused suites...'     PASS: 77 tests, 897 assertions
npm run build                                         PASS: Vite production build
php artisan view:cache                                PASS
composer validate --no-check-publish                 PASS
php artisan route:list --json                         PASS: 313 routes
git diff --check                                      PASS
local HTTP smoke                                      PASS: 21/21 URLs, HTTP 200
```

## Remaining limitations and follow-up priorities

1. External payment provider execution, real email delivery and production deployment were not exercised in the local environment.
2. The upload control now limits each Studio request, but production should still add retention, storage quotas, malware scanning and monitoring.
3. The local browser was available for route smoke testing; exhaustive visual interaction at every desktop/tablet/mobile viewport was not repeated for every route in this audit.
4. The local `.env` intentionally uses HTTP-compatible development settings. Production deployment must set `APP_DEBUG=false`, HTTPS, secure cookies, real secrets and a non-sandbox payment provider.
5. No commit or push was performed as part of this audit. The nested `push/` directory was preserved unchanged.
