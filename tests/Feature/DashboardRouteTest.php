<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_redirects_company_to_account_dashboard()
    {
        User::factory()->create([
            'type' => 'company',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs(User::first())->get('/dashboard');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_dashboard_renders_for_super_admin()
    {
        $freePlan = Plan::create([
            'name' => 'Free',
            'price' => 0,
            'duration' => 'lifetime',
            'max_users' => 1,
            'max_customers' => 1,
            'max_venders' => 1,
            'max_clients' => 1,
            'trial' => 0,
            'trial_days' => 0,
            'description' => '',
            'image' => '',
            'crm' => 1,
            'hrm' => 1,
            'account' => 1,
            'project' => 1,
            'pos' => 1,
            'chatgpt' => 0,
            'storage_limit' => 0,
        ]);

        $paidPlan = Plan::create([
            'name' => 'Pro',
            'price' => 10,
            'duration' => 'month',
            'max_users' => 10,
            'max_customers' => 10,
            'max_venders' => 10,
            'max_clients' => 10,
            'trial' => 0,
            'trial_days' => 0,
            'description' => '',
            'image' => '',
            'crm' => 1,
            'hrm' => 1,
            'account' => 1,
            'project' => 1,
            'pos' => 1,
            'chatgpt' => 0,
            'storage_limit' => 0,
        ]);

        $superAdmin = User::factory()->create([
            'type' => 'super admin',
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'type' => 'company',
            'created_by' => $superAdmin->id,
            'plan' => $paidPlan->id,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)->get('/dashboard');

        $response->assertOk();
        $this->assertNotEmpty($freePlan->id);
    }
}

