# Google reviews

The public testimonial sections include a `Write a review on Google` link using the configured Google Business review URL. Existing testimonial records can also show their source as `Google review` when their source URL points to Google.

## Automatic import

Automatic import uses the server-side Google Places API (New) command:

```bash
php artisan reviews:sync-google
```

Configure these values in the production environment; never expose the API key in Blade, JavaScript or the public repository:

```dotenv
GOOGLE_REVIEW_URL=https://g.page/r/CQ30z52TUXjhEAI/review
GOOGLE_PLACES_API_KEY=replace-with-a-restricted-server-key
GOOGLE_PLACES_PLACE_ID=ChIJF178_ZXjNhgRDfTPnZNReOE
GOOGLE_REVIEWS_AUTO_PUBLISH=true
```

The supplied review URL resolves to the configured Place ID. The sync is idempotent, does not delete existing records, and keeps the individual Google Maps review URL on each imported record. The scheduler runs it daily at 02:30 when the hosting cron invokes `php artisan schedule:run`.

Google review content must retain author attribution and a direct link to the source review. The public card therefore shows the author identity, optional author photo, `Google review` origin link, and the review date when returned. Google API billing, API restrictions and the hosting scheduler remain deployment responsibilities.
