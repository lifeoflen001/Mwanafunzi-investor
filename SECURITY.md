# Mwanafunzi Investor security guide

## Reporting

Please report suspected vulnerabilities privately to the site operator before
publishing details. Include the affected URL or code path, reproduction steps,
impact and any evidence needed to reproduce the issue. Do not include live
passwords, API keys, payment credentials or customer data in a report.

## Application controls

- Administrator routes require authentication, the admin role and the configured
  administrator MFA policy. Admin MFA secrets are encrypted and recovery codes
  are stored as hashes.
- Customer access is server-side and ownership checks are applied to account,
  order, enrollment, download and payment operations.
- Passwords are handled through Laravel's hashed cast. Sessions rotate after
  authentication and production session cookies must be HTTPS-only.
- All state-changing forms use Laravel CSRF protection. Public side-effect forms
  use route throttling and can use Google reCAPTCHA v3 when enabled.
- Rich text is sanitized before rendering. User-supplied plain text is escaped
  by Blade. Raw HTML is reserved for the sanitized rich-text boundary.
- Product assets are stored on the private local disk and served through signed,
  ownership-checked download responses. Uploaded media is type- and size-limited.
- Security headers are applied globally, including CSP, frame denial, MIME
  sniffing protection, referrer policy and a restrictive permissions policy.
- Payment webhooks require provider signature validation, server-side provider
  verification and idempotent completion.

## Production requirements

1. Set `APP_ENV=production` and `APP_DEBUG=false`.
2. Use a strong `APP_KEY`, HTTPS, secure session cookies and a least-privilege
   database account.
3. Keep `.env`, payment keys, mail credentials, reCAPTCHA secrets and encryption
   keys outside Git and outside public storage.
4. Set `ADMIN_MFA_REQUIRED=true`, configure an authenticator for every admin and
   test the recovery process before launch.
5. Configure `RECAPTCHA_ENABLED=true`, the public site key and the server secret
   for the real hostname. The secret may be supplied through the environment or
   through the encrypted admin security setting; never paste it into a Blade
   template or JavaScript bundle.
6. Run `composer audit --no-interaction`, `npm audit --omit=dev` and the full
   test/build checks before release.
7. Restrict web-server access to the public document root, deny access to
   `.env`, `storage`, `bootstrap/cache`, repository metadata and uploaded private
   files, and configure backups with restricted permissions.

## Local development

reCAPTCHA is intentionally disabled in `local` and `testing` environments.
Tests should cover the disabled path and use an HTTP fake for enabled production
verification rather than contacting Google.
