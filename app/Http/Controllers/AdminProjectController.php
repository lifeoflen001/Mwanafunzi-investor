<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Project;
use App\Models\Service;
use App\Support\AdminAudit;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminProjectController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->input('q')),
            'status' => trim((string) $request->input('status')),
            'industry' => trim((string) $request->input('industry')),
        ];
        $query = Project::query()
            ->withCount(['services', 'gallery', 'testimonials'])
            ->when($filters['q'], fn ($query, string $search) => $query->where(fn ($query) => $query->where('title', 'like', '%'.$search.'%')->orWhere('slug', 'like', '%'.$search.'%')))
            ->when($filters['status'], fn ($query, string $status) => $query->where('project_status', $status))
            ->when($filters['industry'], fn ($query, string $industry) => $query->where('industry', $industry))
            ->orderBy('display_order')
            ->orderBy('title');

        return view('admin.projects.index', [
            'projects' => $query->paginate(20)->withQueryString(),
            'filters' => $filters,
            'statuses' => Project::STATUSES,
            'industries' => Project::whereNotNull('industry')->where('industry', '<>', '')->distinct()->orderBy('industry')->pluck('industry'),
        ]);
    }

    public function create() { return view('admin.projects.form', ['project' => new Project(['status' => 'draft']), 'services' => Service::active()->orderBy('sort_order')->orderBy('title')->get(), 'media' => Media::latest()->limit(100)->get(), 'action' => route('admin.projects.store')]); }

    public function store(Request $request)
    {
        $project = Project::create($this->prepare($request, $this->validated($request)));
        $this->syncRelations($request, $project);
        AdminAudit::record('project.created', 'Created project '.$project->title, $project);
        return to_route('admin.projects.edit', $project)->with('success', 'Project created.');
    }

    public function edit(Project $project) { return view('admin.projects.form', ['project' => $project->load(['services', 'gallery']), 'services' => Service::active()->orderBy('sort_order')->orderBy('title')->get(), 'media' => Media::latest()->limit(100)->get(), 'action' => route('admin.projects.update', $project)]); }

    public function update(Request $request, Project $project)
    {
        $project->update($this->prepare($request, $this->validated($request, $project)));
        $this->syncRelations($request, $project);
        AdminAudit::record('project.updated', 'Updated project '.$project->title, $project);
        return back()->with('success', 'Project and case study updated.');
    }

    public function destroy(Project $project)
    {
        $project->services()->detach();
        $project->gallery()->detach();
        $project->delete();
        AdminAudit::record('project.deleted', 'Removed project '.$project->title, $project);
        return back()->with('success', 'Project removed.');
    }

    private function prepare(Request $request, array $data): array
    {
        $data['slug'] = Str::slug($data['slug']);
        $data['is_featured'] = $request->boolean('is_featured');
        foreach (['full_introduction', 'challenge', 'role', 'approach', 'solution', 'technical_notes'] as $field) $data[$field] = RichText::sanitize($data[$field] ?? null);
        $data['services_provided'] = $this->lines($request->input('services_provided'));
        $data['technologies'] = $this->lines($request->input('technologies'));
        $data['features'] = $this->structuredLines($request->input('features'), ['title', 'description']);
        $data['results'] = $this->structuredLines($request->input('results'), ['label', 'value', 'description']);
        $data['project_status'] = $data['project_status'] ?? ($data['status'] === 'published' ? 'completed' : 'concept');
        if ($data['status'] === 'published' && empty($data['published_at'])) $data['published_at'] = now();
        return $data;
    }

    private function syncRelations(Request $request, Project $project): void
    {
        $project->services()->sync(array_filter((array) $request->input('service_ids', [])));
        $gallery = [];
        foreach ((array) $request->input('gallery', []) as $index => $item) {
            if (empty($item['media_id'])) continue;
            $gallery[(int) $item['media_id']] = ['alt_text' => trim((string) ($item['alt_text'] ?? '')), 'caption' => trim((string) ($item['caption'] ?? '')), 'sort_order' => (int) ($item['sort_order'] ?? $index)];
        }
        $project->gallery()->sync($gallery);
    }

    private function lines(?string $value): array { return collect(preg_split('/\r\n|\r|\n/', (string) $value))->map(fn ($item) => trim($item))->filter()->values()->all(); }

    private function structuredLines(?string $value, array $keys): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))->map(function ($item) use ($keys) { $values = array_pad(explode('|', $item, count($keys)), count($keys), ''); return collect($keys)->combine(array_map('trim', $values))->all(); })->filter(fn ($item) => trim((string) reset($item)) !== '')->values()->all();
    }

    private function validated(Request $request, ?Project $project = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:190'], 'slug' => ['required', 'string', 'max:190', Rule::unique('projects', 'slug')->ignore($project?->id)], 'client_name' => ['nullable', 'string', 'max:190'], 'industry' => ['nullable', 'string', 'max:120'], 'project_type' => ['nullable', 'string', 'max:120'], 'short_description' => ['required', 'string', 'max:2000'],
            'full_introduction' => ['nullable', 'string', 'max:30000'], 'challenge' => ['nullable', 'string', 'max:30000'], 'role' => ['nullable', 'string', 'max:30000'], 'approach' => ['nullable', 'string', 'max:30000'], 'solution' => ['nullable', 'string', 'max:30000'], 'technical_notes' => ['nullable', 'string', 'max:30000'], 'timeline' => ['nullable', 'string', 'max:120'], 'platform' => ['nullable', 'string', 'max:120'], 'status' => ['required', Rule::in(['draft', 'published', 'archived'])], 'project_status' => ['nullable', Rule::in(Project::STATUSES)], 'project_date' => ['nullable', 'date'], 'event_date' => ['nullable', 'date'], 'live_url' => ['nullable', 'url', 'max:500'], 'repository_url' => ['nullable', 'url', 'max:500'], 'featured_image' => ['nullable', 'string', 'max:255'], 'featured_focal_point' => ['nullable', 'string', 'max:40'],
            'media_type' => ['nullable', 'string', 'max:32'], 'creative_category' => ['nullable', 'string', 'max:120'], 'photography_type' => ['nullable', 'string', 'max:190'], 'services_provided' => ['nullable', 'string', 'max:5000'], 'location' => ['nullable', 'string', 'max:190'],
            'technologies' => ['nullable', 'string', 'max:5000'], 'features' => ['nullable', 'string', 'max:10000'], 'results' => ['nullable', 'string', 'max:10000'], 'display_order' => ['required', 'integer', 'min:0'], 'is_featured' => ['nullable', 'boolean'], 'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'], 'primary_keyword' => ['nullable', 'string', 'max:190'], 'secondary_keywords' => ['nullable', 'string', 'max:1000'], 'canonical_url' => ['nullable', 'url', 'max:500'], 'robots' => ['nullable', 'regex:/^(index|noindex),(follow|nofollow)$/'], 'og_title' => ['nullable', 'string', 'max:190'], 'og_description' => ['nullable', 'string', 'max:300'], 'og_image' => ['nullable', 'string', 'max:255'],
            'service_ids' => ['nullable', 'array'], 'service_ids.*' => ['integer', 'exists:services,id'], 'gallery' => ['nullable', 'array'], 'gallery.*.media_id' => ['nullable', 'integer', 'exists:media,id'], 'gallery.*.alt_text' => ['nullable', 'string', 'max:190'], 'gallery.*.caption' => ['nullable', 'string', 'max:190'], 'gallery.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
