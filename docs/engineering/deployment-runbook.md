# Deployment runbook

This runbook is for an authorized deployment to a compatible Laravel host. It does not deploy automatically.

## Pre-release

1. Review the commit and ensure `.env`, credentials, `vendor/`, `node_modules/`, and generated local data are not committed.
2. Run `composer validate --strict`, `composer audit --no-interaction`, `vendor/bin/pint --test`, `php artisan test`, `npm ci`, `npm audit --omit=dev --audit-level=high`, and `npm run build`.
3. Confirm a current database backup and a tested restore path.
4. Confirm production values: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `APP_KEY`, HTTPS, secure session cookies, database credentials, SMTP, reCAPTCHA, and payment provider settings.

## Release

```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

Point the web-server document root to `public/`. Keep application source, `.env`, `storage/`, and `vendor/` outside any separately exposed public directory.

If database-backed queues are enabled, run a supervised worker with a bounded timeout and restart policy. There is no scheduler requirement in the current application.

## Smoke checks

```bash
php artisan migrate:status
php artisan route:list
curl -fsS https://YOUR_DOMAIN/health/live
curl -fsS https://YOUR_DOMAIN/health/ready
```

Also verify login, admin MFA, a public content page, contact/newsletter validation, private download authorization, and the payment provider in its approved environment.

## Rollback

1. Stop or drain workers if the release changes queued payloads.
2. Restore the previous application artifact/build.
3. Do not automatically roll back migrations. Use a reviewed forward-compatible migration or restore the database only under an approved recovery procedure.
4. Re-run both health endpoints and the smoke checks.
5. Record the incident and preserve the request IDs from affected logs.
