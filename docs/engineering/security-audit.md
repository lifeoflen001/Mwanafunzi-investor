# Security audit

Audit date: 2026-10-15. This document records source-backed controls and remaining deployment responsibilities. It does not replace a production penetration test.

## Verified controls

| Area | Evidence | Verification |
| --- | --- | --- |
| Passwords and sessions | Laravel authentication, hashed passwords, session regeneration, database sessions | `tests/Feature/SecurityHardeningTest.php`, auth controllers |
| Admin privilege boundary | `EnsureAdmin`, `EnsureAdminMfa`, separate admin routes and password reset flow | `routes/web.php`, security feature tests |
| MFA | TOTP challenge, recovery-code one-time use, rate limiting, reauthentication to disable | `AdminMfaController`, `SecurityHardeningTest` |
| CSRF | Laravel CSRF middleware, webhook explicitly isolated from browser CSRF | `bootstrap/app.php` |
| Input validation | Form requests/controller validation, prohibited honeypot fields, recaptcha rules | public/admin controllers and `app/Rules/` |
| SQL injection | Eloquent/query builder bindings; no user-controlled raw SQL paths found in the audited request flows | `app/`, `routes/` |
| XSS | Blade escaping and sanitized rich text boundary | views and `RichTextTest` |
| File access | Private commerce disk and signed download route; private files are not served by the framework | `DownloadController`, `DownloadService`, tests |
| Webhooks | Flutterwave signature verification and event-key idempotency | `CommerceController`, `PaymentService`, commerce tests |
| Headers | CSP, frame denial, MIME sniffing protection, referrer and permissions policies | `SecurityHeaders`, response test |
| Abuse controls | Endpoint-specific throttles for auth, contact, newsletter, comments, checkout, waitlist, and MFA | `routes/web.php`, controllers |
| Dependencies | Locked Composer/npm dependencies | `composer.lock`, `package-lock.json`, local audits |

## Implemented in this phase

1. Every request now receives a safe correlation identifier. A client-supplied `X-Request-Id` is accepted only when it matches a bounded safe-character format; otherwise an ULID is generated. The identifier is attached to the request, response, and log context by `app/Http/Middleware/RequestContext.php`; logs use the route name/template rather than raw token-bearing URLs.
2. Liveness and readiness are separate. `/health/live` does not depend on the database; `/health/ready` performs a database probe and returns 503 without exposing exception details.
3. CI now runs formatting, tests, frontend build, and dependency audits on pushes and pull requests.

## Residual risks and required production configuration

- Use a real secret manager or protected host environment for `APP_KEY`, database, SMTP, reCAPTCHA, and payment secrets. `.env` must not be committed.
- Set `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, HTTPS, and a least-privileged database user.
- Configure reCAPTCHA and admin MFA policy for the production domain.
- Add WAF/DDoS protection only through the selected host if the risk profile requires it; none is assumed locally.
- A production security review should include upload limits, web-server document root, TLS configuration, dependency update policy, and log access permissions.
