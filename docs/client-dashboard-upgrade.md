# Client dashboard upgrade

## Delivered

- Added a returning-client Creative Studio work area. Only the signed-in user's Studio enquiries are shown, and terminal records remain available in the existing history page.
- Added a saved-articles page backed by the existing `article_reactions` table. Only published articles are displayed; drafts are filtered at query time.
- Added persistent database notifications for comment receipt, Studio enquiry receipt and confirmed payments. Notifications are ownership-scoped and can be read individually or in bulk.
- Added an account data export at `account.privacy.export`. It contains profile, order, enrollment, Studio enquiry, saved article and comment data, while excluding passwords, administrator MFA secrets and client recovery codes.
- Added database-backed session visibility and revocation. A client can revoke owned sessions or all other sessions after re-authentication; the current session is protected from self-revocation.
- Added client-only TOTP two-factor authentication with encrypted secrets, hashed one-time recovery codes, rate-limited setup/challenge actions and a session gate for client workspace routes. Administrator MFA remains on its existing fields and controller flow.

## Verification

- `php artisan test --no-ansi` — **148 passed, 1,349 assertions**.
- `npm run build` — **passed** with Vite production assets generated.
- `php artisan migrate --force` — applied the notifications and client MFA migrations successfully.
- `php artisan view:cache` — Blade templates compiled successfully.
- Route inventory confirms client enhancement routes are behind `auth`, `customer`, and (where appropriate) `client.mfa` middleware.

## Operational notes

Run the migrations before using the new account pages:

```text
php artisan migrate
```

Client MFA is optional. When enabled, a new client session is redirected to the authenticator challenge before opening the protected workspace. Recovery codes are shown once after setup and should be stored by the client.

The implementation uses no new package or external service. The existing `SESSION_DRIVER=database` configuration supports session management in local and deployed environments; if a deployment uses a different driver, the account will still work, but session listing will naturally be empty until database sessions are enabled.
