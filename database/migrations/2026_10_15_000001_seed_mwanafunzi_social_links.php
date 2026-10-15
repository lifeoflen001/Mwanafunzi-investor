<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $links = [
        ['label' => 'Instagram', 'url' => 'https://www.instagram.com/mwanafunziinvestor', 'sort_order' => 1],
        ['label' => 'Facebook', 'url' => 'https://www.facebook.com/mwanafunziinvestor', 'sort_order' => 2],
        ['label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/mwanafunziinvestor', 'sort_order' => 3],
        ['label' => 'TikTok', 'url' => 'https://www.tiktok.com/@mwanafunziinvestor', 'sort_order' => 4],
        ['label' => 'YouTube', 'url' => 'https://www.youtube.com/@mwanafunziinvestor', 'sort_order' => 5],
        ['label' => 'X', 'url' => 'https://x.com/mwanafunziinvestor', 'sort_order' => 6],
    ];

    public function up(): void
    {
        $now = now();

        foreach ($this->links as $link) {
            if (DB::table('social_links')->where('url', $link['url'])->exists()) continue;

            DB::table('social_links')->insert([
                ...$link,
                'is_visible' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('social_links')->whereIn('url', array_column($this->links, 'url'))->delete();
    }
};
