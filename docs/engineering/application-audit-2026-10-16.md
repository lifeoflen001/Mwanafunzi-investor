# Application architecture audit — 2026-10-16

## Scope

Repository-wide review of the Laravel application, including public routes, client and admin authentication, CMS/editorial workflows, media handling, commerce access controls, database migrations/indexes, responsive UI patterns, and production checks. The unrelated untracked `pull/` directory was excluded from the review and remains untouched.

## Prioritized findings and implemented changes

### P0 — reCAPTCHA broke enabled login paths

The client and admin login controllers passed `recaptcha_token` into `Auth::attempt()` along with the email and password. When reCAPTCHA is enabled, Laravel could treat the token as a database credential instead of a validation-only input.

Fixed by removing the token from the credential array before authentication in:

- `app/Http/Controllers/CustomerAuthController.php`
- `app/Http/Controllers/AdminAuthController.php`

Evidence: `SecurityHardeningTest` now exercises both enabled client and admin login flows with a valid reCAPTCHA response and confirms authentication succeeds.

### P1 — article media workflow was unreachable from the editor

Article persistence already accepted `featured_image`, and the media API already supported photo/video uploads, previews, and progress reporting. The article form did not render the upload-enabled media component, so the workflow could not be used.

Fixed by adding the upload-enabled `Featured image or video` field to the article form and extending the shared admin media component with the existing inline upload/progress behavior. Existing image/video validation and public rendering were preserved.

Evidence: `PlatformRoutesTest::test_new_post_media_field_can_upload_and_preview_images_or_videos` verifies the editor controls, image upload, video upload, database records, and public-disk storage.

### P1 — media deletion could orphan published references

The deletion guard covered most content references but omitted team portraits and article category/tag Open Graph images. The media usage counter was inconsistent with the deletion guard.

Fixed by protecting those references in `MediaController` and adding the same taxonomy references to `Media::usageCount()`.

Evidence: `PlatformRoutesTest::test_media_cannot_be_deleted_while_referenced_by_team_or_taxonomy_metadata` confirms a referenced asset is retained and a validation error is returned.

### P1 — local database migration drift

The local database had `2026_10_16_000001_create_social_identities_table` pending while Google authentication code and tests depended on it.

Applied the additive migration locally. `php artisan migrate:status` now reports the migration as `Ran`.

## Existing controls verified

- Admin routes require authentication, admin role, and MFA setup/challenge where configured.
- Customer orders, enrollments, downloads, payments, and lessons enforce ownership or entitlement checks.
- CSRF protection, route throttling, signed download/preview URLs, safe local redirects, and security headers are active.
- Rich text is sanitized before the raw HTML rendering boundary.
- Payment webhook signatures, provider verification, and idempotent completion paths are covered by tests.
- Google sign-in does not authenticate administrator accounts and rejects unsafe return paths.
- Public article discovery uses published records, and SEO/sitemap tests cover canonical public inventory.
- Blog card hover motion is neutralized by the existing editorial card override; no new visual redesign was introduced.

## Verification evidence

- Full test suite: **106 passed, 1,036 assertions**.
- Focused security and platform suite: **59 passed, 766 assertions**.
- Production asset build: `npm run build` passed.
- Composer audit: no security vulnerability advisories.
- `npm audit --omit=dev --audit-level=high`: 0 vulnerabilities.
- `git diff --check`: passed.
- PHP syntax checks for changed controllers: passed.
- Tracked secret-like scan: two matches, both test fixtures in `tests/Feature`; no tracked `.env` or private key was found.

## Known limitations and deferred work

1. The public sitemap and admin SEO inventory materialize many records with `get()`. They are acceptable for the current dataset but should move to chunked/cursor-based inventory or cached generation as content volume grows.
2. Article comments are loaded into an in-memory reply tree. A bounded/paginated comment API is recommended before high-volume public discussion is enabled.
3. Several portal dashboard queries intentionally assemble complete collections for current views. Pagination or summary queries should be added before large customer/order volumes.
4. The stylesheet contains historical layered overrides and multiple design-token surfaces. A dedicated CSS consolidation pass should be isolated from feature work and validated with visual regression screenshots.
5. `vendor/bin/pint --test` reports pre-existing formatting drift across many untouched files. It was not auto-fixed because broad reformatting would create unrelated modifications.
6. Google Search Console, Bing Webmaster Tools, production HTTPS, mail delivery, queue workers, object/private storage, and payment-provider credentials still require deployment-environment verification.

## Release recommendation

The implemented fixes are regression-tested and safe to stage. Before production release, run the deployment checklist in `SECURITY.md` and `HOSTING.md`, apply migrations in the deployment environment, build assets, verify queue/mail/payment integrations, and perform browser checks for the admin media upload and public article hero at mobile and desktop breakpoints.
