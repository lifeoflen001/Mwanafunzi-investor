<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Media extends Model
{
    public const STATUSES = ['published', 'unpublished', 'archived'];

    protected $table = 'media';
    protected $fillable = ['disk', 'path', 'filename', 'mime_type', 'size', 'width', 'height', 'alt_text', 'caption', 'title', 'source', 'copyright_owner', 'license', 'license_url', 'attribution_required', 'attribution_text', 'internal_usage_notes', 'rights_verified', 'status', 'uploaded_by', 'variants'];
    protected function casts(): array { return ['variants' => 'array']; }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->path);
    }

    public function usageCount(): int
    {
        return (int) (
            Course::where('featured_image', $this->path)->orWhere('og_image', $this->path)->count()
            + Product::where('thumbnail', $this->path)->orWhere('og_image', $this->path)->count()
            + Article::where('featured_image', $this->path)->orWhere('og_image', $this->path)->count()
            + LearningTopic::where(fn ($query) => $query->where('image', $this->path)->orWhere('hero_image', $this->path)->orWhere('og_image', $this->path))->count()
            + ProductImage::where('path', $this->path)->count()
            + Page::where(fn ($query) => $query->where('hero_image', $this->path)->orWhere('og_image', $this->path))->count()
            + PageSection::where('image', $this->path)->count()
            + Project::where(fn ($query) => $query->where('featured_image', $this->path)->orWhere('og_image', $this->path))->count()
            + Project::whereHas('gallery', fn ($query) => $query->where('media.path', $this->path))->count()
            + Service::where(fn ($query) => $query->where('featured_image', $this->path)->orWhere('og_image', $this->path))->count()
            + Testimonial::where(fn ($query) => $query->where('client_photo', $this->path)->orWhere('company_logo', $this->path))->count()
            + TeamMember::where(fn ($query) => $query->where('portrait', $this->path)->orWhere('og_image', $this->path))->count()
            + ArticleCategory::where('og_image', $this->path)->count()
            + Tag::where('og_image', $this->path)->count()
            + SiteSetting::where('value', $this->path)->count()
        );
    }

    /**
     * Count content references for a page of media in bulk.
     *
     * The media library renders usage counts for every card. Keeping the
     * legacy per-record method available is useful for one-off checks, but
     * list pages should use this method to avoid an N+1 query burst.
     */
    public static function usageCountsFor(Collection $media): array
    {
        $media = $media->filter(fn (self $item) => filled($item->path));
        $paths = $media->pluck('path')->unique()->values();
        $counts = $media->mapWithKeys(fn (self $item) => [$item->id => 0])->all();

        if ($paths->isEmpty()) {
            return $counts;
        }

        $addPathRows = function (string $model, array $columns) use ($paths, &$counts, $media): void {
            $rows = $model::query()
                ->where(function (Builder $query) use ($columns, $paths): void {
                    foreach ($columns as $index => $column) {
                        $method = $index === 0 ? 'whereIn' : 'orWhereIn';
                        $query->{$method}($column, $paths->all());
                    }
                })
                ->get($columns);

            foreach ($rows as $row) {
                foreach (array_unique(array_map(fn (string $column) => $row->{$column}, $columns)) as $path) {
                    $media->filter(fn (self $item) => $item->path === $path)->each(function (self $item) use (&$counts): void {
                        $counts[$item->id]++;
                    });
                }
            }
        };

        $addPathRows(Course::class, ['featured_image', 'og_image']);
        $addPathRows(Product::class, ['thumbnail', 'og_image']);
        $addPathRows(Article::class, ['featured_image', 'og_image']);
        $addPathRows(LearningTopic::class, ['image', 'hero_image', 'og_image']);
        $addPathRows(Page::class, ['hero_image', 'og_image']);
        $addPathRows(PageSection::class, ['image']);
        $addPathRows(Project::class, ['featured_image', 'og_image']);
        $addPathRows(Service::class, ['featured_image', 'og_image']);
        $addPathRows(Testimonial::class, ['client_photo', 'company_logo']);
        $addPathRows(TeamMember::class, ['portrait', 'og_image']);
        $addPathRows(ArticleCategory::class, ['og_image']);
        $addPathRows(Tag::class, ['og_image']);

        $productImages = ProductImage::query()->whereIn('path', $paths->all())->get(['path']);
        foreach ($productImages as $image) {
            $media->filter(fn (self $item) => $item->path === $image->path)->each(fn (self $item) => $counts[$item->id]++);
        }

        $projectGallery = DB::table('project_media')->whereIn('media_id', $media->pluck('id')->all())->get(['media_id']);
        foreach ($projectGallery as $galleryItem) {
            if (array_key_exists($galleryItem->media_id, $counts)) {
                $counts[$galleryItem->media_id]++;
            }
        }

        $siteSettingReferences = SiteSetting::query()->whereIn('value', $paths->all())->get(['value']);
        foreach ($siteSettingReferences as $setting) {
            $media->filter(fn (self $item) => $item->path === $setting->value)->each(fn (self $item) => $counts[$item->id]++);
        }

        return $counts;
    }
}
