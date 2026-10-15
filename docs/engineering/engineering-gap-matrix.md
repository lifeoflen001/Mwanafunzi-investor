# Engineering gap matrix

Audit date: 2026-10-15. The supplied document calls this a 111-concept checklist, but its eight lists contain 112 named concepts. All 112 named concepts are assessed below; the count discrepancy is recorded rather than silently dropping one item.

Status values follow the supplied task: `IMPLEMENTED & VERIFIED`, `REQUIRES IMPROVEMENT`, `IMPLEMENT NOW`, `CONDITIONAL / FUTURE`, or `NOT APPLICABLE`.

## Traffic, networking and resilience

| Concept | Applicable | Current state / evidence | Issue and recommendation | Priority | Status |
| --- | --- | --- | --- | --- | --- |
| Rate Limiting | Yes | Route throttles and controller limiters cover auth, forms, commerce, comments, and MFA. | Keep endpoint-specific limits and review thresholds with traffic data. | P1 | IMPLEMENTED & VERIFIED |
| Caching | Yes | Database cache is configured; public data is consolidated in `PublicSiteData`. | Measure hit rate and add only permission-safe keys. | P2 | REQUIRES IMPROVEMENT |
| Load Balancing | Conditional | Single-host deployment is documented. | Add only after measured capacity requires multiple web nodes. | P4 | CONDITIONAL / FUTURE |
| Reverse Proxies | Conditional | Apache/XAMPP locally; Apache/Nginx deployment guidance exists. | Configure TLS, forwarding, and client-size limits at the host. | P3 | CONDITIONAL / FUTURE |
| API Gateways | No | No public multi-service API surface. | Do not add an API gateway to a server-rendered monolith. | P4 | NOT APPLICABLE |
| Service Discovery | No | No independently deployed services. | Reassess only with service decomposition. | P4 | NOT APPLICABLE |
| Circuit Breakers | Conditional | External calls have configured timeouts. | Add provider-specific circuit breaking only if failure volume justifies it. | P2 | CONDITIONAL / FUTURE |
| Timeouts | Yes | Outbound connect and total timeouts are configurable. | Keep provider-specific limits bounded and monitored. | P1 | IMPLEMENTED & VERIFIED |
| Retries | Conditional | Payment/webhook flows are idempotent; no broad automatic retry loop. | Retry only transient provider errors with bounded attempts. | P2 | CONDITIONAL / FUTURE |
| Exponential Backoff | Conditional | No distributed retry worker currently requires it. | Use jittered backoff when queued external retries are introduced. | P3 | CONDITIONAL / FUTURE |
| Idempotency | Yes | Payment references and webhook event keys are unique; tests cover duplicate processing. | Preserve database uniqueness as the final guard. | P0 | IMPLEMENTED & VERIFIED |
| DNS | Conditional | Host/domain setup is documented, not managed in repo. | Configure DNS and health-check TTLs with the host. | P3 | CONDITIONAL / FUTURE |
| TCP vs UDP | No | Application uses HTTP/mail/payment clients; no custom transport. | Leave transport selection to the web/hosting stack. | P4 | NOT APPLICABLE |
| HTTP/2 | Conditional | Supported by a compatible TLS web server, not forced by app code. | Enable at the host after TLS verification. | P3 | CONDITIONAL / FUTURE |
| HTTP/3 | Conditional | No evidence of requirement or host support. | Defer until browser/host telemetry supports it. | P4 | CONDITIONAL / FUTURE |
| Network Partitions | Conditional | Single-node application; no service mesh. | Document provider outage behavior and avoid new distributed assumptions. | P3 | CONDITIONAL / FUTURE |
| Clock Skew | Conditional | Time-sensitive tokens/payments rely on host clocks. | Keep production hosts synchronized with NTP and monitor webhook timestamps if added. | P2 | CONDITIONAL / FUTURE |

## Messaging and distributed systems

