<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Project;
use App\Models\Testimonial;
use App\Support\AdminAudit;
use Illuminate\Http\Request;

class AdminTestimonialController extends Controller
{
    public function index() { return view('admin.testimonials.index', ['testimonials' => Testimonial::with(['project'])->orderBy('sort_order')->latest('testimonial_date')->paginate(20)]); }

    public function create() { return view('admin.testimonials.form', ['testimonial' => new Testimonial(['is_published' => false]), 'projects' => Project::orderBy('title')->get(), 'media' => Media::latest()->limit(80)->get(), 'action' => route('admin.testimonials.store')]); }

    public function store(Request $request) { $testimonial = Testimonial::create($this->validated($request)); AdminAudit::record('testimonial.created', 'Created testimonial for '.$testimonial->client_name, $testimonial); return to_route('admin.testimonials.edit', $testimonial)->with('success', 'Testimonial created.'); }

    public function edit(Testimonial $testimonial) { return view('admin.testimonials.form', ['testimonial' => $testimonial, 'projects' => Project::orderBy('title')->get(), 'media' => Media::latest()->limit(80)->get(), 'action' => route('admin.testimonials.update', $testimonial)]); }

    public function update(Request $request, Testimonial $testimonial) { $testimonial->update($this->validated($request)); AdminAudit::record('testimonial.updated', 'Updated testimonial for '.$testimonial->client_name, $testimonial); return back()->with('success', 'Testimonial updated.'); }

    public function destroy(Testimonial $testimonial) { $testimonial->delete(); AdminAudit::record('testimonial.deleted', 'Removed testimonial for '.$testimonial->client_name, $testimonial); return back()->with('success', 'Testimonial removed.'); }

    private function validated(Request $request): array
    {
        $data = $request->validate(['client_name' => ['required', 'string', 'max:190'], 'client_role' => ['nullable', 'string', 'max:190'], 'company' => ['nullable', 'string', 'max:190'], 'testimonial' => ['required', 'string', 'max:10000'], 'client_photo' => ['nullable', 'string', 'max:255'], 'company_logo' => ['nullable', 'string', 'max:255'], 'project_id' => ['nullable', 'integer', 'exists:projects,id'], 'rating' => ['nullable', 'integer', 'min:1', 'max:5'], 'source_url' => ['nullable', 'url', 'max:500'], 'anonymous_display' => ['nullable', 'boolean'], 'initials_only' => ['nullable', 'boolean'], 'hide_company' => ['nullable', 'boolean'], 'is_featured' => ['nullable', 'boolean'], 'is_published' => ['nullable', 'boolean'], 'sort_order' => ['required', 'integer', 'min:0'], 'testimonial_date' => ['nullable', 'date']]);
        foreach (['anonymous_display', 'initials_only', 'hide_company', 'is_featured', 'is_published'] as $field) $data[$field] = $request->boolean($field);
        return $data;
    }
}
