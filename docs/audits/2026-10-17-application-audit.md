# Mwanafunzi Investor application audit — 17 October 2026

## Scope and method

This audit was run against the local XAMPP checkout at `C:\xampp\htdocs\Mwanafunzi-investor-fresh` with the Laravel application booted against the local MySQL database. Existing uncommitted work was preserved. No Git commit, push, reset, or deletion was performed.

The route inventory came from `php artisan route:list --json`. Source tracing covered route actions, middleware, controllers, Blade templates, Eloquent models, validation, relationships and the existing feature-test suite. Local HTTP smoke checks were made against the main public, authentication, CMS, academy, studio, blog and legal surfaces.

## Route inventory

| Area | Registered routes | Evidence |
| --- | ---: | --- |
| Entire application | 313 | `php artisan route:list --json` |
| GET/HEAD-capable | 177 | route inventory |
| Mutating (POST/PUT/PATCH/DELETE) | 136 | route inventory |
| Admin URI namespace | 197 | route inventory |

The inventory includes public pages and redirects, health and SEO endpoints, authentication and verification, academy content, Creative Studio, blog/editorial interactions, checkout/payment/downloads, client portal, CMS, content taxonomy, admin management, newsletter/cookie settings, compliance, SEO and account security.

The complete enumerated inventory, with route-by-route source-integrity and HTTP-smoke status, is in [`2026-10-17-route-inventory.md`](2026-10-17-route-inventory.md). Mutating routes are deliberately marked as workflow-test/not-directly-smoked where a safe authenticated operation was not performed.

## Confirmed defects fixed

### Creative Studio contact-route inconsistency

- Reproduction: open Creative Studio services/testimonial surfaces and follow the primary or placeholder contact action.
- Root cause: the Studio page used the generic `contact?module=studio` route while the implemented workflow is the dedicated `creative-studio.book` enquiry form.
- Fix: Studio hero, collection CTA, testimonial placeholder and Studio service CTA now resolve to the dedicated booking/enquiry route. Files: `resources/views/public/modules/studio.blade.php`, `resources/views/public/modules/collection.blade.php`, `resources/views/public/services/show.blade.php`.

### Malformed enquiry previews

- Reproduction: create or search an enquiry containing literal `\\n`, HTML, or repeated whitespace; open the admin dashboard, inbox, notification popover or notification page.
- Root cause: each view truncated the raw database value independently, so legacy escaped line breaks and markup affected layout.
- Fix: `App\\Models\\ContactMessage::preview()` strips tags, converts literal escaped line breaks and squishes whitespace before truncation. All four admin surfaces use it.

### Enquiry-status counts and action queue

- Reproduction: leave a resolved message unread and open the admin shell/dashboard.
- Root cause: unread counts only checked `read_at`, so a resolved message was counted as work needing attention.
- Fix: notification badge and dashboard unread KPI now count only `new` and `in_progress` messages. The inbox also exposes live counts for New, In progress, Resolved and Spam.

### Awkward waiting labels

- Reproduction: open the dashboard with an active enquiry.
- Root cause: Carbon was asked to describe an older timestamp relative to `now()`, yielding directional phrasing such as “before” in a label already prefixed with “Waiting”.
- Fix: the dashboard now requests an absolute short duration, so the label reads as a clean duration rather than a duplicated direction.

### Product availability management

- Reproduction: open the admin product filter and try to filter Draft, Unavailable or Archived products.
- Root cause: the UI exposed those states, but controller validation accepted only Available, Waitlist and Coming soon.
- Fix: all six supported availability states are accepted. The dashboard’s “Products not available” metric also counts every non-available product instead of omitting Draft and Unavailable records.

### FAQ MySQL date rendering

- Reproduction: open `/admin/faqs` with FAQ records on MySQL.
- Root cause: the SQL `UNION` paginator returns `updated_at` as a string, but the Blade template called `format()` on it.
- Fix: `AdminFaqController` normalizes the union row timestamp to a Carbon instance before rendering.

### Cookie Preferences navigation

- Verification: the footer link keeps the cookie-policy fallback URL and carries `data-cookie-preferences`; the shared JavaScript intercepts it, opens the preferences modal, restores focus and preserves the policy page when JavaScript is unavailable. The cookie policy and preferences controls remain separately accessible.

## Development fixture data

Added `database/seeders/DevelopmentDemoSeeder.php`. It must be run explicitly:

