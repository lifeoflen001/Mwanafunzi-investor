<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ArticleCategory;
use App\Models\BusinessUnit;
use App\Models\Course;
use App\Models\LearningTopic;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $businessUnits = [
            [
                'slug' => 'forex',
                'name' => 'Forex Academy',
                'tagline' => 'Student of Money. Systems. Discipline.',
                'description' => 'Structured financial education for systematic trading, probability, risk management and disciplined portfolio thinking.',
                'accent_color' => '#c56c38',
                'route_name' => 'home',
                'sort_order' => 1,
            ],
            [
                'slug' => 'development',
                'name' => 'Digital Systems',
                'tagline' => 'Websites and software built for real work.',
                'description' => 'Websites, Laravel systems, dashboards and integrations for organisations that need dependable digital tools.',
                'accent_color' => '#56736d',
                'route_name' => 'development',
                'sort_order' => 2,
            ],
            [
                'slug' => 'studio',
                'name' => 'Creative Studio',
                'tagline' => 'Photography and video with a point of view.',
                'description' => 'Photography and videography for people, brands and organisations that need their story documented well.',
                'accent_color' => '#a85b45',
                'route_name' => 'studio',
                'sort_order' => 3,
            ],
        ];

        foreach ($businessUnits as $businessUnit) {
            BusinessUnit::updateOrCreate(['slug' => $businessUnit['slug']], $businessUnit);
        }

        User::updateOrCreate(['email' => 'test@example.com'], [
            'name' => 'Test User',
            'password' => 'password',
            'is_admin' => false,
        ]);

        $topics = [
            ['title' => 'Forex Core Basics — No BS', 'slug' => 'forex-core-basics', 'icon' => '◒', 'short_description' => 'Understand the language of the market, from currency pairs to the forces that move them.', 'full_description' => 'Start with a grounded understanding of market structure, currency pairs, context and the habits of a careful student.', 'skill_level' => 'Foundation', 'study_time' => '2–4 weeks', 'sort_order' => 1],
            ['title' => 'Mechanical Analysis Concepts, Rules & Execution', 'slug' => 'mechanical-analysis', 'icon' => '⌁', 'short_description' => 'Translate an idea into a repeatable, testable decision process.', 'full_description' => 'Move from a market idea to clear rules that can be tested, repeated and reviewed without relying on impulse.', 'skill_level' => 'Intermediate', 'study_time' => '4–6 weeks', 'sort_order' => 2],
            ['title' => 'Risk Planning', 'slug' => 'risk-planning', 'icon' => '◌', 'short_description' => 'Make position size, exposure and the cost of being wrong part of the plan.', 'full_description' => 'Learn to make risk visible before a trade, a week or a portfolio.', 'skill_level' => 'All levels', 'study_time' => '2–3 weeks', 'sort_order' => 3],
            ['title' => 'Running Trade Risk Management', 'slug' => 'running-trade-risk-management', 'icon' => '↗', 'short_description' => 'Manage an open position with rules that protect your thinking as conditions change.', 'full_description' => 'Practice the decisions that matter after entry: manage, document and review.', 'skill_level' => 'Intermediate', 'study_time' => '3–4 weeks', 'sort_order' => 4],
        ];
        foreach ($topics as $topic) LearningTopic::updateOrCreate(['slug' => $topic['slug']], $topic);

        $courses = [
            ['title' => 'Forex Foundations', 'slug' => 'forex-foundations', 'subtitle' => 'Start with a stronger foundation.', 'level' => 'Beginner', 'duration' => 'Self-paced', 'status' => 'published', 'is_featured' => true, 'short_description' => 'Start with market structure, language, context and the habits of a careful student.', 'full_description' => 'A calm entry point into the language and structure of the market, built for people who want context before complexity.', 'cta_label' => 'Join the waitlist', 'sort_order' => 1],
            ['title' => 'Building a Mechanical Trading System', 'slug' => 'building-a-mechanical-trading-system', 'subtitle' => 'Turn ideas into repeatable rules.', 'level' => 'Intermediate', 'duration' => 'Self-paced', 'status' => 'coming_soon', 'is_featured' => true, 'short_description' => 'Move from a market idea to clear rules you can test and repeat.', 'full_description' => 'A structured path for translating an idea into a repeatable, testable decision process.', 'cta_label' => 'Join the waitlist', 'sort_order' => 2],
            ['title' => 'Risk Planning & Capital Preservation', 'slug' => 'risk-planning-capital-preservation', 'subtitle' => 'Keep risk visible before you act.', 'level' => 'Intermediate', 'duration' => 'Guided', 'status' => 'coming_soon', 'is_featured' => false, 'short_description' => 'Learn how to keep risk visible before a trade, a week or a portfolio.', 'full_description' => 'Build a risk-first framework for position size, exposure and the cost of being wrong.', 'cta_label' => 'Join the waitlist', 'sort_order' => 3],
            ['title' => 'Running Trade Management', 'slug' => 'running-trade-management', 'subtitle' => 'Practice the decisions after entry.', 'level' => 'Intermediate / Advanced', 'duration' => 'Coming soon', 'status' => 'coming_soon', 'is_featured' => false, 'short_description' => 'Practice the decisions that matter after an entry: manage, document and review.', 'full_description' => 'A future course on managing open positions with rules that protect your thinking as conditions change.', 'cta_label' => 'Join the waitlist', 'sort_order' => 4],
        ];
        foreach ($courses as $courseData) {
            $course = Course::updateOrCreate(['slug' => $courseData['slug']], $courseData);
            $topic = LearningTopic::where('sort_order', $courseData['sort_order'])->first();
            $topic?->courses()->syncWithoutDetaching([$course->id]);
        }

        $products = [
            ['name' => 'Trading Journal Sheet', 'slug' => 'trading-journal-sheet', 'product_type' => 'Template / Notion or Sheet', 'availability' => 'waitlist', 'is_featured' => true, 'short_description' => 'A calm place to capture the plan, execution and lesson behind each decision.', 'detailed_description' => 'A focused journal template for recording decisions while the context is clear, then reviewing patterns across a meaningful sample.', 'sort_order' => 1],
            ['name' => 'MT Simulator', 'slug' => 'mt-simulator', 'product_type' => 'Practice / Simulation', 'availability' => 'coming_soon', 'is_featured' => false, 'short_description' => 'Rehearse your rules in a controlled environment before the pressure is real.', 'detailed_description' => 'A future practice tool for replaying market conditions and rehearsing a defined process.', 'sort_order' => 2],
            ['name' => 'Risk Planner / Manager', 'slug' => 'risk-planner-manager', 'product_type' => 'Planning / Spreadsheet', 'availability' => 'waitlist', 'is_featured' => true, 'short_description' => 'Bring risk per trade, portfolio exposure and the bigger picture into one view.', 'detailed_description' => 'A planning workspace for keeping risk visible across individual decisions and the wider portfolio.', 'sort_order' => 3],
            ['name' => 'MT Risk Calculator', 'slug' => 'mt-risk-calculator', 'product_type' => 'Utility / Calculator', 'availability' => 'coming_soon', 'is_featured' => false, 'short_description' => 'Keep position sizing mechanical, clear and accountable before you place a trade.', 'detailed_description' => 'A future position-sizing utility built around a risk-first process.', 'sort_order' => 4],
        ];
        foreach ($products as $productData) Product::updateOrCreate(['slug' => $productData['slug']], $productData);

        foreach (['Trading Reviews', 'Forex Education', 'Mechanical Systems', 'Risk Management', 'Trading Psychology', 'Portfolio Thinking', 'Student of Money', 'Lessons From The Market'] as $category) {
            ArticleCategory::updateOrCreate(['slug' => str($category)->slug()], ['name' => $category]);
        }

        $settings = [
            'brand_name' => 'Mwanafunzi Investor',
            'contact_email' => 'mwanafunziinvestor@outlook.com',
            'contact_phone' => '+255 787 172 686',
            'contact_location' => 'Tanzania',
            'default_seo_title' => 'Mwanafunzi Investor — Become a Student of Money',
            'default_seo_description' => 'Financial education for systematic trading, probability, risk management and disciplined portfolio thinking.',
            'risk_disclaimer' => 'Educational content only. This is not personalised financial advice. Trading involves risk and leveraged trading can result in capital loss.',
        ];
        foreach ($settings as $key => $value) SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => 'text']);
    }
}
