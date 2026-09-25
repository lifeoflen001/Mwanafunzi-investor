<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminPageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::withTrashed()->withCount('sections')->orderBy('name')->paginate(20)]);
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page(['status' => 'draft', 'is_visible' => true, 'hero_overlay' => 'medium', 'hero_alignment' => 'left']), 'media' => Media::orderBy('filename')->get(), 'action' => route('admin.pages.store')]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['key'] = Str::slug($data['key'], '-');
        $data['slug'] = $data['slug'] ? Str::slug($data['slug']) : null;
        $data['is_visible'] = $request->boolean('is_visible');
        $page = Page::create($data);
        return to_route('admin.pages.edit', $page)->with('success', 'Page created. Add sections when the page content is ready.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', ['page' => $page->load('sections'), 'media' => Media::orderBy('filename')->get(), 'action' => route('admin.pages.update', $page)]);
    }

    public function update(Request $request, Page $page)
    {
        $data = $this->validated($request, $page);
        $data['key'] = Str::slug($data['key'], '-');
        $data['slug'] = $data['slug'] ? Str::slug($data['slug']) : null;
        $data['is_visible'] = $request->boolean('is_visible');
        $page->update($data);
        $this->saveSections($request, $page);
        return back()->with('success', 'Page and sections updated.');
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        return $request->validate([
            'key' => ['required', 'string', 'max:100', Rule::unique('pages', 'key')->ignore($page?->id)],
            'slug' => ['nullable', 'string', 'max:190', Rule::unique('pages', 'slug')->ignore($page?->id)],
            'name' => ['required', 'string', 'max:190'],
            'page_type' => ['required', 'in:home,index,standard,form,policy'],
            'status' => ['required', 'in:draft,published,archived'],
            'hero_eyebrow' => ['nullable', 'string', 'max:190'],
            'hero_title' => ['nullable', 'string', 'max:190'],
            'hero_summary' => ['nullable', 'string', 'max:1000'],
            'hero_image' => ['nullable', 'string', 'max:255'],
            'hero_overlay' => ['required', 'in:light,medium,strong'],
            'hero_alignment' => ['required', 'in:left,center,right'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:300'],
            'published_at' => ['nullable', 'date'],
            'is_visible' => ['nullable', 'boolean'],
        ]);
    }

    private function saveSections(Request $request, Page $page): void
    {
        foreach ((array) $request->input('sections', []) as $sectionId => $input) {
            $section = $page->sections()->whereKey($sectionId)->first();
            if (! $section) continue;
            $payload = $section->payload ?: [];
            if (array_key_exists('callout', $input)) {
                $payload['callout'] = trim((string) $input['callout']) ?: null;
            }
            if (array_key_exists('list', $input)) {
                $payload['list'] = collect(preg_split('/\r\n|\r|\n/', (string) $input['list']))
                    ->map(fn ($item) => trim($item))
                    ->filter()
                    ->values()
                    ->all();
            }
            $section->update([
                'heading' => $input['heading'] ?? null,
                'body' => $input['body'] ?? null,
                'image' => $input['image'] ?? null,
                'cta_label' => $input['cta_label'] ?? null,
                'cta_url' => $input['cta_url'] ?? null,
                'payload' => $payload,
                'sort_order' => max(0, (int) ($input['sort_order'] ?? $section->sort_order)),
                'is_enabled' => filter_var($input['is_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
