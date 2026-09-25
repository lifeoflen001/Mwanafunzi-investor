<?php

use App\Models\BusinessUnit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('navigation_items', 'menu_group')) {
            Schema::table('navigation_items', function (Blueprint $table) {
                $table->string('menu_group')->default('primary')->after('location');
            });
        }

        $now = now();
        DB::table('navigation_items')->delete();
        $header = BusinessUnit::query()->orderBy('sort_order')->get()->map(fn (BusinessUnit $unit) => [
            'location' => 'header', 'menu_group' => 'primary', 'label' => $unit->name, 'route_name' => $unit->route_name,
            'target' => '_self', 'cta_style' => 'link', 'sort_order' => $unit->sort_order, 'is_visible' => $unit->is_active,
            'created_at' => $now, 'updated_at' => $now,
        ])->all();
        $items = array_merge($header, [
            ['location' => 'header', 'menu_group' => 'primary', 'label' => 'About', 'route_name' => 'about', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => 10, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'footer', 'menu_group' => 'explore', 'label' => 'Learn', 'route_name' => 'learn', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => 1, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'footer', 'menu_group' => 'explore', 'label' => 'Courses', 'route_name' => 'courses', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => 2, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'footer', 'menu_group' => 'explore', 'label' => 'Tools', 'route_name' => 'tools', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => 3, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'footer', 'menu_group' => 'company', 'label' => 'Journal', 'route_name' => 'journal', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => 1, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'footer', 'menu_group' => 'company', 'label' => 'About', 'route_name' => 'about', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => 2, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'footer', 'menu_group' => 'company', 'label' => 'Contact', 'route_name' => 'contact', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => 3, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'footer', 'menu_group' => 'legal', 'label' => 'Privacy', 'route_name' => 'legal', 'url' => 'privacy-policy', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => 1, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'footer', 'menu_group' => 'legal', 'label' => 'Terms', 'route_name' => 'legal', 'url' => 'terms', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => 2, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'footer', 'menu_group' => 'legal', 'label' => 'Risk disclosure', 'route_name' => 'legal', 'url' => 'risk-disclosure', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => 3, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('navigation_items')->insert(array_map(fn (array $item) => array_merge(['url' => null], $item), $items));
    }

    public function down(): void
    {
        Schema::table('navigation_items', function (Blueprint $table) { $table->dropColumn('menu_group'); });
    }
};
