<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        foreach ([
            ['label' => 'Services', 'route_name' => 'services', 'sort_order' => 5],
            ['label' => 'Projects', 'route_name' => 'projects', 'sort_order' => 6],
        ] as $item) {
            DB::table('navigation_items')->updateOrInsert(
                ['location' => 'header', 'route_name' => $item['route_name'], 'parent_id' => null],
                array_merge($item, ['url' => null, 'target' => '_self', 'cta_style' => 'link', 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now])
            );
        }
    }

    public function down(): void
    {
        DB::table('navigation_items')->whereIn('route_name', ['services', 'projects'])->where('location', 'header')->delete();
    }
};
