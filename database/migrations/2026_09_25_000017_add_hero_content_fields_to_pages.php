<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('hero_highlight')->nullable()->after('hero_title');
            $table->string('hero_primary_label')->nullable()->after('hero_image');
            $table->string('hero_primary_url')->nullable()->after('hero_primary_label');
            $table->string('hero_secondary_label')->nullable()->after('hero_primary_url');
            $table->string('hero_secondary_url')->nullable()->after('hero_secondary_label');
            $table->string('hero_note')->nullable()->after('hero_secondary_url');
            $table->text('hero_aside')->nullable()->after('hero_note');
            $table->string('hero_aside_index', 50)->nullable()->after('hero_aside');
        });

        $now = now();
        DB::table('pages')->where('key', 'home')->update([
            'hero_eyebrow' => 'Financial education for the long game',
            'hero_title' => 'Become a',
            'hero_highlight' => 'Student of Money.',
            'hero_summary' => 'Systematic Trading. Probability. Risk. Discipline. Learn to understand markets, develop mechanical trading rules and protect your capital without the hype.',
            'hero_primary_label' => 'Start Learning',
            'hero_primary_url' => '#courses',
            'hero_secondary_label' => 'Explore Trading Tools',
            'hero_secondary_url' => '#tools',
            'hero_note' => 'Forex Education • Risk Management • Trading Systems • Portfolio Thinking',
            'hero_aside' => "For the person who wants\nto understand, not predict.",
            'hero_aside_index' => '01 / 04',
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn([
                'hero_highlight', 'hero_primary_label', 'hero_primary_url',
                'hero_secondary_label', 'hero_secondary_url', 'hero_note',
                'hero_aside', 'hero_aside_index',
            ]);
        });
    }
};
