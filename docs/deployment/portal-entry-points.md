# Portal entry-point routing

Public login links open the client or administrator login in a new tab using `target="_blank" rel="noopener"`. HTTP redirects cannot force a new tab when an address is typed or opened externally.

In production, GET/HEAD `/login`, `/register` and `/forgot-password` on the main or www host redirect to the client subdomain. The client root redirects to its `/login`; public content requested on the client host returns to the main host. Client authentication, OAuth, account, checkout, payment and download paths remain on the client host. `/admin/login` redirects to the admin subdomain's `/login`; other `/admin/...` links retain their path on the admin host. The admin root redirects to `/login`, and public content requested on the admin host returns to the main host. The admin host has explicit GET/POST `/login` routes registered before the generic client login. Existing `/admin/login` POST routes continue to handle the administrator form on that host.

Redirects use configured hosts, preserve the query string, leave local/staging hosts alone and never forward POST bodies. Disallowed non-GET/HEAD requests on portal hosts return 404 rather than forwarding submitted data to another host. Existing portal authentication and authorization checks remain in place. Signed download URLs must be generated on the client host when they are consumed there.

## Release checks

- Both portal subdomains must serve this same Laravel release through its public directory with valid SSL.
- Defaults activate only with APP_ENV=production. PORTAL_SUBDOMAINS_ENABLED can override this. PORTAL_PUBLIC_URL, PORTAL_CLIENT_URL and PORTAL_ADMIN_URL are configurable.
- Run `php artisan test --filter=PortalSubdomainTest`, the complete application test suite and `npm run build`.
- Clear/rebuild configuration and route caches during deployment, not on a repeating schedule.
- Confirm client and admin login links on desktop and mobile open the correct login in a new tab. Direct legacy URLs should redirect in their current tab.
- Test password login, reset email links and Google sign-in on the client host. Google OAuth's registered/configured callback must match the host where its state/PKCE session is stored. Do not disable OAuth state validation to accommodate a mismatched callback. If the current callback still targets the public host with host-only cookies, update the OAuth callback configuration and Google authorized redirect URI together before enabling this change.
- Verify blog comment/checkout continuation and account sessions with the production session settings before rollout. This patch does not silently broaden cookie scope.

No live settings or credentials are included in this patch.
