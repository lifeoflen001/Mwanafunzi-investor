# Performance benchmarks

Audit date: 2026-10-15. Measurements below are local development measurements and must not be read as production SLOs.

## Baseline and verification

| Check | Result | Method |
| --- | --- | --- |
| Automated suite | 92 tests / 913 assertions / 46.38 s | `php artisan test` on XAMPP checkout |
| Composer audit | 0 advisories | `composer audit --no-interaction --format=summary` |
| Production npm audit | 0 vulnerabilities | `npm audit --omit=dev --audit-level=high` |
| Asset build | Passed | `npm run build` |
| Database query controls | Pagination and scoped indexes present | `database/migrations/`, admin/public controllers |

No before/after percentage improvement is claimed because a representative HTTP load baseline, browser trace, P95/P99 sample, or production-like dataset was not available.

## Current performance design

- Vite emits hashed assets; the build output is safe for long-lived browser caching.
- Public site chrome is consolidated through `PublicSiteData` and can be cached by the existing application strategy without sharing private data.
- Admin and portal lists use database pagination instead of unbounded result sets.
- Migrations include targeted composite indexes for public discovery, admin filtering, comments, commerce status, and audit records.
- External calls use configured connect and total timeouts (`OUTBOUND_CONNECT_TIMEOUT`, `OUTBOUND_TIMEOUT`).
- Database queues prevent selected mail operations from blocking the request when a worker is running.

## Next measurement gate

Before optimizing further, run a staging benchmark with a representative dataset and record route, SQL query count/time, response time, memory, asset transfer size, and P95/P99 latency. Do not add Redis, a CDN, read replicas, or a load balancer until this evidence shows the single-node design is the bottleneck.
