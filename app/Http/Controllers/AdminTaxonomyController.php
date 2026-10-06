<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use App\Models\SocialLink;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Support\AdminAudit;
use App\Support\PublicSiteData;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminTaxonomyController extends Controller
{
    public function categories(Request $request)
    {
        $term = trim((string) $request->input('q', ''));
        $categories = ArticleCategory::withCount('articles')->when($term !== '', fn ($query) => $query->where(fn ($search) => $search->where('name', 'like', '%'.$term.'%')->orWhere('slug', 'like', '%'.$term.'%')))->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.taxonomy.index', ['title' => 'Article categories', 'eyebrow' => 'CMS / Journal', 'items' => $categories, 'kind' => 'categories', 'term' => $term]);
    }

    public function categoryCreate() { return view('admin.taxonomy.form', ['title' => 'New category', 'kind' => 'categories', 'item' => new ArticleCategory(['is_active' => true]), 'action' => route('admin.categories.store')]); }
    public function categoryEdit(ArticleCategory $category) { return view('admin.taxonomy.form', ['title' => 'Edit category', 'kind' => 'categories', 'item' => $category, 'action' => route('admin.categories.update', $category)]); }
    public function categoryStore(Request $request) { $data = $this->categoryData($request); ArticleCategory::create($data); return to_route('admin.categories')->with('success', 'Category created.'); }
    public function categoryUpdate(Request $request, ArticleCategory $category) { $category->update($this->categoryData($request, $category)); return to_route('admin.categories')->with('success', 'Category updated.'); }
    public function categoryDestroy(ArticleCategory $category) { if ($category->articles()->exists()) return back()->with('error', 'This category is still assigned to articles. Reassign them before removing it.'); $category->delete(); return back()->with('success', 'Category removed.'); }

    public function tags(Request $request)
    {
        $term = trim((string) $request->input('q', ''));
        $tags = Tag::withCount('articles')->when($term !== '', fn ($query) => $query->where(fn ($search) => $search->where('name', 'like', '%'.$term.'%')->orWhere('slug', 'like', '%'.$term.'%')))->orderBy('name')->paginate(30)->withQueryString();
        return view('admin.taxonomy.index', ['title' => 'Article tags', 'eyebrow' => 'CMS / Journal', 'items' => $tags, 'kind' => 'tags', 'term' => $term]);
    }

    public function tagCreate() { return view('admin.taxonomy.form', ['title' => 'New tag', 'kind' => 'tags', 'item' => new Tag(), 'action' => route('admin.tags.store')]); }
    public function tagEdit(Tag $tag) { return view('admin.taxonomy.form', ['title' => 'Edit tag', 'kind' => 'tags', 'item' => $tag, 'action' => route('admin.tags.update', $tag)]); }
    public function tagStore(Request $request) { $data = $this->tagData($request); Tag::create($data); return to_route('admin.tags')->with('success', 'Tag created.'); }
    public function tagUpdate(Request $request, Tag $tag) { $tag->update($this->tagData($request, $tag)); return to_route('admin.tags')->with('success', 'Tag updated.'); }
    public function tagDestroy(Tag $tag) { $tag->articles()->detach(); $tag->delete(); return back()->with('success', 'Tag removed.'); }

    private function categoryData(Request $request, ?ArticleCategory $category = null): array
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string', 'max:140'], 'description' => ['nullable', 'string', 'max:1000'], 'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'], 'sort_order' => ['nullable', 'integer', 'min:0'], 'is_active' => ['nullable', 'boolean']]);
        $data['slug'] = Str::slug($data['slug'] ?? $data['name']);
        validator(['slug' => $data['slug']], ['slug' => [Rule::unique('article_categories', 'slug')->ignore($category?->id)]])->validate();
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }

    private function tagData(Request $request, ?Tag $tag = null): array
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string', 'max:140'], 'description' => ['nullable', 'string', 'max:1000']]);
        $data['name'] = trim($data['name']);
        $duplicate = Tag::whereRaw('lower(name) = ?', [strtolower($data['name'])])->when($tag, fn ($query) => $query->where('id', '!=', $tag->id))->exists();
        if ($duplicate) throw ValidationException::withMessages(['name' => 'A tag with this name already exists. Use the existing tag instead.']);
        $data['slug'] = Str::slug($data['slug'] ?? $data['name']);
        validator(['slug' => $data['slug']], ['slug' => [Rule::unique('tags', 'slug')->ignore($tag?->id)]])->validate();
        return $data;
    }

    public function socialLinks() { return view('admin.social-links.index', ['links' => SocialLink::orderBy('sort_order')->get()]); }
    public function socialLinkStore(Request $request) { $data = $request->validate(['label' => ['required', 'string', 'max:80'], 'url' => ['required', 'url'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_visible' => ['nullable', 'boolean']]); $data['is_visible'] = $request->boolean('is_visible'); $link = SocialLink::create($data); PublicSiteData::forgetCache(); AdminAudit::record('social.created', 'Added social link '.$link->label, $link); return back()->with('success', 'Social link added.'); }
    public function socialLinkUpdate(Request $request, SocialLink $socialLink) { $data = $request->validate(['label' => ['required', 'string', 'max:80'], 'url' => ['required', 'url'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_visible' => ['nullable', 'boolean']]); $data['is_visible'] = $request->boolean('is_visible'); $socialLink->update($data); PublicSiteData::forgetCache(); AdminAudit::record('social.updated', 'Updated social link '.$socialLink->label, $socialLink); return back()->with('success', 'Social link updated.'); }
    public function socialLinkDestroy(SocialLink $socialLink) { $socialLink->delete(); PublicSiteData::forgetCache(); AdminAudit::record('social.deleted', 'Removed social link '.$socialLink->label, $socialLink); return back()->with('success', 'Social link removed.'); }
}