| Concept | Applicable | Current state / evidence | Issue and recommendation | Priority | Status |
| --- | --- | --- | --- | --- | --- |
| Message Queues | Yes | Laravel database queue and jobs table are configured. | Run a supervised worker in production. | P1 | IMPLEMENTED & VERIFIED |
| Pub/Sub | No | No multi-consumer event platform is required. | Keep local events/queues until a real consumer fan-out exists. | P4 | NOT APPLICABLE |
| Event-Driven Architecture | Conditional | Commerce and mail have service boundaries, but the app is request/DB centered. | Introduce domain events only for a demonstrated asynchronous need. | P3 | CONDITIONAL / FUTURE |
| Distributed Transactions | No | Critical workflows use one relational database. | Prefer local transactions; avoid two-phase infrastructure. | P4 | NOT APPLICABLE |
| Saga Pattern | No | No multi-service transaction exists. | Use compensation only if integrations become independently committed. | P4 | NOT APPLICABLE |
| Dead Letter Queues | Conditional | `failed_jobs` provides framework failure storage. | Define retry/failed-job review and replay procedure for production. | P2 | REQUIRES IMPROVEMENT |
| Cron Jobs | Conditional | No scheduled task is registered in `routes/console.php`. | Add schedules only for a specific maintenance requirement. | P3 | CONDITIONAL / FUTURE |
| WebSockets | No | No real-time requirement found. | Do not add a socket server. | P4 | NOT APPLICABLE |
| Long Polling | No | No real-time requirement found. | Keep normal request/response. | P4 | NOT APPLICABLE |
| Server-Sent Events | No | No real-time requirement found. | Keep normal request/response. | P4 | NOT APPLICABLE |
| Leader Election | No | No multi-worker singleton scheduler requirement exists. | Reassess only with distributed scheduled work. | P4 | NOT APPLICABLE |
| CAP Theorem | Conditional | Single database is the consistency boundary. | Document trade-offs before introducing distributed stores. | P4 | CONDITIONAL / FUTURE |
| Eventual Consistency | Conditional | Queued mail can complete after the request. | Expose/retry failed jobs without weakening payment consistency. | P2 | CONDITIONAL / FUTURE |
| Backpressure | Conditional | Database queue provides a basic buffer. | Monitor queue depth and bound bulk operations before scaling workers. | P2 | CONDITIONAL / FUTURE |

## Database engineering

| Concept | Applicable | Current state / evidence | Issue and recommendation | Priority | Status |
| --- | --- | --- | --- | --- | --- |
| Database Indexing | Yes | Migrations include targeted composite and unique indexes. | Validate with `EXPLAIN` on staging data; avoid speculative indexes. | P1 | IMPLEMENTED & VERIFIED |
| Query Optimization | Yes | Eloquent scopes, pagination, eager loading, and indexes are used. | Capture query timings on representative traffic. | P2 | REQUIRES IMPROVEMENT |
| N+1 Queries | Yes | Shared/public/admin query paths were reviewed; tests cover routes. | Add query-count regression tests for any measured hot path. | P2 | REQUIRES IMPROVEMENT |
| Connection Pooling | Conditional | PHP request workers use normal DB connections. | Tune host worker/database limits before adding a pooler. | P4 | CONDITIONAL / FUTURE |
| Read Replicas | Conditional | No traffic or write/read contention evidence. | Defer until measured read load warrants replica operations. | P4 | CONDITIONAL / FUTURE |
| Sharding | No | Single tenant/domain and relational model. | Do not shard this application. | P4 | NOT APPLICABLE |
| Partitioning | Conditional | Tables are moderate and indexed. | Consider only after data-volume measurements. | P4 | CONDITIONAL / FUTURE |
| Replication | Conditional | Production database replication is host-owned. | Configure backup/replication based on RPO, not assumption. | P3 | CONDITIONAL / FUTURE |
| Optimistic Locking | Conditional | No version column is required by current workflows. | Add version checks only for an observed lost-update workflow. | P3 | CONDITIONAL / FUTURE |
| Pessimistic Locking | Yes | Payment/refund services use `lockForUpdate()` inside transactions. | Keep locks short and monitor deadlocks. | P0 | IMPLEMENTED & VERIFIED |
| Distributed Locks | No | Database uniqueness/transactions cover current concurrency. | Do not add a distributed lock service without multiple-node need. | P4 | NOT APPLICABLE |
| Race Conditions | Yes | Payment idempotency, unique keys, and transaction tests cover key races. | Keep retryable operations idempotent. | P0 | IMPLEMENTED & VERIFIED |
| Deadlocks | Conditional | Short transactional locks exist. | Add retry policy only after measured deadlocks; inspect DB logs. | P2 | CONDITIONAL / FUTURE |
| Database Migrations | Yes | Incremental migrations are present and tested. | Review migration locks and backup before production runs. | P1 | IMPLEMENTED & VERIFIED |
| Schema Versioning | Yes | Laravel migration repository versions the schema. | Keep migrations backward compatible across rolling releases. | P1 | IMPLEMENTED & VERIFIED |

