# Mwanafunzi Investor

Laravel application for Mwanafunzi Investor's financial education, digital products, and digital software services. It combines a public website, a customer workspace, and an administration CMS.

## Features in the codebase

- Financial academy pages, courses, tools, and customer course workflows.
- Digital software project catalogue and studio enquiries.
- Blog/journal publishing, categories, tags, comments, and reactions.
- Commerce, customer accounts, and controlled downloads.
- Newsletter subscription, signed confirmation and unsubscribe links.
- CMS controls for pages, navigation, media, services, team members, testimonials, redirects, and SEO.
- Google sign-in, customer security settings, and administrator MFA.
- XML/HTML sitemaps and public metadata.

External services require valid configuration; a route or controller does not establish that its production integration is operational.

## Technology

PHP 8.2+, Laravel 12, Blade, Tailwind CSS, Vite, Laravel Socialite, and PHPUnit. The environment example targets MySQL/MariaDB for production, with SQLite suitable for isolated tests.

## Local setup

Requirements: PHP 8.2+ with the extensions required by Composer, Composer 2, a compatible Node.js release (Node 22.12+ is a suitable baseline for these Vite dependencies), npm, and a database.

```bash
git clone https://github.com/lifeoflen001/Mwanafunzi-investor.git
cd Mwanafunzi-investor
cp .env.example .env
composer install
php artisan key:generate
npm ci
```

Before migrating, configure `APP_URL` and the database in your local `.env`. For SQLite, create `database/database.sqlite` if it does not exist; for MySQL/PostgreSQL, create a dedicated local database and set its connection values. Use a separate test database.

```bash
php artisan migrate
php artisan storage:link
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000). The `cp` command above uses a Unix-style shell; in PowerShell use `Copy-Item .env.example .env`.

Review seeders before running them. Run `php artisan db:seed` only when you explicitly want their data in a disposable development database; do not assume they are safe for a populated installation.

## Configuration

Use [`.env.example`](.env.example) as the configuration reference.

| Area | Settings |
| --- | --- |
| Application | `APP_URL`, `APP_TIMEZONE`, `SEO_SITE_URL` |
| Portals | `PORTAL_PUBLIC_URL`, `PORTAL_CLIENT_URL`, `PORTAL_ADMIN_URL` |
| Storage and assets | `FILESYSTEM_DISK`, `ASSET_URL`, `CDN_URL` |
| Mail and jobs | `MAIL_*`, `QUEUE_CONNECTION` |
| Google sign-in | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` |
| Forms | `RECAPTCHA_*` |
| Commerce | `PAYMENT_PROVIDER`, `PAYMENT_ENVIRONMENT`, `FLW_*` |
| Administrator access | `ADMIN_MFA_REQUIRED` |

For local HTTP development, override `SESSION_SECURE_COOKIE=false`; the example deliberately enables secure cookies. `MAIL_MAILER=log` records messages locally and does not deliver email. Keep payments in sandbox while developing.

Production portal defaults point to `mwanafunziinvestor.com`, `client.mwanafunziinvestor.com`, and `admin.mwanafunziinvestor.com`. Configure DNS, TLS, web-server routing, and OAuth callback URLs for the deployment you actually use.

## Development and checks

```bash
composer dev
php artisan test
npm run build
php artisan route:list
```

`composer dev` runs the local server, queue listener, logs, and Vite together. The test command uses the project's PHPUnit configuration; ensure it cannot connect to production data.

## Repository map

| Path | Purpose |
| --- | --- |
| `app/Http/Controllers` | Public, commerce, customer, and admin request handlers |
| `resources/views` | Blade views and shared UI |
| `routes/web.php` | Web routes and access middleware |
| `routes/console.php` | Console/scheduled work definitions |
| `database` | Migrations, factories, and seeders |
| `tests` | Automated tests |
| `docs` | Architecture, audits, and deployment guidance |

## Deployment and security

Follow [HOSTING.md](HOSTING.md) and [SECURITY.md](SECURITY.md). Serve Laravel through `public/`, use `APP_DEBUG=false`, configure real mail delivery and queue processing, and provision the scheduler as required by the application. Keep secrets outside Git and review migrations before deployment.

Financial education content does not promise trading returns.
