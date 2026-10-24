<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $developmentId = DB::table('business_units')->where('slug', 'development')->value('id');

        if (! $developmentId) {
            $developmentId = DB::table('business_units')->insertGetId([
                'slug' => 'development',
                'name' => 'Digital Software',
                'tagline' => 'Websites and software built for real work.',
                'description' => 'Websites, Laravel systems, dashboards and integrations for organisations that need dependable digital tools.',
                'accent_color' => '#56736d',
                'route_name' => 'digital-systems',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $now = now();

        DB::table('services')->updateOrInsert(
            ['slug' => 'digital-software-platforms'],
            [
                'title' => 'Digital software platforms',
                'short_description' => 'Websites, dashboards and operational software shaped around real workflows.',
                'detailed_description' => 'Digital software work documented in the Mwanafunzi Investor project catalogue, including public websites, business systems, portals, dashboards and integrations.',
                'pricing_type' => 'custom_quote',
                'pricing_label' => 'Custom quote',
                'included_features' => json_encode(['Websites and digital experiences', 'Business software and dashboards', 'Client portals and commerce', 'APIs and integrations', 'Maintenance and technical support'], JSON_UNESCAPED_UNICODE),
                'cta_label' => 'Discuss a project',
                'cta_url' => '/digital-software/contact',
                'sort_order' => 10,
                'is_featured' => false,
                'is_active' => true,
                'seo_title' => 'Digital software platforms — Mwanafunzi Investor',
                'seo_description' => 'Documented websites, software platforms, dashboards and operational systems from Mwanafunzi Investor.',
                'robots' => 'index,follow',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $serviceId = DB::table('services')->where('slug', 'digital-software-platforms')->value('id');

        $projects = [
            [
                'title' => 'Lodgix',
                'slug' => 'lodgix',
                'industry' => 'Hospitality & Tourism',
                'project_type' => 'Hotel operations platform',
                'short_description' => 'A Laravel-based hotel operations platform for reservations, rooms, guests, staff access, housekeeping, maintenance, payments, notifications and operational reporting.',
                'full_introduction' => 'Lodgix is documented as a Laravel-based hotel operations platform for managing reservations, rooms, guests, staff access, housekeeping, maintenance, payments, notifications and operational reporting in one workspace.',
                'solution' => 'The documented product includes reservation lifecycle management, check-in, check-out, cancellation and no-show workflows; room planning and availability; staff roles and permissions; maintenance and task tracking; payment records, exports, webhook handling, notification preferences and reports.',
                'technical_notes' => 'Evidence source: the Lodgix repository README and project documentation. The documented stack is PHP 8.2+, Laravel 12, Laravel Jetstream, Blade, JavaScript, Tailwind CSS, Vite, MySQL for production and SQLite for local development and tests.',
                'technologies' => json_encode(['PHP 8.2+', 'Laravel 12', 'Laravel Jetstream', 'Blade', 'JavaScript', 'Tailwind CSS', 'Vite', 'MySQL', 'SQLite', 'PHPUnit'], JSON_UNESCAPED_UNICODE),
                'features' => json_encode([
                    ['title' => 'Reservation lifecycle', 'description' => 'Check-in, check-out, cancellation and no-show workflows.'],
                    ['title' => 'Room operations', 'description' => 'Room planning, room categories, room types, availability and housekeeping status.'],
                    ['title' => 'Staff access', 'description' => 'Staff profiles, role-based access, permissions and two-factor authentication.'],
                    ['title' => 'Maintenance and tasks', 'description' => 'Maintenance and task tracking connected to rooms and reservations.'],
                    ['title' => 'Payments and reporting', 'description' => 'Payment records, exports, webhooks, notification preferences and reports.'],
                ], JSON_UNESCAPED_UNICODE),
                'project_status' => 'active_development',
                'live_url' => 'https://lodgix.up.railway.app',
                'display_order' => 10,
                'seo_title' => 'Lodgix — Hotel operations platform — Mwanafunzi Investor',
                'seo_description' => 'Documented Lodgix hotel operations platform for reservations, rooms, teams, payments and operational reporting.',
            ],
            [
                'title' => 'Tour Operator Management Platform',
                'slug' => 'tour-operator-management-platform',
                'client_name' => 'Generations Safaris',
                'industry' => 'Hospitality & Tourism',
                'project_type' => 'Tour operations workspace',
                'short_description' => 'A Laravel 12 tour operations workspace for inquiry CRM, itinerary versioning, costing, quotation flow, audit history and booking conversion.',
                'full_introduction' => 'The Generations Safaris repository documents a Laravel 12 tour operations workspace for a Tanzanian tour company. Its first documented vertical slice connects inquiry CRM, itinerary versioning, costing, quotation flow, audit history and conversion to a central Booking File.',
                'solution' => 'The documented workflow is Inquiry → Itinerary → Quotation version → Customer approval → Booking conversion, with supplier, accommodation, activity, passenger, payment, readiness, operations and audit records attached to the booking domain.',
                'technical_notes' => 'Evidence source: the Generations Safaris repository README and module documentation, including inquiry, itinerary/quotation, booking-management, operations and public-tourism website records. The repository is a Laravel 12 application and its performance documentation records PHP 8.2, Vite and a MySQL-compatible database.',
                'technologies' => json_encode(['PHP 8.2', 'Laravel 12', 'Livewire', 'Laravel Jetstream', 'Laravel Reverb', 'Vite', 'MySQL-compatible database', 'PHPUnit', 'DomPDF'], JSON_UNESCAPED_UNICODE),
                'features' => json_encode([
                    ['title' => 'Inquiry CRM', 'description' => 'Customer, owner, pipeline stage and follow-up records.'],
                    ['title' => 'Itineraries and quotations', 'description' => 'Versioned itineraries and quotation versions with server-side costing.'],
                    ['title' => 'Booking conversion', 'description' => 'Customer approval and idempotent conversion to a central Booking File.'],
                    ['title' => 'Supplier and passenger records', 'description' => 'Supplier, accommodation, passenger, payment and readiness records.'],
                    ['title' => 'Trip operations', 'description' => 'Operational plans, resource allocation, dispatch, incidents and closure controls.'],
                    ['title' => 'Public tourism routes', 'description' => 'Itineraries, destinations, activities, accommodations, proposals, blog and FAQs.'],
                ], JSON_UNESCAPED_UNICODE),
                'project_status' => 'active_development',
                'display_order' => 20,
                'seo_title' => 'Tour Operator Management Platform — Mwanafunzi Investor',
                'seo_description' => 'Documented tour operations workspace for inquiries, itineraries, quotations, bookings and operational handoffs.',
            ],
            [
                'title' => 'Schoolix',
                'slug' => 'schoolix',
                'industry' => 'Education',
                'project_type' => 'School management platform',
                'short_description' => 'A Django foundation for secure, multi-organization and multi-school school management with academic, operational, portal, reporting and API domains.',
                'full_introduction' => 'Schoolix is documented as a Django foundation for a secure, multi-organization and multi-school school management platform. Its current evidence class is Pre-Production MVP, with live infrastructure and release gates explicitly remaining outstanding.',
                'solution' => 'The documented platform foundation includes identity and tenant administration, academic configuration, admissions, student records, staff and teacher workflows, timetables, attendance, examinations, results, report cards, finance, communications, learner and guardian portals, library, transport, hostel/boarding, inventory/assets/procurement and centralized reporting.',
                'technical_notes' => 'Evidence source: Schoolix README and SYSTEM_DOCUMENTATION.md. The documented production-shaped stack is Python 3.14, Django 6.0, MySQL 8.x, Django ORM, server-rendered Django templates and Django tests. SQLite is documented only for lightweight local/testing use; MySQL 8, Redis/Celery, staging and authenticated browser/UAT validation remain outstanding.',
                'technologies' => json_encode(['Python 3.14', 'Django 6.0', 'Django ORM', 'MySQL 8.x', 'Django templates', 'Celery', 'Redis', 'Django TestCase', 'REST API', 'OpenAPI'], JSON_UNESCAPED_UNICODE),
                'features' => json_encode([
                    ['title' => 'School tenancy', 'description' => 'Organization → School → Campus tenancy and contextual memberships.'],
                    ['title' => 'Admissions and people', 'description' => 'Admissions, student records, staff/HR and teacher assignments.'],
                    ['title' => 'Academic operations', 'description' => 'Academic configuration, timetables, attendance, examinations, results and report cards.'],
                    ['title' => 'Finance and portals', 'description' => 'Finance, communications, learner/guardian portals and teacher portal resources.'],
                    ['title' => 'School services', 'description' => 'Library, transport, hostel/boarding and inventory/assets/procurement foundations.'],
                    ['title' => 'Reporting and documents', 'description' => 'Central reporting, saved reports, controlled document templates and secure exports.'],
                    ['title' => 'API boundary', 'description' => 'Versioned school-bound REST API and provider-neutral integration boundaries.'],
                ], JSON_UNESCAPED_UNICODE),
                'project_status' => 'active_development',
                'display_order' => 30,
                'seo_title' => 'Schoolix — School management platform — Mwanafunzi Investor',
                'seo_description' => 'Documented Schoolix school management foundation for multi-school administration, academics, portals and reporting.',
            ],
            [
                'title' => 'NIDEO / Stockflow',
                'slug' => 'nideo-stockflow',
                'industry' => 'Inventory & Operations',
                'project_type' => 'Inventory and procurement platform',
                'short_description' => 'A Laravel and React/TypeScript inventory application with tenant-aware stock, warehouse, supplier, procurement, reporting and API workflows.',
                'full_introduction' => 'StockFlow is documented as a Laravel 12 application with a React/TypeScript frontend. Laravel serves the REST API and the compiled single-page application, with production deployment documentation for a PostgreSQL service.',
                'solution' => 'The repository and architecture audit document organization-scoped inventory, warehouses, stock balances and movements, suppliers, purchase orders, goods receipt, reporting, backups, permissions, audit controls and API workflows.',
                'technical_notes' => 'Evidence source: the StockFlow README, architecture audit, models, API controllers and feature tests. The audit records a credible MVP foundation, with remaining recommendations for production object storage, database-specific integration jobs, browser workflow tests and query observability.',
                'technologies' => json_encode(['PHP 8.2+', 'Laravel 12', 'React', 'TypeScript', 'Vite', 'REST API', 'PostgreSQL'], JSON_UNESCAPED_UNICODE),
                'features' => json_encode([
                    ['title' => 'Tenant-scoped access', 'description' => 'Organization and tenant-scoped access controls.'],
                    ['title' => 'Stock and warehouses', 'description' => 'Stock ledger, balances, movements, warehouses and locations.'],
                    ['title' => 'Procurement', 'description' => 'Suppliers, procurement requests, purchase orders and goods receipt.'],
                    ['title' => 'Inventory reporting', 'description' => 'Inventory valuation, reports, imports and exports.'],
                    ['title' => 'Operational controls', 'description' => 'Role and permission isolation, audit records and operational notifications.'],
                    ['title' => 'Application interface', 'description' => 'REST API and compiled single-page frontend.'],
                ], JSON_UNESCAPED_UNICODE),
                'project_status' => 'active_development',
                'display_order' => 40,
                'seo_title' => 'NIDEO / Stockflow — Inventory platform — Mwanafunzi Investor',
                'seo_description' => 'Documented StockFlow inventory and procurement platform with tenant-aware operations, reporting and API workflows.',
            ],
            [
                'title' => 'Mwanafunzi Investor',
                'slug' => 'mwanafunzi-investor-platform',
                'industry' => 'Education & Digital Software',
                'project_type' => 'Public learning and CMS platform',
                'short_description' => 'The Laravel public platform and CMS foundation for financial education, digital software, creative work, accounts and digital commerce.',
                'full_introduction' => 'This repository documents the Mwanafunzi Investor Laravel public platform and CMS foundation. Public learning, course, tool, journal, contact, legal and disclosure routes are database-backed, with additional account, commerce, client-portal and administration workflows in the current application.',
                'solution' => 'The current application includes database-backed CMS pages and sections, Financial Academy content, Digital Software and Creative Studio module pages, blog discovery, course and product flows, client accounts and portals, contact and newsletter workflows, media management, SEO controls, cookie preferences and admin operations.',
                'technical_notes' => 'Evidence source: the current Mwanafunzi Investor repository README, Laravel route inventory, application models/controllers, migrations and passing feature tests. The documented application uses PHP 8.2+, Laravel 12, Blade, Vite and Tailwind CSS, with MySQL/MariaDB used for local/production deployments and SQLite in the test suite.',
                'technologies' => json_encode(['PHP 8.2+', 'Laravel 12', 'Blade', 'Vite', 'Tailwind CSS', 'MySQL/MariaDB', 'SQLite tests', 'PHPUnit'], JSON_UNESCAPED_UNICODE),
                'features' => json_encode([
                    ['title' => 'Public learning routes', 'description' => 'Database-backed public learning, course, tool, journal, contact and legal routes.'],
                    ['title' => 'Admin CMS', 'description' => 'Admin CMS for pages, sections, services, projects, media, articles, FAQs and testimonials.'],
                    ['title' => 'Accounts and commerce', 'description' => 'Client accounts, course enrolment, digital products and protected downloads.'],
                    ['title' => 'SEO and privacy', 'description' => 'SEO metadata, sitemap, canonical URLs, cookie preferences and privacy controls.'],
                    ['title' => 'Module enquiries', 'description' => 'Module-specific enquiry flows for Digital Software and Creative Studio.'],
                    ['title' => 'Security and testing', 'description' => 'Authentication, authorization, security hardening and feature-test coverage.'],
                ], JSON_UNESCAPED_UNICODE),
                'project_status' => 'active_development',
                'display_order' => 50,
                'seo_title' => 'Mwanafunzi Investor — Public learning and CMS platform',
                'seo_description' => 'Documented Mwanafunzi Investor Laravel platform for financial education, CMS content, digital products and client workflows.',
            ],
        ];

        foreach ($projects as $project) {
            $project['status'] = 'published';
            $project['published_at'] = $now;
            $project['is_featured'] = false;
            $project['robots'] = 'index,follow';
            $project['created_at'] = $now;
            $project['updated_at'] = $now;

            // These records are source-backed catalogue entries. Do not overwrite
            // an existing CMS record or a manually uploaded media field on rerun.
            $existingId = DB::table('projects')->where('slug', $project['slug'])->value('id');
            $projectId = $existingId ?: DB::table('projects')->insertGetId($project);

            DB::table('project_service')->updateOrInsert([
                'project_id' => $projectId,
                'service_id' => $serviceId,
            ], []);
        }
    }

    public function down(): void
    {
        $slugs = ['lodgix', 'tour-operator-management-platform', 'schoolix', 'nideo-stockflow', 'mwanafunzi-investor-platform'];
        $projectIds = DB::table('projects')->whereIn('slug', $slugs)->pluck('id');

        if ($projectIds->isNotEmpty()) {
            DB::table('project_service')->whereIn('project_id', $projectIds)->delete();
            DB::table('projects')->whereIn('id', $projectIds)->delete();
        }

        DB::table('services')->where('slug', 'digital-software-platforms')->delete();
    }
};
