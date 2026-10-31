<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleReviewsTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_google_reviews_sync_is_idempotent_and_preserves_source_attribution(): void
    {
        Config::set('services.google.places_api_key', 'test-places-key');
        Config::set('services.google.places_place_id', 'test-place-id');
        Http::fake(['https://places.googleapis.com/v1/places/*' => Http::response([
            'reviews' => [[
                'name' => 'places/test-place-id/reviews/review-1',
                'rating' => 5,
                'publishTime' => '2026-10-30T08:00:00Z',
                'googleMapsUri' => 'https://www.google.com/maps/contrib/review-1',
                'authorAttribution' => [
                    'displayName' => 'Google reviewer',
                    'uri' => 'https://www.google.com/maps/contrib/reviewer-1',
                    'photoUri' => 'https://lh3.googleusercontent.com/test-avatar',
                ],
                'text' => ['text' => 'A published Google review.'],
                'originalText' => ['text' => 'A published Google review.'],
            ]],
        ], 200)]);

        $this->artisan('reviews:sync-google')->assertExitCode(0);
        $this->artisan('reviews:sync-google')->assertExitCode(0);

        $this->assertDatabaseCount('testimonials', 1);
        $this->assertDatabaseHas('testimonials', [
            'client_name' => 'Google reviewer',
            'testimonial' => 'A published Google review.',
            'source_url' => 'https://www.google.com/maps/contrib/review-1',
            'is_published' => true,
            'consent_confirmed' => true,
        ]);

        $this->get(route('home'))->assertOk()->assertSee('Google review')->assertSee('Write a review on Google');
        Http::assertSent(fn ($request) => $request->hasHeader('X-Goog-Api-Key', 'test-places-key')
            && $request->hasHeader('X-Goog-FieldMask', 'id,displayName,rating,reviews'));
    }

    public function test_google_reviews_sync_is_safe_when_credentials_are_missing(): void
    {
        Config::set('services.google.places_api_key', null);
        Config::set('services.google.places_place_id', null);

        $this->artisan('reviews:sync-google')->assertExitCode(0);
        Http::assertNothingSent();
        $this->assertDatabaseCount('testimonials', 0);
    }
}