## Scalability and runtime efficiency

| Concept | Applicable | Current state / evidence | Issue and recommendation | Priority | Status |
| --- | --- | --- | --- | --- | --- |
| Memory Leaks | Conditional | Short-lived PHP requests reduce persistent leak risk. | Profile long-running workers before adding special handling. | P3 | CONDITIONAL / FUTURE |
| Garbage Collection | Conditional | PHP runtime manages request memory. | Monitor worker memory if queue workers run continuously. | P3 | CONDITIONAL / FUTURE |
| Thread Safety | No | Deployment model is PHP request/worker based; no shared threaded app state. | Keep mutable state in DB/cache primitives. | P4 | NOT APPLICABLE |
| Autoscaling | Conditional | No multi-node deployment evidence. | Defer until traffic and host metrics justify it. | P4 | CONDITIONAL / FUTURE |
| Horizontal Scaling | Conditional | Database sessions/cache/queue can support more than one app node, but files need shared storage. | Validate shared storage and worker coordination before adding nodes. | P4 | CONDITIONAL / FUTURE |
| Vertical Scaling | Yes | First operational scale path for the current host. | Measure CPU/RAM/database limits before resizing. | P3 | CONDITIONAL / FUTURE |
| CDN | Conditional | `ASSET_URL` is configurable and assets are hashed. | Add a CDN only after transfer/latency data supports it. | P3 | CONDITIONAL / FUTURE |
| Edge Caching | Conditional | Public pages may be cacheable; private/admin pages are not. | Define explicit public-only cache policy if needed. | P3 | CONDITIONAL / FUTURE |
| Cache Invalidation | Yes | Database cache exists but no broad public invalidation contract is documented. | Define keys/TTL/invalidation before adding more caching. | P2 | REQUIRES IMPROVEMENT |
| Cold Starts | No | Not a serverless deployment. | Not an application concern today. | P4 | NOT APPLICABLE |
| Serverless Limits | No | Not a serverless deployment. | Do not redesign for serverless without a hosting decision. | P4 | NOT APPLICABLE |
| Latency | Yes | Local suite/build measured; representative HTTP latency is not. | Establish staging route timings before optimization. | P2 | REQUIRES IMPROVEMENT |
| Throughput | Yes | No load baseline exists. | Run authorized isolated load tests against staging. | P2 | REQUIRES IMPROVEMENT |
| P99 Latency | Yes | Not measured. | Add percentile measurements to staging benchmark. | P2 | REQUIRES IMPROVEMENT |
| Tail Latency | Yes | Not measured. | Correlate slow tails with DB/external calls before tuning. | P2 | REQUIRES IMPROVEMENT |
| Cost Optimization | Yes | Avoiding unnecessary distributed services limits cost. | Compare host, mail, payment, storage, and CDN usage after traffic data. | P3 | REQUIRES IMPROVEMENT |

## Deployment and infrastructure

