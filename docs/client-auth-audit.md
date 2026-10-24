# Client authentication and workspace audit

## Scope verified

The current implementation uses Laravel session authentication with separate admin and client authorization paths. Client routes are protected by the existing auth, verified, and account ownership checks. Google sign-in uses Socialite with PKCE and stores provider identities separately from passwords.

Verified source areas:

- app/Http/Controllers/CustomerAuthController.php
- app/Http/Controllers/GoogleAuthController.php
- app/Http/Controllers/CustomerAccountController.php
- app/Services/CustomerDashboardService.php
- app/Services/AuthenticationRedirectService.php
- resources/views/components/public-account-menu.blade.php
- resources/views/account/dashboard.blade.php
- resources/views/public/journal/show.blade.php
- bootstrap/app.php
- routes/web.php

## Findings and fixes

| Area | Finding | Resolution |
| --- | --- | --- |
| Normal login and registration | Successful client authentication defaulted to the public home page. | Centralized continuation handling now defaults to the client dashboard. |
| Guest actions | Authenticated-only comments, reactions, and checkout lost the originating context. | Guest middleware captures a safe same-origin destination and sends it through the login flow. |
| Comment drafts | A guest comment body was lost when authentication was required. | Non-empty comment drafts are stored for 30 minutes, restored on the article page, and cleared after successful submission. |
| Redirect safety | Absolute URLs from internal Laravel routes were not compatible with the relative-only safety helper. | Same-origin absolute routes are converted to internal paths; foreign hosts and auth/admin destinations are rejected. |
| Google OAuth failures | Cancellation and provider errors consumed the continuation before the user could retry. | Failure paths now restore the safe continuation in session. |
| Dashboard summary | Returning clients could not see an order or active-service count in the primary summary. | Dashboard metrics now use real order and active studio enquiry relationships. |
| Account menu | The compact menu lacked real avatar fallback behavior and stronger keyboard focus handling. | Avatar image/initials, first-name label, menu semantics, Escape handling, focus return, and POST logout are present. |
| Greeting | Honorifics could render as the greeting name. | User::firstName() skips common honorifics before selecting the first name. |

## Security invariants retained

- Admin login and admin middleware were not changed.
- Logout remains a POST action with CSRF protection and session invalidation.
- Redirects are limited to same-origin internal paths and cannot target admin or authentication endpoints.
- Account, order, enrollment, download, and profile access remains ownership-checked by the existing controllers/policies.
- Comments remain pending moderation after successful submission.
- Google-only users remain subject to the existing password/disconnect rules.

## Automated evidence

Focused feature coverage:

    php artisan test --filter="ClientAuthenticationExperienceTest|GoogleAuthenticationTest|StudentPortalTest" --no-ansi
    18 passed (129 assertions)

The new ClientAuthenticationExperienceTest covers:

- default login and registration destinations;
- verification notification dispatch;
- guest comment intent and draft restoration;
- moderated comment persistence and draft cleanup;
- guest reaction and checkout continuation;
- external redirect rejection;
- account menu links and POST logout markup.

The existing Google and Student Portal suites continue to pass.
