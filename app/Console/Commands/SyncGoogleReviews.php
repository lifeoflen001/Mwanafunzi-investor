<?php

namespace App\Console\Commands;

use App\Models\Testimonial;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncGoogleReviews extends Command
{
    protected $signature = 'reviews:sync-google {--dry-run : Fetch and report without writing records}';

    protected $description = 'Synchronise public Google reviews into the testimonial CMS';

    public function handle(): int
    {
        $apiKey = (string) config('services.google.places_api_key');
        $placeId = (string) config('services.google.places_place_id');

        if ($apiKey === '' || $placeId === '') {
            $this->warn('Google review sync is not configured. Set GOOGLE_PLACES_API_KEY and GOOGLE_PLACES_PLACE_ID.');

            return self::SUCCESS;
        }

        $response = Http::acceptJson()
            ->withHeaders([
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'id,displayName,rating,reviews',
            ])
            ->timeout(15)
            ->get('https://places.googleapis.com/v1/places/'.rawurlencode($placeId));

        if ($response->failed()) {
            $this->error('Google Places API request failed with HTTP '.$response->status().'.');

            return self::FAILURE;
        }

        $autoPublish = filter_var(config('services.google.reviews_auto_publish', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
        $reviews = collect($response->json('reviews', []))
            ->filter(fn ($review) => filled(data_get($review, 'name'))
                && filled(data_get($review, 'text.text'))
                && filled(data_get($review, 'authorAttribution.displayName')))
            ->values();

        if ($reviews->isEmpty()) {
            $this->info('Google returned no review records. Existing testimonials were left unchanged.');

            return self::SUCCESS;
        }

        foreach ($reviews as $index => $review) {
            $sourceUrl = (string) data_get($review, 'googleMapsUri');
            if (! filter_var($sourceUrl, FILTER_VALIDATE_URL)) continue;

            $payload = [
                'client_name' => Str::limit((string) data_get($review, 'authorAttribution.displayName', 'Google reviewer'), 190, ''),
                'client_role' => null,
                'company' => null,
                'testimonial' => (string) data_get($review, 'originalText.text', data_get($review, 'text.text')),
                'client_photo' => data_get($review, 'authorAttribution.photoUri'),
                'company_logo' => null,
                'project_id' => null,
                'business_unit_id' => null,
                'rating' => in_array((int) data_get($review, 'rating', 0), range(1, 5), true) ? (int) data_get($review, 'rating') : null,
                'consent_confirmed' => true,
                'consent_confirmed_at' => now(),
                'consent_notes' => 'Imported from Google Places API; Google author and source attribution retained.',
                'anonymous_display' => false,
                'initials_only' => false,
                'hide_company' => true,
                'is_featured' => $autoPublish,
                'is_published' => $autoPublish,
                'sort_order' => 900 + $index,
                'testimonial_date' => data_get($review, 'publishTime') ? substr((string) data_get($review, 'publishTime'), 0, 10) : null,
            ];

            if ($this->option('dry-run')) {
                $this->line('Would sync: '.$payload['client_name']);
                continue;
            }

            Testimonial::updateOrCreate(['source_url' => $sourceUrl], $payload);
        }

        $this->info(($this->option('dry-run') ? 'Reviewed' : 'Synchronised').' '.$reviews->count().' Google review record(s).');

        return self::SUCCESS;
    }
}