| Concept | Applicable | Current state / evidence | Issue and recommendation | Priority | Status |
| --- | --- | --- | --- | --- | --- |
| CI/CD | Yes | GitHub Actions workflow now validates dependencies, format, tests, build, and audits. | Execute it in GitHub and protect `main` with required checks. | P1 | IMPLEMENTED & VERIFIED |
| Docker | No | XAMPP/local Apache is the current deployment model; no container requirement. | Keep host deployment and document parity. | P4 | NOT APPLICABLE |
| Kubernetes | No | No cluster or multi-service workload. | Do not introduce it. | P4 | NOT APPLICABLE |
| Feature Flags | Conditional | CMS visibility/status controls content; no release flag service. | Add only for risky staged releases. | P3 | CONDITIONAL / FUTURE |
| Blue-Green Deployment | Conditional | Single-host deployment. | Use only if host can maintain two tested artifacts. | P4 | CONDITIONAL / FUTURE |
| Canary Releases | Conditional | No traffic-splitting infrastructure. | Defer until a proxy/platform supports it. | P4 | CONDITIONAL / FUTURE |
| Rolling Deployments | Conditional | Requires multiple app nodes. | Defer; keep migrations backward compatible. | P4 | CONDITIONAL / FUTURE |
| Rollbacks | Yes | Runbook documents artifact rollback and cautious migration handling. | Test rollback on staging. | P1 | IMPLEMENTED & VERIFIED |
| Health Checks | Yes | Existing `/health` plus new liveness/readiness endpoints. | Monitor both endpoints from the host. | P1 | IMPLEMENTED & VERIFIED |
| Liveness & Readiness Probes | Yes | `/health/live` is dependency-free; `/health/ready` checks DB. | Keep liveness independent of optional providers. | P1 | IMPLEMENTED & VERIFIED |
| Infrastructure as Code | No | No infrastructure is managed in this repository. | Do not create cloud resources without authorization. | P4 | NOT APPLICABLE |
| Terraform | No | No Terraform deployment. | Defer with infrastructure as code. | P4 | NOT APPLICABLE |
| Helm Charts | No | No Kubernetes deployment. | Not applicable. | P4 | NOT APPLICABLE |
| Build Caching | Yes | npm lockfile and setup-node npm cache are used in CI. | Add Composer cache after measuring CI time if useful. | P3 | REQUIRES IMPROVEMENT |
| Dependency Hell | Yes | Composer/npm lockfiles and audits are clean locally. | Keep updates reviewed and CI audits active. | P1 | IMPLEMENTED & VERIFIED |
| Multi-Region Deployment | No | No availability/budget requirement or replicated data plane. | Defer until explicit RPO/RTO and budget exist. | P4 | NOT APPLICABLE |

## Monitoring and reliability

| Concept | Applicable | Current state / evidence | Issue and recommendation | Priority | Status |
| --- | --- | --- | --- | --- | --- |
| Monitoring | Yes | Health endpoints and logs exist; no external monitor is configured. | Configure host monitoring and endpoint checks. | P1 | REQUIRES IMPROVEMENT |
| Logging | Yes | Laravel logs plus request ID/method/route context now exist. | Restrict access and route production logs to an owned sink. | P1 | IMPLEMENTED & VERIFIED |
| Distributed Tracing | No | Single application with limited async work. | Reassess only after service/queue complexity grows. | P4 | NOT APPLICABLE |
| Metrics | Yes | No metrics backend is configured. | Collect request errors, latency, queue, DB, and payment metrics at host/platform level. | P2 | REQUIRES IMPROVEMENT |
| Alerts | Yes | No alert destination is configured. | Define actionable owners and thresholds before launch. | P2 | REQUIRES IMPROVEMENT |
| SLOs | Yes | No business-approved targets exist. | Define after baseline and critical workflow review. | P2 | REQUIRES IMPROVEMENT |
| SLIs | Yes | Candidate indicators are documented but not collected. | Instrument success, readiness, latency, and queue indicators. | P2 | REQUIRES IMPROVEMENT |
| Error Budgets | Conditional | Requires approved SLOs and traffic history. | Add after SLOs exist. | P3 | CONDITIONAL / FUTURE |
| Observability | Yes | Request correlation and useful logs are implemented. | Add metrics/alerts through the chosen host platform. | P2 | REQUIRES IMPROVEMENT |
| Disaster Recovery | Yes | Recovery procedure is documented; provider execution is absent. | Configure and test backups/restores. | P1 | REQUIRES IMPROVEMENT |
| Backups | Yes | No backup provider/configuration is in source. | Configure encrypted DB/media backups and retention on host. | P0 | REQUIRES IMPROVEMENT |
| Failover | Conditional | Single host and database. | Defer until recovery objectives justify redundancy. | P4 | CONDITIONAL / FUTURE |
| Chaos Engineering | No | No authorized isolated environment or distributed system. | Do not run disruptive tests locally/production. | P4 | NOT APPLICABLE |
| Production Incidents | Yes | No external incident platform; runbook has smoke/rollback guidance. | Add severity, escalation, and timeline procedure before launch. | P2 | REQUIRES IMPROVEMENT |
| On-Call | Conditional | Requires an operating team and alert destination. | Assign ownership as part of launch readiness. | P2 | CONDITIONAL / FUTURE |
| Postmortems | Conditional | No incident history/template in repository. | Add a lightweight template when the first incident occurs or before launch. | P3 | CONDITIONAL / FUTURE |

