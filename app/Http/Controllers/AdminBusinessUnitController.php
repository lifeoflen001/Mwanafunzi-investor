<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use App\Models\Page;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBusinessUnitController extends Controller
{
    public function index()
    {
        return view('admin.business-units.index', [
            'units' => BusinessUnit::orderBy('sort_order')->orderBy('name')->get(),
            'pages' => Page::whereIn('key', BusinessUnit::query()->pluck('slug'))->get()->keyBy('key'),
        ]);
    }

    public function update(Request $request, BusinessUnit $businessUnit)
    {
        $data = $this->validated($request, $businessUnit);
        $data['slug'] = Str::slug($data['slug']);
        $data['is_active'] = $request->boolean('is_active');
        $businessUnit->update($data);

        AdminAudit::record('business_unit.updated', 'Updated business module '.$businessUnit->name, $businessUnit);

        return back()->with('success', 'Business module updated.');
    }

    private function validated(Request $request, BusinessUnit $current): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'tagline' => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:2000'],
            'accent_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $current->slug;
        $data['route_name'] = $current->route_name;

        return $data;
    }
}