```text
php artisan db:seed --class=DevelopmentDemoSeeder
```

Safety properties:

- Refuses `production`; allowed only in `local`, `testing` or `staging`.
- Uses `updateOrCreate`/`firstOrCreate`; it does not delete or truncate anything.
- Uses `[DEMO]` labels, `example.test` addresses, draft/unpublished content, consent-disabled testimonials and a failed zero-value payment fixture.
- Does not create fictional paid revenue, real customer endorsements, external image downloads or public live offers.
- The local run was executed twice. Counts remained stable:

| Model/area | Count |
| --- | ---: |
| Demo users | 2 |
| Studio services | 2 |
| Studio project | 1 |
| Testimonials | 5 |
| Course | 1 |
| Course module/lesson/FAQ | 1 / 1 / 1 |
| Article/category/tag/comment/reaction | 1 / 1 / 1 / 1 / 1 |
| Product/features/version | 1 / 1 / 1 |
| Order/payment | 1 / 1 |
| Contact enquiry | 1 |
| Newsletter subscriber | 1 |
| Studio enquiry | 1 |
| Draft CMS page/section/FAQ | 1 / 1 / 1 |
| Existing brand media relationship fixture | 1 |

## Test evidence

- `php artisan test --compact`: **111 passed, 1,097 assertions**.
- Added `tests/Feature/RouteInventoryTest.php`: registered controller actions resolve to real methods; public system route smoke test passes.
- Added `tests/Feature/DevelopmentDemoSeederTest.php`: idempotency, explicit demo markers/statuses and production guard pass.
- Existing Studio enquiry workflow tests pass: public submission, persistence, owner portal visibility, admin visibility and status workflow.
- Existing commerce tests pass: server-side pricing, unavailable checkout rejection, sandbox payment idempotency, protected downloads, enrollment/waitlist separation, refunds, webhook verification and private asset upload.
- `npm run build`: **passed** with Vite production assets generated.
- Local HTTP smoke checks returned 200 for home, contact, Creative Studio, Studio booking, Financial Academy, courses, tools, blog, cookie policy, client login and admin login.

## Coverage and limitations

The route inventory is complete, and the existing suite covers the public platform, all static admin GET routes, authentication, CMS CRUD, editorial workflows, commerce, newsletter/cookie controls, SEO and the newly audited Studio workflow. The route-action integrity test covers every registered controller action.

This was not a claim that every mutating route was manually clicked at every viewport. External provider behavior was not exercised against live Google OAuth, reCAPTCHA, email delivery or Flutterwave credentials; those paths are covered with validation, fakes/sandbox tests and authorization tests where available. Visual QA was HTTP/template based in this pass, not a screenshot matrix for all 313 routes at four breakpoints. These are the remaining audit limitations to address before production release.

## Files changed in this audit

- `app/Models/ContactMessage.php`
- `app/Http/Controllers/AdminController.php`
- `app/Http/Controllers/AdminFaqController.php`
- `resources/views/admin/messages/index.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/notifications/index.blade.php`
- `resources/views/layouts/admin.blade.php`
- `resources/views/public/modules/studio.blade.php`
- `resources/views/public/modules/collection.blade.php`
- `resources/css/app.css` (final mobile footer order override)
- `database/seeders/DevelopmentDemoSeeder.php`
- `docs/audits/2026-10-17-route-inventory.md`
- `docs/audits/2026-10-17-application-audit.md`
- `tests/Feature/DevelopmentDemoSeederTest.php`
- `tests/Feature/RouteInventoryTest.php`

The checkout also contains pre-existing, still-uncommitted Studio workflow and platform changes from the earlier task; they were not discarded or committed by this audit.

## Responsive browser checks

The public home/footer was checked locally at 1440, 1024, 768 and 375 CSS pixels. At each width the document had no horizontal overflow; the footer contained the expected navigation groups, contact links and newsletter email field/label. The footer's mobile order was explicitly fixed so the brand block precedes the link groups, while the social row and compact bottom bar remain below them. The browser console reported no warnings or errors during the smoke check.

## Remaining product decisions

- There is no standalone public `/faqs` route in the registered route inventory; FAQs are currently exposed through their related course/product/topic/CMS surfaces. If a dedicated public FAQ index is required, that should be treated as a separate route/content decision.
- Live third-party delivery and OAuth/payment credentials still require environment-specific integration testing.
