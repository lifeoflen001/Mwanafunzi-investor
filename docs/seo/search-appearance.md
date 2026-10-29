# Branded Google search appearance

The goal is a clear Mwanafunzi Investor home result with useful section links. Google selects sitelinks, their order, snippets, AI Overviews and related questions automatically; no markup forces the Replit layout.

## Changes

- Built-in CMS aliases such as `/pages/projects` permanently redirect to the corresponding public route, after publication and visibility checks. Custom CMS pages continue to render.
- Sitemap and admin SEO inventory exclude built-in CMS aliases using the same route registry.
- Shared Organization metadata supplies a stable ID, logo and configured visible HTTPS social profiles. Homepage WebSite metadata references that organization. JSON is encoded safely for script elements.
- Homepage metadata has a nonempty configured fallback title and resolves its canonical consistently with internal pages. About has its own fallback title.
- Footer links expose Services, Projects, Financial Academy, Digital Software, Creative Studio and the HTML site map, without duplicating identical links already configured in those groups.
- Existing editorial titles, navigation settings, styling and page content remain editable.

## Release verification

Run `php artisan test --filter=SeoTest` and the complete CI suite before merging. On production, verify `/` returns 200 with an indexable robots directive, self canonical, one description and valid WebSite/Organization JSON-LD. Verify `/pages/projects` redirects to `/projects` when that CMS slug exists; unpublished aliases must return 404. Verify sitemap URLs return their intended public pages.

After deployment, use the verified Search Console property to inspect the homepage and request indexing, then submit `https://mwanafunziinvestor.com/sitemap.xml`. Inspect representative main sections as needed. Do not submit login, admin or private portal pages as content targets. Google must recrawl and reprocess changes; there is no guaranteed schedule or sitelinks outcome.

References: https://developers.google.com/search/docs/appearance/sitelinks and https://developers.google.com/search/docs/appearance/site-names
