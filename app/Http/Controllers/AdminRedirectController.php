<?php

namespace App\Http\Controllers;

use App\Models\Redirect;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminRedirectController extends Controller
{
    public function index()
    {
        return view('admin.redirects.index', ['redirects' => Redirect::latest()->paginate(30)]);
    }

    public function store(Request $request)
    {
        $redirect = Redirect::create($this->validated($request));
        AdminAudit::record('redirect.created', 'Created redirect '.$redirect->source_path, $redirect);
        return back()->with('success', 'Redirect added.');
    }

    public function update(Request $request, Redirect $redirect)
    {
        $redirect->update($this->validated($request, $redirect));
        AdminAudit::record('redirect.updated', 'Updated redirect '.$redirect->source_path, $redirect);
        return back()->with('success', 'Redirect updated.');
    }

    public function destroy(Redirect $redirect)
    {
        $redirect->delete();
        AdminAudit::record('redirect.deleted', 'Removed redirect '.$redirect->source_path, $redirect);
        return back()->with('success', 'Redirect removed.');
    }

    private function validated(Request $request, ?Redirect $redirect = null): array
    {
        $data = $request->validate([
            'source_path' => ['required', 'string', 'max:500', Rule::unique('redirects', 'source_path')->ignore($redirect?->id)],
            'destination_path' => ['required', 'string', 'max:500'],
            'status_code' => ['required', 'in:301,302,307,308'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);
        $data['source_path'] = '/'.ltrim(trim($data['source_path']), '/');
        $data['destination_path'] = trim($data['destination_path']);
        abort_unless(str_starts_with($data['destination_path'], '/') || preg_match('/^https?:\/\//i', $data['destination_path']), 422, 'Redirect destinations must be local paths or https URLs.');
        $data['is_enabled'] = $request->boolean('is_enabled');
        return $data;
    }
}
