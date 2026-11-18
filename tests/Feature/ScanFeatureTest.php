<?php

namespace Tests\Feature;

use App\Models\Scan;
use App\Models\User;
use Database\Seeders\AppSettingSeeder;
use Database\Seeders\BrandWatchlistSeeder;
use Database\Seeders\CriterionSeeder;
use Database\Seeders\ThreatIntelligenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            CriterionSeeder::class,
            AppSettingSeeder::class,
            BrandWatchlistSeeder::class,
            ThreatIntelligenceSeeder::class,
        ]);
    }

    public function test_landing_page_accessible(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('SafeLink');
    }

    public function test_guest_can_scan_valid_url(): void
    {
        $response = $this->post(route('scan.store'), [
            'url' => 'https://example.com',
        ]);

        $scan = Scan::query()->first();
        $this->assertNotNull($scan);
        $response->assertRedirect(route('scan.show', $scan));
        $this->assertEqualsWithDelta(1.0, $scan->criterionResults->sum('criterion_weight_snapshot'), 0.0001);
        $this->assertCount(6, $scan->criterionResults);
    }

    public function test_invalid_url_is_rejected(): void
    {
        $this->from(route('scan.create'))
            ->post(route('scan.store'), ['url' => 'ftp://example.com'])
            ->assertSessionHasErrors('url');

    }

    public function test_user_cannot_view_other_users_scan(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($owner)->post(route('scan.store'), ['url' => 'https://example.org']);
        $scan = Scan::query()->first();

        $this->actingAs($other)
            ->get(route('scan.show', $scan))
            ->assertForbidden();
    }

    public function test_user_cannot_access_admin(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_access_dashboard_and_manage_weights(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();

        $criteria = \App\Models\Criterion::active()->get();
        $weights = [];
        foreach ($criteria as $c) {
            $weights[$c->id] = $c->weight;
        }

        $this->actingAs($admin)
            ->put(route('admin.weights.update'), ['weights' => $weights])
            ->assertRedirect();
    }

    public function test_confirmed_threat_sets_priority_warning(): void
    {
        $this->post(route('scan.store'), [
            'url' => 'https://phish.example.net/login',
        ])->assertRedirect();

        $scan = Scan::query()->latest('id')->first();
        $this->assertSame('phishing', $scan->threat_match_status);
        $this->assertStringContainsString('Peringatan prioritas', (string) $scan->warning_message);
    }

    public function test_xss_is_escaped_in_result_page(): void
    {
        // URL parser will reject many XSS payloads; ensure blade escapes stored content.
        $user = User::factory()->create();
        $scan = Scan::query()->create([
            'user_id' => $user->id,
            'original_url' => 'https://example.com/<script>alert(1)</script>',
            'normalized_url' => 'https://example.com/%3Cscript%3Ealert(1)%3C/script%3E',
            'domain' => 'example.com',
            'protocol' => 'https',
            'security_score' => 0.8,
            'risk_index' => 20,
            'risk_category' => 'low',
            'data_coverage_percentage' => 100,
            'explanation' => '<script>alert(1)</script>',
            'scanned_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('scan.show', $scan))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
