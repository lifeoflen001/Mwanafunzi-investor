<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $featuresBySlug = [
            'lodgix' => [
                ['title' => 'Reservation lifecycle', 'description' => 'Check-in, check-out, cancellation and no-show workflows.'],
                ['title' => 'Room operations', 'description' => 'Room planning, room categories, room types, availability and housekeeping status.'],
                ['title' => 'Staff access', 'description' => 'Staff profiles, role-based access, permissions and two-factor authentication.'],
                ['title' => 'Maintenance and tasks', 'description' => 'Maintenance and task tracking connected to rooms and reservations.'],
                ['title' => 'Payments and reporting', 'description' => 'Payment records, exports, webhooks, notification preferences and reports.'],
            ],
            'tour-operator-management-platform' => [
                ['title' => 'Inquiry CRM', 'description' => 'Customer, owner, pipeline stage and follow-up records.'],
                ['title' => 'Itineraries and quotations', 'description' => 'Versioned itineraries and quotation versions with server-side costing.'],
                ['title' => 'Booking conversion', 'description' => 'Customer approval and idempotent conversion to a central Booking File.'],
                ['title' => 'Supplier and passenger records', 'description' => 'Supplier, accommodation, passenger, payment and readiness records.'],
                ['title' => 'Trip operations', 'description' => 'Operational plans, resource allocation, dispatch, incidents and closure controls.'],
                ['title' => 'Public tourism routes', 'description' => 'Itineraries, destinations, activities, accommodations, proposals, blog and FAQs.'],
            ],
            'schoolix' => [
                ['title' => 'School tenancy', 'description' => 'Organization → School → Campus tenancy and contextual memberships.'],
                ['title' => 'Admissions and people', 'description' => 'Admissions, student records, staff/HR and teacher assignments.'],
                ['title' => 'Academic operations', 'description' => 'Academic configuration, timetables, attendance, examinations, results and report cards.'],
                ['title' => 'Finance and portals', 'description' => 'Finance, communications, learner/guardian portals and teacher portal resources.'],
                ['title' => 'School services', 'description' => 'Library, transport, hostel/boarding and inventory/assets/procurement foundations.'],
                ['title' => 'Reporting and documents', 'description' => 'Central reporting, saved reports, controlled document templates and secure exports.'],
                ['title' => 'API boundary', 'description' => 'Versioned school-bound REST API and provider-neutral integration boundaries.'],
            ],
            'nideo-stockflow' => [
                ['title' => 'Tenant-scoped access', 'description' => 'Organization and tenant-scoped access controls.'],
                ['title' => 'Stock and warehouses', 'description' => 'Stock ledger, balances, movements, warehouses and locations.'],
                ['title' => 'Procurement', 'description' => 'Suppliers, procurement requests, purchase orders and goods receipt.'],
                ['title' => 'Inventory reporting', 'description' => 'Inventory valuation, reports, imports and exports.'],
                ['title' => 'Operational controls', 'description' => 'Role and permission isolation, audit records and operational notifications.'],
                ['title' => 'Application interface', 'description' => 'REST API and compiled single-page frontend.'],
            ],
            'mwanafunzi-investor-platform' => [
                ['title' => 'Public learning routes', 'description' => 'Database-backed public learning, course, tool, journal, contact and legal routes.'],
                ['title' => 'Admin CMS', 'description' => 'Admin CMS for pages, sections, services, projects, media, articles, FAQs and testimonials.'],
                ['title' => 'Accounts and commerce', 'description' => 'Client accounts, course enrolment, digital products and protected downloads.'],
                ['title' => 'SEO and privacy', 'description' => 'SEO metadata, sitemap, canonical URLs, cookie preferences and privacy controls.'],
                ['title' => 'Module enquiries', 'description' => 'Module-specific enquiry flows for Digital Software and Creative Studio.'],
                ['title' => 'Security and testing', 'description' => 'Authentication, authorization, security hardening and feature-test coverage.'],
            ],
        ];

        foreach (DB::table('projects')->whereIn('slug', array_keys($featuresBySlug))->get(['id', 'slug', 'features']) as $project) {
            $current = json_decode((string) $project->features, true);

            // Only normalize the rows created by the preceding migration. A
            // manually edited structured feature list remains untouched.
            if (is_array($current) && collect($current)->every(fn ($feature): bool => is_string($feature))) {
                DB::table('projects')->where('id', $project->id)->update([
                    'features' => json_encode($featuresBySlug[$project->slug], JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // The original feature groups are source-backed CMS data. Keep them
        // intact on rollback rather than degrading the public case studies.
    }
};
