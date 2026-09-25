<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Page;
use App\Support\AdminAudit;
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
        AdminAudit::record('page.created', 'Created page '.$page->name, $page);
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
        $this->createSection($request, $page);
        AdminAudit::record('page.updated', 'Updated page '.$page->name, $page, ['sections' => $page->sections()->count()]);
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
            'hero_highlight' => ['nullable', 'string', 'max:190'],
            'hero_summary' => ['nullable', 'string', 'max:1000'],
            'hero_image' => ['nullable', 'string', 'max:255'],
            'hero_primary_label' => ['nullable', 'string', 'max:120'],
            'hero_primary_url' => ['nullable', 'string', 'max:500'],
            'hero_secondary_label' => ['nullable', 'string', 'max:120'],
            'hero_secondary_url' => ['nullable', 'string', 'max:500'],
            'hero_note' => ['nullable', 'string', 'max:500'],
            'hero_aside' => ['nullable', 'string', 'max:500'],
            'hero_aside_index' => ['nullable', 'string', 'max:50'],
            'hero_overlay' => ['required', 'in:light,medium,strong'],
            'hero_alignment' => ['required', 'in:left,center,right'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:300'],
            'published_at' => ['nullable', 'date'],
            'is_visible' => ['nullable', 'boolean'],
            'sections' => ['nullable', 'array'],
            'sections.*.heading' => ['nullable', 'string', 'max:190'],
            'sections.*.body' => ['nullable', 'string', 'max:20000'],
            'sections.*.image' => ['nullable', 'string', 'max:255'],
            'sections.*.cta_label' => ['nullable', 'string', 'max:120'],
            'sections.*.cta_url' => ['nullable', 'string', 'max:500'],
            'sections.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'sections.*.is_enabled' => ['nullable', 'boolean'],
            'sections.*.list' => ['nullable', 'string', 'max:10000'],
            'sections.*.callout' => ['nullable', 'string', 'max:1000'],
            'sections.*.steps' => ['nullable', 'string', 'max:10000'],
            'new_section_key' => ['nullable', 'string', 'max:80', 'alpha_dash'],
            'new_section_type' => ['nullable', 'in:rich_text,split_content,featured_topics,featured_courses,featured_products,latest_journal,framework,faq,quote,feature_grid,contact_block,cta'],
            'new_section_heading' => ['nullable', 'string', 'max:190'],
            'new_section_body' => ['nullable', 'string', 'max:20000'],
            'new_section_image' => ['nullable', 'string', 'max:255'],
            'new_section_cta_label' => ['nullable', 'string', 'max:120'],
            'new_section_cta_url' => ['nullable', 'string', 'max:500'],
            'new_section_sort_order' => ['nullable', 'integer', 'min:0'],
            'new_section_enabled' => ['nullable', 'boolean'],
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
            if (array_key_exists('steps', $input)) {
                $payload['steps'] = collect(preg_split('/\r\n|\r|\n/', (string) $input['steps']))
                    ->map(function ($item) {
                        [$title, $body] = array_pad(explode('|', $item, 2), 2, '');
                        return ['title' => trim($title), 'body' => trim($body)];
                    })
                    ->filter(fn ($item) => $item['title'] !== '')
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

    private function createSection(Request $request, Page $page): void
    {
        $key = trim((string) $request->input('new_section_key'));
        if ($key === '') return;

        abort_unless(! $page->sections()->where('key', $key)->exists(), 422, 'That section key is already in use on this page.');
        $page->sections()->create([
            'key' => $key,
            'section_type' => $request->input('new_section_type', 'rich_text'),
            'heading' => $request->input('new_section_heading'),
            'body' => $request->input('new_section_body'),
            'image' => $request->input('new_section_image'),
            'cta_label' => $request->input('new_section_cta_label'),
            'cta_url' => $request->input('new_section_cta_url'),
            'sort_order' => max(0, (int) $request->input('new_section_sort_order', 0)),
            'is_enabled' => $request->boolean('new_section_enabled', true),
        ]);
    }
}
