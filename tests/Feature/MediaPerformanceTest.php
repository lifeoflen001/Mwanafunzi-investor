<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MediaPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_media_usage_counts_are_bulk_loaded_without_changing_reference_totals(): void
    {
        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/performance-check.jpg',
            'filename' => 'performance-check.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'alt_text' => 'Performance check',
            'status' => 'published',
        ]);
        Page::query()->where('key', 'home')->firstOrFail()->update(['hero_image' => $media->path]);
        $legacyCount = $media->usageCount();

        $queries = 0;
        DB::listen(static function () use (&$queries): void {
            $queries++;
        });

        $bulkCount = Media::usageCountsFor(collect([$media]))[$media->id];

        $this->assertSame($legacyCount, $bulkCount);
        $this->assertSame(1, $bulkCount);
        $this->assertLessThan(20, $queries, 'Media usage counts should use a fixed-size bulk query pass.');
    }
}
