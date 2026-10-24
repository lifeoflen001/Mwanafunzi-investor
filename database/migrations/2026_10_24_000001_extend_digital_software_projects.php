<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            if (! Schema::hasColumn('projects', 'project_status')) {
                $table->string('project_status', 50)->nullable()->after('status');
            }

            if (! Schema::hasColumn('projects', 'technical_notes')) {
                $table->longText('technical_notes')->nullable()->after('solution');
            }
        });

        Schema::table('projects', function (Blueprint $table): void {
            $table->index(['industry', 'project_status', 'status', 'is_featured'], 'projects_digital_catalogue_index');
        });

        DB::table('pages')->where('key', 'development')->update([
            'hero_title' => 'Websites and software built around real business operations.',
            'hero_highlight' => null,
            'hero_summary' => 'Professional websites, custom business systems, portals, dashboards and integrations for hospitality, tourism, education and growing organisations.',
            'hero_primary_label' => 'View completed projects',
            'hero_primary_url' => '/digital-software/projects',
            'hero_secondary_label' => 'Discuss a project',
            'hero_secondary_url' => '/digital-software/contact',
            'seo_title' => 'Digital Software — Mwanafunzi Investor',
            'seo_description' => 'Websites, business systems, dashboards and integrations built around real workflows for hospitality, tourism, education and growing organisations.',
            'updated_at' => now(),
        ]);

        DB::table('page_sections')->where('page_id', DB::table('pages')->where('key', 'development')->value('id'))->where('key', 'capabilities')->update([
            'heading' => 'Capabilities shaped around the work.',
            'body' => 'We design and develop websites, software, portals, commerce experiences and integrations around the people and workflows that use them.',
            'payload' => json_encode(['eyebrow' => 'Capabilities', 'cards' => [
                ['index' => '01', 'title' => 'Websites and digital experiences', 'body' => 'Purposeful public-facing websites with strong structure, responsive layouts and content that helps people decide.', 'tags' => 'Strategy · UX · Build'],
                ['index' => '02', 'title' => 'Business software and dashboards', 'body' => 'Custom web applications, dashboards and internal tools that turn repeated work into a clearer system.', 'tags' => 'Laravel · Interfaces · Workflows'],
                ['index' => '03', 'title' => 'Client portals and commerce', 'body' => 'Secure workspaces for accounts, purchases, downloads, learning and everyday customer relationships.', 'tags' => 'Accounts · Commerce · Access'],
                ['index' => '04', 'title' => 'APIs and integrations', 'body' => 'Payment, communication, content and business integrations that connect the important parts of an operation.', 'tags' => 'Payments · APIs · Automation'],
                ['index' => '05', 'title' => 'Maintenance and technical support', 'body' => 'Measured improvements, maintenance and support so the system remains useful after the first release.', 'tags' => 'Support · Iteration · Care'],
            ]], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropIndex('projects_digital_catalogue_index');

            $columns = [];
            if (Schema::hasColumn('projects', 'technical_notes')) $columns[] = 'technical_notes';
            if (Schema::hasColumn('projects', 'project_status')) $columns[] = 'project_status';
            if ($columns) $table->dropColumn($columns);
        });
    }
};
