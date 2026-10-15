# Disaster recovery

## Current status

The repository does not configure a backup provider, offsite retention, or a restore schedule. Those are hosting and business-ownership decisions, not safe defaults to invent in application code.

## Required production procedure

1. Schedule encrypted database backups at a frequency approved for the business data and record retention duration.
2. Store at least one protected copy outside the primary host/account.
3. Back up private commerce files and public media according to their business value; do not rely on a database dump for filesystem assets.
4. Restrict backup access and never store backup credentials in Git or application logs.
5. Test restoration in an isolated environment at an agreed cadence. Record restore duration, missing files, migration steps, and validation results.
6. After restoration, run migrations only after checking the restored schema, then verify `/health/live`, `/health/ready`, authentication, CMS access, and customer entitlements.

## Recovery objectives

RPO and RTO are intentionally `TBD` until the owner confirms acceptable data loss and downtime. Multi-region failover is deferred; the current single-region/single-host design has no evidence or budget requirement for it.
