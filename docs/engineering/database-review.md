# Database review

## Current design

The application uses MySQL/MariaDB in XAMPP and production, with SQLite for isolated feature tests. Laravel migrations are incremental and preserve existing records. Sessions, cache, and queue rows are database-backed by default.

## Integrity controls verified

- Unique keys cover user emails, slugs, order numbers, payment references, webhook event keys, newsletter identities, enrolments, waitlists, and content identities.
- Composite indexes cover common status/date, visibility/order, ownership/status, and public discovery filters.
- Commerce services use transactions and server-side price snapshots; tests cover duplicate webhooks, payment idempotency, refunds, access revocation, and customer object authorization.
- Signed routes and ownership checks protect customer downloads, order views, and portal records.
- Migrations are additive in the audited history. No destructive reset was run.

## Recommendations

1. Run `php artisan migrate --force` only after a database backup and migration review.
2. Use `EXPLAIN` on the busiest public/admin queries against a production-sized staging copy before adding indexes.
3. Keep database constraints as the final duplicate/race-condition guard even when application validation exists.
4. If multiple queue workers are introduced, retain the current idempotency keys and monitor failed jobs; do not replace them with distributed locks without evidence.
5. Add a scheduled backup and an isolated restore drill as host-owned operations.

No new schema migration was necessary for the improvements in this phase.
