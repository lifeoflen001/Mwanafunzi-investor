<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\ProductImage;
use App\Models\Course;
use App\Models\Product;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Models\TeamMember;
use App\Models\LearningTopic;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\MediaService;
use App\Support\AdminAudit;

class MediaController extends Controller
{
    public function search(Request $request)
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'string', 'max:80'],
            'status' => ['nullable', 'in:published,unpublished,archived'],
        ]);

        $query = Media::query()
            ->when($data['q'] ?? null, function ($query, string $term) {
                $query->where(function ($nested) use ($term) {
                    $nested->where('filename', 'like', '%'.$term.'%')
                        ->orWhere('title', 'like', '%'.$term.'%')
                        ->orWhere('caption', 'like', '%'.$term.'%')
                        ->orWhere('alt_text', 'like', '%'.$term.'%');
                });
            })
            ->when($data['type'] ?? null, fn ($query, string $type) => $query->where('mime_type', 'like', $type.'%'))
            ->when($data['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest()
            ->limit(40)
            ->get();

        return response()->json($query->map(fn (Media $media) => [
            'path' => $media->path,
            'url' => $media->url,
            'label' => $media->filename.($media->width ? ' · '.$media->width.'×'.$media->height : ''),
            'alt_text' => $media->alt_text,
            'mime_type' => $media->mime_type,
            'status' => $media->status,
        ])->values());
    }

    public function index(Request $request)
    {
        $query = Media::with('uploader');
        if ($request->filled('q')) $query->where(fn ($q) => $q->where('filename', 'like', '%'.$request->string('q').'%')->orWhere('title', 'like', '%'.$request->string('q').'%')->orWhere('caption', 'like', '%'.$request->string('q').'%'));
        if ($request->filled('type')) $query->where('mime_type', 'like', $request->string('type').'%');
        if ($request->filled('status') && in_array($request->string('status')->toString(), Media::STATUSES, true)) $query->where('status', $request->string('status'));
        $media = $query->latest()->paginate(24)->withQueryString();

        return view('admin.media.index', [
            'media' => $media,
            'usageCounts' => Media::usageCountsFor($media->getCollection()),
        ]);
    }

    public function store(Request $request, MediaService $mediaService)
    {
        $data = $request->validate(['file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'], 'title' => ['nullable', 'string', 'max:190'], 'alt_text' => ['required', 'string', 'max:190'], 'caption' => ['nullable', 'string', 'max:1000'], 'source' => ['nullable', 'string', 'max:500'], 'copyright_owner' => ['nullable', 'string', 'max:190'], 'license' => ['nullable', 'string', 'max:190'], 'license_url' => ['nullable', 'url', 'max:500'], 'attribution_required' => ['nullable', 'boolean'], 'attribution_text' => ['nullable', 'string', 'max:500'], 'internal_usage_notes' => ['nullable', 'string', 'max:2000'], 'rights_verified' => ['nullable', 'boolean'], 'status' => ['nullable', 'in:published,unpublished,archived']]);
        $file = $request->file('file');
        $media = $mediaService->store($file, 'media', $data['alt_text'], $data['title'] ?? null);
        $media->update(['uploaded_by' => $request->user()->id, ...collect($data)->except('file')->all(), 'attribution_required' => $request->boolean('attribution_required'), 'rights_verified' => $request->boolean('rights_verified')]);
        AdminAudit::record('media.created', 'Uploaded media '.$media->filename, $media);
        return back()->with('success', 'Media uploaded.');
    }

    public function inlineStore(Request $request, MediaService $mediaService)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,avif,mp4,webm,mov,ogv,m4v', 'max:51200'],
            'title' => ['nullable', 'string', 'max:190'],
            'alt_text' => ['nullable', 'string', 'max:190'],
        ]);

        $media = $mediaService->store($data['file'], 'media', $data['alt_text'] ?? null, $data['title'] ?? null);
        $media->update(['uploaded_by' => $request->user()->id]);
        AdminAudit::record('media.created', 'Uploaded media '.$media->filename, $media);

        return response()->json([
            'path' => $media->path,
            'url' => $media->url,
            'filename' => $media->filename,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'width' => $media->width,
            'height' => $media->height,
        ], 201);
    }

    public function update(Request $request, Media $media)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:190'],
            'alt_text' => ['required', 'string', 'max:190'],
            'caption' => ['nullable', 'string', 'max:1000'],
            'source' => ['nullable', 'string', 'max:500'],
            'copyright_owner' => ['nullable', 'string', 'max:190'],
            'license' => ['nullable', 'string', 'max:190'],
            'license_url' => ['nullable', 'url', 'max:500'],
            'attribution_required' => ['nullable', 'boolean'],
            'attribution_text' => ['nullable', 'string', 'max:500'],
            'internal_usage_notes' => ['nullable', 'string', 'max:2000'],
            'rights_verified' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:published,unpublished,archived'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'],
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $disk = $media->disk ?: 'public';
            Storage::disk($disk)->put($media->path, file_get_contents($file->getRealPath()));
            foreach ($media->variants ?? [] as $variant) {
                if (! empty($variant['path'])) Storage::disk($disk)->delete($variant['path']);
            }
            $dimensions = @getimagesize($file->getRealPath()) ?: [null, null];
            $data = array_merge($data, [
                'filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'width' => $dimensions[0],
                'height' => $dimensions[1],
                'variants' => [],
            ]);
        }
        unset($data['file']);
        $data['attribution_required'] = $request->boolean('attribution_required');
        $data['rights_verified'] = $request->boolean('rights_verified');
        $media->update($data);
        AdminAudit::record($request->hasFile('file') ? 'media.replaced' : 'media.updated', ($request->hasFile('file') ? 'Replaced media ' : 'Updated media ').$media->filename, $media);
        return back()->with('success', 'Media metadata updated.');
    }

    public function destroy(Media $media, MediaService $mediaService)
    {
        $deleteFiles = request()->boolean('delete_files', true);
        $inUse = ProductImage::where('path', $media->path)->exists()
            || Course::where('featured_image', $media->path)->orWhere('og_image', $media->path)->exists()
            || Product::where('thumbnail', $media->path)->orWhere('og_image', $media->path)->exists()
            || Article::where('featured_image', $media->path)->orWhere('og_image', $media->path)->exists()
            || LearningTopic::where('image', $media->path)->exists()
            || \App\Models\Page::where(fn ($query) => $query->where('hero_image', $media->path)->orWhere('og_image', $media->path))->exists()
            || \App\Models\PageSection::where('image', $media->path)->exists()
            || \App\Models\Project::where(fn ($query) => $query->where('featured_image', $media->path)->orWhere('og_image', $media->path))->exists()
            || \App\Models\Project::whereHas('gallery', fn ($query) => $query->where('media.path', $media->path))->exists()
            || \App\Models\Service::where(fn ($query) => $query->where('featured_image', $media->path)->orWhere('og_image', $media->path))->exists()
            || \App\Models\Testimonial::where(fn ($query) => $query->where('client_photo', $media->path)->orWhere('company_logo', $media->path))->exists()
            || LearningTopic::where(fn ($query) => $query->where('image', $media->path)->orWhere('hero_image', $media->path)->orWhere('og_image', $media->path))->exists()
            || TeamMember::where(fn ($query) => $query->where('portrait', $media->path)->orWhere('og_image', $media->path))->exists()
            || ArticleCategory::where('og_image', $media->path)->exists()
            || Tag::where('og_image', $media->path)->exists()
            || SiteSetting::where('value', $media->path)->exists();
        if ($deleteFiles && $inUse) {
            return back()->withErrors(['media' => 'The stored file cannot be deleted while content still references it. Remove or replace those references first, or choose “Remove record only”.']);
        }
        $filename = $media->filename;
        $mediaService->delete($media, $deleteFiles);
        AdminAudit::record('media.deleted', ($deleteFiles ? 'Deleted media and stored files ' : 'Removed media record while keeping stored files ').$filename, $media);
        return back()->with('success', $deleteFiles ? 'Media and its stored files were deleted.' : 'Media record removed; stored files were kept.');
    }
}
