<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use App\Models\SocialLink;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminTaxonomyController extends Controller
{
    public function categories() { return view('admin.taxonomy.index', ['title' => 'Article categories', 'eyebrow' => 'CMS / Journal', 'items' => ArticleCategory::withCount('articles')->orderBy('name')->get(), 'kind' => 'categories']); }
    public function categoryStore(Request $request) { $data = $request->validate(['name' => ['required', 'string', 'max:120']]); ArticleCategory::create(['name' => $data['name'], 'slug' => Str::slug($data['name'])]); return back()->with('success', 'Category created.'); }
    public function categoryDestroy(ArticleCategory $category) { $category->delete(); return back()->with('success', 'Category removed.'); }

    public function tags() { return view('admin.taxonomy.index', ['title' => 'Article tags', 'eyebrow' => 'CMS / Journal', 'items' => Tag::withCount('articles')->orderBy('name')->get(), 'kind' => 'tags']); }
    public function tagStore(Request $request) { $data = $request->validate(['name' => ['required', 'string', 'max:120']]); Tag::create(['name' => $data['name'], 'slug' => Str::slug($data['name'])]); return back()->with('success', 'Tag created.'); }
    public function tagDestroy(Tag $tag) { $tag->delete(); return back()->with('success', 'Tag removed.'); }

    public function socialLinks() { return view('admin.social-links.index', ['links' => SocialLink::orderBy('sort_order')->get()]); }
    public function socialLinkStore(Request $request) { $data = $request->validate(['label' => ['required', 'string', 'max:80'], 'url' => ['required', 'url'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_visible' => ['nullable', 'boolean']]); $data['is_visible'] = $request->boolean('is_visible'); SocialLink::create($data); return back()->with('success', 'Social link added.'); }
    public function socialLinkUpdate(Request $request, SocialLink $socialLink) { $data = $request->validate(['label' => ['required', 'string', 'max:80'], 'url' => ['required', 'url'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_visible' => ['nullable', 'boolean']]); $data['is_visible'] = $request->boolean('is_visible'); $socialLink->update($data); return back()->with('success', 'Social link updated.'); }
    public function socialLinkDestroy(SocialLink $socialLink) { $socialLink->delete(); return back()->with('success', 'Social link removed.'); }
}
