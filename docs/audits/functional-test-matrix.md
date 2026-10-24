# Functional test matrix

**Evidence date:** 24 October 2026
**Automated baseline:** 152 tests, 1,365 assertions, all passing.

## Route inventory

Laravel reports **327 non-vendor routes** with `php artisan route:list --except-vendor`. The canonical machine-readable inventory is always the command output because route names, middleware and verbs can change with code. `tests/Feature/RouteInventoryTest.php` verifies that controller actions registered in the inventory resolve to real methods.

### Public route groups

| Group | Representative named routes | Result |
|---|---|---|
| Platform and health | `home`, `health`, `health.live`, `health.ready`, `robots`, `sitemap`, `site-map` | PASS in `RouteInventoryTest`, `PlatformRoutesTest`, `SecurityHardeningTest`, `SeoTest` |
| Legal and contact | `contact`, `legal`, `cookie-policy`, `contact.submit` | PASS for rendering, validation persistence and policy routes; external delivery not verified |
| Academy | `learn`, `learn.show`, `courses`, `courses.show`, `tools`, `tools.show` | PASS for public rendering, course/lesson ownership and no-curriculum state; full browser enrollment walkthrough remains required |
| Digital Software | `digital-systems`, `digital-software.projects`, `digital-software.projects.show`, `digital-software.hospitality`, `digital-software.contact` | PASS for published/scoped project routes and hospitality records |
| Creative Studio | `studio`, `studio.services`, `studio.services.show`, `studio.projects`, `studio.projects.show`, `studio.inquiry` | PASS for scoped service/project rendering and enquiry persistence; file/email delivery remains external QA |
| Blog / journal | `blog`, `blog.show`, category/tag/author variants, reactions, comments, `feed` | PASS for published filtering, search/filter/view state, safe comments and moderation; interactive browser coverage remains required |
| Authentication and portal | `login`, `register`, verification, password reset, Google auth, `account.*` | PASS for local auth, safe return paths, ownership, client MFA, sessions, notifications and export in feature tests; real OAuth/mail delivery not verified |

### Admin route groups

The route inventory includes admin authentication, dashboard, CMS pages/sections, articles/journal, FAQs, media, courses/modules/lessons, products/assets/orders/payments/entitlements, customers, newsletters, contact and Studio enquiries, services/projects/testimonials, navigation, settings, SEO, security, administrators, audit and notifications. Static authenticated admin GET destinations are covered by `PlatformRoutesTest::test_all_static_admin_get_routes_render_for_an_authorised_admin`; mutation coverage is listed below.

| Workflow | Coverage | Result |
|---|---|---|
| Authentication, MFA and admin boundary | `SecurityHardeningTest`, `GoogleAuthenticationTest`, `PlatformRoutesTest` | PASS for tested role boundaries, MFA challenge/recovery, safe redirects and suspended admin behavior |
| Articles, categories, tags, comments | `PlatformRoutesTest`, `CurrentAuditFixesTest` | PASS for create/edit/tag/media paths, published/draft visibility and optional social rows; scheduling and browser editor behavior remain required |
| FAQs, media and uploads | `PlatformRoutesTest` | PASS for media metadata, referenced-file protection, record-only/full deletion options, status visibility and media-backed article fields |
| Academy content and enrolment | `PlatformRoutesTest`, `StudentPortalTest`, `CommerceTest`, `CurrentAuditFixesTest` | PASS for module/lesson ordering, progress ownership and no-curriculum messaging; reviewed curriculum content is a product decision |
| Commerce | `CommerceTest` | PASS for sandbox price/payment/webhook/refund/entitlement invariants; live provider, queue and reconciliation remain unverified |
| Customers and client privacy | `PlatformRoutesTest`, `ClientAccountEnhancementsTest`, `StudentPortalTest` | PASS for status changes, profile ownership, sessions, export, notifications and client MFA |
| Contact, newsletter and Studio enquiry | `PlatformRoutesTest`, `NewsletterCookieConsentTest`, `StudioInquiryTest`, `CurrentAuditFixesTest` | PASS for persistence, pending confirmation, duplicate/unsubscribe, enquiry scoping and dashboard unread filtering; SMTP and browser submission remain required |
| Website CMS and testimonials | `PlatformRoutesTest`, `PortfolioTest`, `ComplianceAuditTest` | PASS for CRUD/public visibility, consent gate and demo suppression |
| SEO and public publication | `SeoTest`, `PlatformRoutesTest` | PASS for metadata, sitemap exclusion and CMS propagation; production crawler and canonical-host verification remain required |
| Settings, cookies and security | `NewsletterCookieConsentTest`, `SecurityHardeningTest`, `ComplianceAuditTest` | PASS for tested controls; production secret/configuration review remains required |

## Phase 2 local browser evidence — 24 October 2026

| Journey | Local browser result | Evidence/limitation |
|---|---|---|
| Public home, Academy, Digital Software, Studio, Blog, About, Contact and legal pages | PASS | Expected page headings rendered for the listed routes; no HTTP error page observed. |
| Admin dashboard and major CMS destinations | PASS | Dashboard, messages, Studio enquiries, FAQs, media, courses, products, articles, pages, customers and newsletter rendered for an authenticated admin. |
| FAQ CRUD | PASS | Empty create validation fired; a `[QA]` FAQ was created and edited with success feedback. It was intentionally not deleted during browser evidence collection. |
| Contact form | PARTIAL | Browser required-field validation fired. Controlled `.test` submission persisted in the local DB as `new` with consent; post-submit browser state timed out, so visible success feedback is not claimed. |
| Blog comments | PARTIAL | Empty body validation fired; no real/test comment was submitted. Moderation and persistence remain covered by feature tests. |
| Client authentication/portal | PASS/PARTIAL | Local demo login reached the client MFA verification gate and `/account` shell; MFA code entry, password reset mail and all portal mutations were not browser-completed. |
| Queue/failure recovery | PASS locally | Three queued jobs were processed; two newsletter jobs initially failed due to missing legacy unsubscribe tokens, then both were retried successfully after the fix. `queue:failed` is empty and pending size is 0. |

## Required manual/staging coverage still open

The following are not claimed as passed because the available execution did not include a browser session against the running local server or third-party sandboxes:

- desktop/mobile keyboard and focus walkthroughs for every admin drawer, modal, table action and file picker;
- SMTP delivery, queue worker execution, newsletter confirmation mail and contact notification mail;
- real Google OAuth callback, payment provider sandbox callback and webhook reachability;
- schedule/cron execution (the current application reports no scheduled tasks);
- production HTTPS, trusted proxy, backups and restore rehearsal;
- role-by-role direct HTTP authorization matrix across all 327 routes;
- third-party widget/cookie consent behavior after deployment.
- full browser mutation coverage for course enrollment, products/payment sandbox, media upload/delete, Studio enquiry uploads, testimonial approval/reordering, newsletter confirmation/unsubscribe, and all admin role variants;
- responsive screenshots and keyboard/zoom/screen-reader evidence at the release breakpoints.

## Reproduction commands

```powershell
php artisan route:list --except-vendor
php artisan test --no-ansi
php artisan about
php artisan migrate:status
php artisan schedule:list
php artisan app:demo-data report --json
php artisan app:demo-data cleanup --json
```
