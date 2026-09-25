<?php

namespace App\Http\Controllers;

use App\Models\NavigationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use App\Support\AdminAudit;

class AdminNavigationController extends Controller
{
    public function index()
    {
        return view('admin.navigation.index', ['items' => NavigationItem::with('parent')->orderBy('location')->orderBy('menu_group')->orderBy('sort_order')->paginate(30), 'parentOptions' => NavigationItem::whereNull('parent_id')->orderBy('label')->get(), 'routes' => collect(Route::getRoutes()->getRoutesByName())->keys()->filter(fn (string $name) => ! str_starts_with($name, 'admin.'))->sort()->values()]);
    }

    public function store(Request $request)
    {
        $item = NavigationItem::create($this->validated($request));
        AdminAudit::record('navigation.created', 'Added navigation item '.$item->label, $item);
        return back()->with('success', 'Navigation item added.');
    }

    public function update(Request $request, NavigationItem $navigationItem)
    {
        $navigationItem->update($this->validated($request, $navigationItem));
        AdminAudit::record('navigation.updated', 'Updated navigation item '.$navigationItem->label, $navigationItem);
        return back()->with('success', 'Navigation item updated.');
    }

    public function destroy(NavigationItem $navigationItem)
    {
        $navigationItem->delete();
        AdminAudit::record('navigation.deleted', 'Removed navigation item '.$navigationItem->label, $navigationItem);
        return back()->with('success', 'Navigation item removed.');
    }

    private function validated(Request $request, ?NavigationItem $current = null): array
    {
        $data = $request->validate([
            'location' => ['required', 'in:header,footer'],
            'menu_group' => ['required', 'alpha_dash', 'max:80'],
            'label' => ['required', 'string', 'max:120'],
            'parent_id' => ['nullable', 'integer', 'exists:navigation_items,id'],
            'route_name' => ['nullable', 'string', 'max:190'],
            'url' => ['nullable', 'string', 'max:500'],
            'target' => ['required', 'in:_self,_blank'],
            'cta_style' => ['required', 'in:link,button'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_visible' => ['nullable', 'boolean'],
        ]);
        $data['is_visible'] = $request->boolean('is_visible');
        if (! empty($data['route_name']) && ! Route::has($data['route_name'])) {
            throw ValidationException::withMessages(['route_name' => 'Choose a valid public route or leave this blank for an external URL.']);
        }
        if (! empty($data['url']) && ! preg_match('/^(https?:\/\/|\/|#)/i', $data['url'])) {
            throw ValidationException::withMessages(['url' => 'Links must use https://, a local path or a page anchor.']);
        }
        if (! empty($data['parent_id'])) {
            $parent = NavigationItem::findOrFail($data['parent_id']);
            abort_unless($parent->location === $data['location'] && $parent->parent_id === null && $parent->id !== $current?->id, 422, 'Navigation nesting is limited to one safe level in the same menu.');
        }
        return $data;
    }
}
