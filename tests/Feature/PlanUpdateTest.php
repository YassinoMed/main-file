<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PlanUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_can_be_updated_without_payment_settings()
    {
        Permission::query()->firstOrCreate(['name' => 'edit plan', 'guard_name' => 'web']);

        $user = User::factory()->create([
            'type' => 'super admin',
        ]);
        $user->givePermissionTo('edit plan');

        Plan::query()->create([
            'name' => 'Free',
            'price' => 0,
            'duration' => 'lifetime',
            'max_users' => 10,
            'max_customers' => 10,
            'max_venders' => 10,
            'max_clients' => 10,
            'storage_limit' => 100,
            'chatgpt' => 0,
            'crm' => 0,
            'hrm' => 0,
            'account' => 0,
            'project' => 0,
            'pos' => 0,
        ]);

        $plan = Plan::query()->create([
            'name' => 'Pro',
            'price' => 10,
            'duration' => 'month',
            'max_users' => 10,
            'max_customers' => 10,
            'max_venders' => 10,
            'max_clients' => 10,
            'storage_limit' => 100,
            'chatgpt' => 0,
            'crm' => 0,
            'hrm' => 0,
            'account' => 0,
            'project' => 0,
            'pos' => 0,
        ]);

        $response = $this->actingAs($user)->put(route('plans.update', $plan->id), [
            'name' => 'Pro Updated',
            'price' => 25,
            'duration' => 'year',
            'max_users' => 20,
            'max_customers' => 30,
            'max_venders' => 40,
            'max_clients' => 50,
            'storage_limit' => 200,
            'enable_crm' => 'on',
            'trial' => 1,
            'trial_days' => 7,
        ]);

        $response->assertStatus(302);

        $plan->refresh();

        $this->assertSame('Pro Updated', $plan->name);
        $this->assertSame(25.0, (float) $plan->price);
        $this->assertSame('year', $plan->duration);
        $this->assertSame(20, $plan->max_users);
        $this->assertSame(30, $plan->max_customers);
        $this->assertSame(40, $plan->max_venders);
        $this->assertSame(50, $plan->max_clients);
        $this->assertSame(200.0, (float) $plan->storage_limit);
        $this->assertSame(1, $plan->crm);
        $this->assertSame(1, $plan->trial);
        $this->assertSame(7, $plan->trial_days);
    }
}
