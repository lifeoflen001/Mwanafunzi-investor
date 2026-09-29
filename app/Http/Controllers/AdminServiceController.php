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

class AdminServiceController extends Controller
{
    public function index() { return view('admin.services.index', ['services' => Service::withCount('projects')->orderBy('sort_order')->orderBy('title')->paginate(20)]); }

    public function create() { return view('admin.services.form', ['service' => new Service(['is_active' => true, 'pricing_type' => 'custom_quote', 'currency' => 'TZS']), 'projects' => Project::orderBy('title')->get(), 'media' => Media::latest()->limit(80)->get(), 'action' => route('admin.services.store')]); }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $service = Service::create($this->prepare($request, $data));
        $service->projects()->sync(array_filter((array) $request->input('project_ids', [])));
        AdminAudit::record('service.created', 'Created service '.$service->title, $service);
        return to_route('admin.services.edit', $service)->with('success', 'Service created.');
    }

    public function edit(Service $service) { return view('admin.services.form', ['service' => $service->load('projects'), 'projects' => Project::orderBy('title')->get(), 'media' => Media::latest()->limit(80)->get(), 'action' => route('admin.services.update', $service)]); }

    public function update(Request $request, Service $service)
    {
        $service->update($this->prepare($request, $this->validated($request, $service)));
        $service->projects()->sync(array_filter((array) $request->input('project_ids', [])));
        AdminAudit::record('service.updated', 'Updated service '.$service->title, $service);
        return back()->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->projects()->detach();
        $service->delete();
        AdminAudit::record('service.deleted', 'Removed service '.$service->title, $service);
        return back()->with('success', 'Service removed.');
    }

    private function prepare(Request $request, array $data): array
    {
        $data['slug'] = Str::slug($data['slug']);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['included_features'] = $this->lines($request->input('included_features'));
        $data['detailed_description'] = RichText::sanitize($data['detailed_description'] ?? null);
        return $data;
    }

    private function lines(?string $value): array { return collect(preg_split('/\r\n|\r|\n/', (string) $value))->map(fn ($item) => trim($item))->filter()->values()->all(); }

    private function validated(Request $request, ?Service $service = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:190'], 'slug' => ['required', 'string', 'max:190', Rule::unique('services', 'slug')->ignore($service?->id)],
            'short_description' => ['required', 'string', 'max:2000'], 'detailed_description' => ['nullable', 'string', 'max:30000'], 'icon' => ['nullable', 'string', 'max:80'], 'featured_image' => ['nullable', 'string', 'max:255'],
            'starting_price' => ['nullable', 'numeric', 'min:0'], 'pricing_label' => ['nullable', 'string', 'max:120'], 'currency' => ['nullable', 'string', 'size:3'], 'pricing_type' => ['required', Rule::in(['fixed', 'starting_from', 'hourly', 'project_based', 'custom_quote'])], 'delivery_estimate' => ['nullable', 'string', 'max:120'], 'included_features' => ['nullable', 'string', 'max:10000'],
            'cta_label' => ['nullable', 'string', 'max:120'], 'cta_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|mailto:|\/|#)/i'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_featured' => ['nullable', 'boolean'], 'is_active' => ['nullable', 'boolean'],
            'project_ids' => ['nullable', 'array'], 'project_ids.*' => ['integer', 'exists:projects,id'],
            'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'], 'canonical_url' => ['nullable', 'url', 'max:500'], 'robots' => ['nullable', 'regex:/^(index|noindex),(follow|nofollow)$/'], 'og_image' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
