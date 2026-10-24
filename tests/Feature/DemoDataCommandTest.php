<?php

namespace Tests\Feature;

use Database\Seeders\DevelopmentDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDataCommandTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_demo_data_command_reports_exact_fixture_selectors_without_mutating_data(): void
    {
        $this->seed(DevelopmentDemoSeeder::class);

        $this->artisan('app:demo-data', ['action' => 'report', '--json' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('demo.admin@...');

        $this->assertDatabaseHas('users', ['email' => 'demo.admin@mwanafunziinvestor.test']);
        $this->assertDatabaseHas('articles', ['slug' => 'demo-article-for-qa']);
    }

    public function test_demo_data_command_refuses_production_even_with_apply(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this->artisan('app:demo-data', ['action' => 'cleanup', '--apply' => true])
            ->assertFailed()
            ->expectsOutputToContain('limited to local, testing, and staging');
    }
}
