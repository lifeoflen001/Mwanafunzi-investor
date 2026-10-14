<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $titles = [
            'privacy-policy' => 'Privacy Policy — Mwanafunzi Investor',
            'terms' => 'Terms of Use — Mwanafunzi Investor',
            'disclaimer' => 'Educational Disclaimer — Mwanafunzi Investor',
        ];
        $genericTitle = 'Mwanafunzi Investor — Become a Student of Money';

        foreach ($titles as $key => $title) {
            DB::table('pages')
                ->where('key', $key)
                ->where(fn ($query) => $query->whereNull('seo_title')->orWhere('seo_title', $genericTitle))
                ->update(['seo_title' => $title, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        DB::table('pages')
            ->whereIn('key', ['privacy-policy', 'terms', 'disclaimer'])
            ->whereIn('seo_title', [
                'Privacy Policy — Mwanafunzi Investor',
                'Terms of Use — Mwanafunzi Investor',
                'Educational Disclaimer — Mwanafunzi Investor',
            ])
            ->update(['seo_title' => null, 'updated_at' => now()]);
    }
};
