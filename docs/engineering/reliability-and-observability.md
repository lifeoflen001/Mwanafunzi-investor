# Reliability and observability

## Implemented

- `GET /health/live` reports process/router liveness without requiring the database.
- `GET /health/ready` probes the database and returns a generic 503 when the dependency is unavailable.
- Existing `GET /health` remains compatible and continues to report the application/database status expected by current tests and monitors.
- `RequestContext` emits a bounded `X-Request-Id` response header and adds the identifier, method, and route name/template to Laravel log context without recording token-bearing URL values.
- Security headers continue to be applied globally.
- Database queue and failed-jobs tables are present for recoverable background work.

## Operational guidance

Monitor the readiness endpoint, HTTP 5xx rate, queue backlog/failed jobs, database connectivity, disk usage, and payment/webhook failures. Alerts should have a named owner and a documented response; do not alert on every transient request error.

Suggested initial service indicators (targets require business approval): request success rate, readiness success rate, checkout completion rate, queue failure rate, and route latency percentiles. No SLO or availability target is claimed until traffic and business criticality are measured.

## Not implemented intentionally

Distributed tracing, metrics storage, on-call tooling, and alert delivery require an operational platform and credentials that are not present in this local project. Adding a vendor integration without an agreed destination would create configuration debt.
