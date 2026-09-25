<?php

namespace App\Http\Controllers;

use App\Models\NavigationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class AdminNavigationController extends Controller
{
    public function index()
    {
        return view('admin.navigation.index', ['items' => NavigationItem::with('parent')->orderBy('location')->orderBy('menu_group')->orderBy('sort_order')->paginate(30), 'routes' => collect(Route::getRoutes()->getRoutesByName())->keys()->filter(fn (string $name) => ! str_starts_with($name, 'admin.'))->sort()->values()]);
    }

    public function store(Request $request)
    {
        NavigationItem::create($this->validated($request));
        return back()->with('success', 'Navigation item added.');
    }

    public function update(Request $request, NavigationItem $navigationItem)
    {
        $navigationItem->update($this->validated($request));
        return back()->with('success', 'Navigation item updated.');
    }

    public function destroy(NavigationItem $navigationItem)
    {
        $navigationItem->delete();
        return back()->with('success', 'Navigation item removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'location' => ['required', 'in:header,footer'],
            'menu_group' => ['required', 'alpha_dash', 'max:80'],
            'label' => ['required', 'string', 'max:120'],
            'route_name' => ['nullable', 'string', 'max:190'],
            'url' => ['nullable', 'string', 'max:500'],
            'target' => ['required', 'in:_self,_blank'],
            'cta_style' => ['required', 'in:link,button'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_visible' => ['nullable', 'boolean'],
        ]);
    }
}
