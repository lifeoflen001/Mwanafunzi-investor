<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('team_members')->where('slug', 'lenkai-mollel')->update([
            'role' => 'Software Developer & Video Editor',
            'department' => 'Digital Software & Creative Studio',
            'short_intro' => 'Builds thoughtful digital systems and shapes clear visual stories through editing.',
            'focus' => 'Turning complex ideas into reliable software and purposeful visual stories.',
            'expertise' => json_encode(['Software development', 'Web applications and systems', 'Video editing and post-production']),
            'bio' => 'Lenkai works across software development and post-production, bringing structure, care and a strong editorial eye to every project. He builds useful digital experiences and edits video that gives people and ideas room to be understood.',
            'sort_order' => 2,
        ]);

        DB::table('team_members')->where('slug', 'david-lyengi')->update([
            'role' => 'Senior Financial Advisor, Forex Trader & Photographer',
            'department' => 'Financial Academy & Creative Studio',
            'short_intro' => 'Guides disciplined market thinking while creating photographs with clarity and intent.',
            'focus' => 'Helping people make better decisions in uncertain markets and meaningful images.',
            'expertise' => json_encode(['Financial education and advisory', 'Forex trading and risk discipline', 'Photography and visual storytelling']),
            'bio' => 'David brings together senior financial advisory, practical forex trading and photography. His work is grounded in disciplined decision-making: understand the context, respect the risk and communicate with clarity.',
            'sort_order' => 1,
        ]);
    }

    public function down(): void
    {
        DB::table('team_members')->where('slug', 'lenkai-mollel')->update(['role' => null, 'department' => null, 'short_intro' => 'Part of the team building thoughtful work across the Mwanafunzi Investor platform.', 'focus' => null, 'expertise' => null, 'bio' => null, 'sort_order' => 1]);
        DB::table('team_members')->where('slug', 'david-lyengi')->update(['role' => null, 'department' => null, 'short_intro' => 'Part of the team contributing care, clarity and consistency to the work.', 'focus' => null, 'expertise' => null, 'bio' => null, 'sort_order' => 2]);
    }
};
