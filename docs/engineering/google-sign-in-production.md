# Google sign-in production checklist

The application keeps administrator authentication separate. Google sign-in creates or authenticates ordinary client accounts only; it never grants administrator access.

## Google Cloud setup

- Create or select the Google Cloud project that owns the Mwanafunzi Investor OAuth client.
- Configure the OAuth consent screen with the production application name, support contact, developer contact, privacy policy URL, and terms URL.
- Request only the required scopes: `openid`, `email`, and `profile`.
- Create a **Web application** OAuth client.
- Add the production site origin as an authorized JavaScript origin, for example `https://mwanafunziinvestor.com`.
- Add the exact callback URL as an authorized redirect URI:
  `https://mwanafunziinvestor.com/auth/google/callback`
- If the site uses `www`, add the `www` origin and callback only when that hostname is an intentional canonical host.
- Move the consent screen out of testing mode before launch and complete Google verification if Google requests it.
- Keep the client secret out of Git, browser code, logs, screenshots, and admin pages.

## Production environment

Set these values on the server, then clear cached configuration:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mwanafunziinvestor.com
GOOGLE_CLIENT_ID=replace-with-server-secret
GOOGLE_CLIENT_SECRET=replace-with-server-secret
GOOGLE_REDIRECT_URI=https://mwanafunziinvestor.com/auth/google/callback
```

The callback must match the Google Cloud value byte-for-byte, including scheme, hostname, path, and trailing slash behavior. Run the new migration before enabling the button:

```text
php artisan migrate --force
php artisan config:clear
php artisan config:cache
```

Use HTTPS in production and configure the web server to redirect HTTP to the canonical HTTPS hostname before OAuth testing. Do not place credentials in `.env.example`; it intentionally contains empty placeholders.

## Functional smoke test

1. Open the public client login page and confirm the Google button is visible.
2. Sign in with a new Google account and confirm the user returns to `/`, not `/account`.
3. Sign in again and confirm the same Google identity reaches the public site without creating a duplicate user.
4. Try a client comment sign-in and confirm the callback returns to the article comment area.
5. Confirm an existing password account with the same email is asked for its password before Google is linked.
6. In client Security, connect Google, set a local password, and verify disconnect works only after a second sign-in method exists.
7. Confirm an administrator email cannot authenticate through the client Google flow.
8. Review the admin customer record: it may show Google as a sign-in method, but never an OAuth token or secret.

## Operational checks

- Confirm `/auth/google/redirect` and `/auth/google/callback` are reachable through the HTTPS application host.
- Confirm `/robots.txt` and the sitemap remain publicly accessible after deployment.
- Add the canonical HTTPS property and sitemap to Google Search Console.
- Add the canonical HTTPS site and sitemap to Bing Webmaster Tools.
- Monitor application logs for the generic Google failure message and the exception class only; OAuth tokens and claims must not be logged.
- If the canonical hostname changes, update `APP_URL`, `GOOGLE_REDIRECT_URI`, Google Cloud origins/redirects, Search Console, and Bing Webmaster Tools together.