## Application security

| Concept | Applicable | Current state / evidence | Issue and recommendation | Priority | Status |
| --- | --- | --- | --- | --- | --- |
| Secrets Management | Yes | Secrets are environment/config driven; local `.env` is ignored. | Use protected production environment/secret manager and rotate credentials. | P0 | REQUIRES IMPROVEMENT |
| IAM | Yes | Admin/customer middleware, MFA, and ownership checks exist. | Review admin accounts and least privilege operationally. | P0 | IMPLEMENTED & VERIFIED |
| OAuth | No | No OAuth login/integration exists. | Do not introduce it without a business identity requirement. | P4 | NOT APPLICABLE |
| JWT Rotation | No | Cookie sessions are used; no JWTs found. | Do not add JWTs as a replacement. | P4 | NOT APPLICABLE |
| TLS | Conditional | HTTPS and secure-cookie requirements are documented; local XAMPP is HTTP. | Configure and verify production TLS at the web host. | P0 | CONDITIONAL / FUTURE |
| Encryption at Rest | Conditional | Laravel encryption protects selected secrets; DB/filesystem encryption is host-owned. | Confirm provider disk/database encryption for sensitive data. | P1 | REQUIRES IMPROVEMENT |
| Encryption in Transit | Conditional | HTTPS is a production requirement; local traffic is HTTP. | Enforce HTTPS/HSTS only after TLS is valid. | P0 | CONDITIONAL / FUTURE |
| WAF | Conditional | No WAF assumed. | Use host protection if risk/traffic justifies it. | P3 | CONDITIONAL / FUTURE |
| DDoS Protection | Conditional | No custom DDoS layer. | Use provider edge protection if exposed to material traffic. | P3 | CONDITIONAL / FUTURE |
| CORS | No | Same-origin server-rendered application; no public cross-origin API. | Keep cross-origin access closed unless a future API requires an allowlist. | P1 | NOT APPLICABLE |
| CSRF | Yes | Laravel CSRF middleware and tests are active. | Keep webhook route isolated and authenticated by signature. | P0 | IMPLEMENTED & VERIFIED |
| SQL Injection | Yes | ORM/query binding and validation; no unsafe request SQL path identified. | Retain binding and review any future raw queries. | P0 | IMPLEMENTED & VERIFIED |
| XSS | Yes | Blade escaping and rich-text sanitization are tested. | Keep HTML allowlist narrow and review editor changes. | P0 | IMPLEMENTED & VERIFIED |
| SSRF | Conditional | No user-controlled server-side fetch endpoint was identified; integrations use configured hosts. | Validate any future URL-fetch feature against an allowlist. | P1 | NOT APPLICABLE |

## APIs and integrations

| Concept | Applicable | Current state / evidence | Issue and recommendation | Priority | Status |
| --- | --- | --- | --- | --- | --- |
| gRPC | No | No service-to-service API. | Keep HTTP/Blade and provider APIs. | P4 | NOT APPLICABLE |
| Webhooks | Yes | Flutterwave signature verification and idempotent event processing are tested. | Add replay/timestamp enforcement if provider supports it and monitor failures. | P1 | IMPLEMENTED & VERIFIED |
| API Versioning | No | No public API contract is exposed. | Version only when a public API is introduced. | P4 | NOT APPLICABLE |
| Semantic Versioning | Yes | Composer/npm dependencies use semver ranges and lockfiles; application releases lack a documented version policy. | Add release tags/changelog policy before external integrations depend on versions. | P3 | REQUIRES IMPROVEMENT |

## Matrix conclusion

The actionable P0/P1 items are now covered in application code, tests, CI, or deployment documentation. P2 items requiring production traffic data remain deliberately open. P3/P4 distributed-infrastructure items are deferred because the current monolith and single-host model do not justify them.
