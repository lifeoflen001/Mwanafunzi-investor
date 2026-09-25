<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\ProductImage;
use App\Models\Course;
use App\Models\Product;
use App\Models\Article;
use App\Models\LearningTopic;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Services\MediaService;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::query();
        if ($request->filled('q')) $query->where(fn ($q) => $q->where('filename', 'like', '%'.$request->string('q').'%')->orWhere('title', 'like', '%'.$request->string('q').'%'));
        if ($request->filled('type')) $query->where('mime_type', 'like', $request->string('type').'%');
        return view('admin.media.index', ['media' => $query->latest()->paginate(24)->withQueryString()]);
    }

    public function store(Request $request, MediaService $mediaService)
    {
        $data = $request->validate(['file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,avif,svg', 'max:8192'], 'title' => ['nullable', 'string', 'max:190'], 'alt_text' => ['required', 'string', 'max:190']]);
        $file = $request->file('file');
        $mediaService->store($file, 'media', $data['alt_text'], $data['title'] ?? null);
        return back()->with('success', 'Media uploaded.');
    }

    public function update(Request $request, Media $media)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:190'],
            'alt_text' => ['required', 'string', 'max:190'],
        ]);
        $media->update($data);
        return back()->with('success', 'Media metadata updated.');
    }

    public function destroy(Media $media, MediaService $mediaService)
    {
        $inUse = ProductImage::where('path', $media->path)->exists()
            || Course::where('featured_image', $media->path)->orWhere('og_image', $media->path)->exists()
            || Product::where('thumbnail', $media->path)->orWhere('og_image', $media->path)->exists()
            || Article::where('featured_image', $media->path)->orWhere('og_image', $media->path)->exists()
            || LearningTopic::where('image', $media->path)->exists()
            || \App\Models\Page::where('hero_image', $media->path)->exists()
            || \App\Models\PageSection::where('image', $media->path)->exists()
            || SiteSetting::where('value', $media->path)->exists();
        if ($inUse) {
            return back()->withErrors(['media' => 'This asset is still assigned to published or draft content. Replace the reference first.']);
        }
        $mediaService->delete($media);
        return back()->with('success', 'Media removed.');
    }
}
