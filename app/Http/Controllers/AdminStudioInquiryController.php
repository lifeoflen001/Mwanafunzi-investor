<?php

namespace App\Http\Controllers;

use App\Models\StudioInquiry;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminStudioInquiryController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'status' => ['nullable', Rule::in(StudioInquiry::STATUSES)]]);
        $inquiries = StudioInquiry::studio()->with(['service', 'project'])->when($data['q'] ?? null, function ($query, $term) {
            $query->where(fn ($search) => $search->where('reference', 'like', '%'.$term.'%')->orWhere('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%'));
        })->when($data['status'] ?? null, fn ($query, $status) => $query->where('status', $status))->latest()->paginate(20)->withQueryString();

        return view('admin.studio.inquiries.index', compact('inquiries'));
    }

    public function show(StudioInquiry $inquiry)
    {
        abort_unless($inquiry->businessUnit?->slug === 'studio', 404);
        $inquiry->load(['service', 'project', 'user']);
        $statuses = StudioInquiry::STATUSES;

        return view('admin.studio.inquiries.show', compact('inquiry', 'statuses'));
    }

    public function update(Request $request, StudioInquiry $inquiry)
    {
        abort_unless($inquiry->businessUnit?->slug === 'studio', 404);
        $data = $request->validate([
            'status' => ['required', Rule::in(StudioInquiry::STATUSES)],
            'admin_notes' => ['nullable', 'string', 'max:10000'],
        ]);
        $timestamps = [];
        if ($data['status'] !== $inquiry->status && $inquiry->reviewed_at === null) $timestamps['reviewed_at'] = now();
        if ($data['status'] === 'quoted' && $inquiry->quoted_at === null) $timestamps['quoted_at'] = now();
        if (in_array($data['status'], ['accepted', 'scheduled'], true) && $inquiry->confirmed_at === null) $timestamps['confirmed_at'] = now();
        if ($data['status'] === 'completed' && $inquiry->completed_at === null) $timestamps['completed_at'] = now();
        $inquiry->update([...$data, ...$timestamps]);
        AdminAudit::record('studio.inquiry.updated', 'Updated studio enquiry '.$inquiry->reference.' to '.$inquiry->status, $inquiry);

        return back()->with('success', 'Studio enquiry updated.');
    }

    public function downloadReference(StudioInquiry $inquiry, int $file)
    {
        abort_unless($inquiry->businessUnit?->slug === 'studio', 404);
        $record = collect($inquiry->reference_files ?? [])->get($file);
        abort_unless($record && isset($record['disk'], $record['path']), 404);
        abort_unless(Storage::disk($record['disk'])->exists($record['path']), 404);

        return Storage::disk($record['disk'])->download($record['path'], $record['name'] ?? basename($record['path']));
    }
}
