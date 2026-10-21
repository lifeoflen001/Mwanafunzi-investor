<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use App\Models\Project;
use App\Models\Service;
use App\Models\StudioInquiry;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudioInquiryController extends Controller
{
    public function create()
    {
        $studio = BusinessUnit::query()->active()->where('slug', 'studio')->firstOrFail();
        $services = Service::active()->where('business_unit_id', $studio->id)->orderBy('sort_order')->orderBy('title')->get();

        return view('public.studio.book', [
            'studio' => $studio,
            'services' => $services,
            'selectedService' => $services->firstWhere('slug', request('service')),
            'selectedProject' => Project::published()->where('slug', request('project'))->whereHas('services.businessUnit', fn ($query) => $query->where('slug', 'studio'))->first(),
        ]);
    }

    public function store(Request $request)
    {
        $studio = BusinessUnit::query()->active()->where('slug', 'studio')->firstOrFail();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'service_id' => ['nullable', 'integer', Rule::exists('services', 'id')->where(fn ($query) => $query->where('business_unit_id', $studio->id)->where('is_active', true))],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'preferred_time' => ['nullable', 'string', 'max:40'],
            'location' => ['nullable', 'string', 'max:190'],
            'duration' => ['nullable', 'string', 'max:120'],
            'budget_range' => ['nullable', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:20', 'max:10000'],
            'special_requirements' => ['nullable', 'string', 'max:5000'],
            'reference_files' => ['nullable', 'array', 'max:5'],
            'reference_files.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,mp4,mov', 'max:16384'],
            'consent' => ['accepted'],
            'website' => ['prohibited'],
            'recaptcha_token' => Recaptcha::rules('studio_booking'),
        ]);

        if (! empty($data['project_id']) && ! Project::published()->whereKey($data['project_id'])->whereHas('services.businessUnit', fn ($query) => $query->where('slug', 'studio'))->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['project_id' => 'Please choose a published Creative Studio project.']);
        }

        $referenceBytes = collect($request->file('reference_files', []))->sum(fn ($file) => (int) $file->getSize());
        if ($referenceBytes > config('security.studio_max_reference_bytes')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'reference_files' => 'Please keep all reference files under 32 MB combined.',
            ]);
        }

        $reference = 'STU-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        while (StudioInquiry::where('reference', $reference)->exists()) {
            $reference = 'STU-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        }

        $files = collect($request->file('reference_files', []))->map(function ($file) use ($reference) {
            $path = $file->store('studio-inquiries/'.$reference, 'local');

            return [
                'disk' => 'local',
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
        })->values()->all();

        StudioInquiry::create([
            ...$data,
            'reference' => $reference,
            'user_id' => $request->user()?->id,
            'business_unit_id' => $studio->id,
            'project_id' => $data['project_id'] ?? null,
            'reference_files' => $files,
            'status' => 'new',
        ]);

        return to_route('creative-studio.book')->with('success', 'Your studio enquiry has been received. Keep your reference '.$reference.' for follow-up.');
    }
}
