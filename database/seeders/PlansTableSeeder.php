<?php

namespace Database\Seeders;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plan::updateOrCreate(
            [
                'name' => 'Free Plan',
            ],
            [
                'price' => 0,
                'duration' => 'lifetime',
                'max_users' => 5,
                'max_customers' => 5,
                'max_venders' => 5,
                'max_clients' => 5,
                'trial' => 0,
                'trial_days' => null,
                'is_disable' => 1,
                'storage_limit' => 1024,
                'crm' => 1,
                'hrm' => 1,
                'account' => 1,
                'project' => 1,
                'pos' => 1,
                'production' => 1,
                'chatgpt' => 1,
                'image' => 'free_plan.png',
            ]
        );

        Plan::updateOrCreate(
            [
                'name' => 'Starter Plan',
            ],
            [
                'price' => 19,
                'duration' => 'month',
                'max_users' => 10,
                'max_customers' => 50,
                'max_venders' => 25,
                'max_clients' => 50,
                'trial' => 1,
                'trial_days' => 14,
                'is_disable' => 1,
                'storage_limit' => 2048,
                'crm' => 1,
                'hrm' => 1,
                'account' => 1,
                'project' => 1,
                'pos' => 1,
                'production' => 1,
                'chatgpt' => 1,
                'image' => null,
            ]
        );

        Plan::updateOrCreate(
            [
                'name' => 'CRM Plan',
            ],
            [
                'price' => 29,
                'duration' => 'month',
                'max_users' => 10,
                'max_customers' => 500,
                'max_venders' => 0,
                'max_clients' => 500,
                'trial' => 1,
                'trial_days' => 7,
                'is_disable' => 1,
                'storage_limit' => 2048,
                'crm' => 1,
                'hrm' => 1,
                'account' => 1,
                'project' => 1,
                'pos' => 1,
                'production' => 1,
                'chatgpt' => 1,
                'image' => null,
            ]
        );

        Plan::updateOrCreate(
            [
                'name' => 'POS Plan',
            ],
            [
                'price' => 39,
                'duration' => 'month',
                'max_users' => 10,
                'max_customers' => 0,
                'max_venders' => 0,
                'max_clients' => 0,
                'trial' => 1,
                'trial_days' => 7,
                'is_disable' => 1,
                'storage_limit' => 2048,
                'crm' => 1,
                'hrm' => 1,
                'account' => 1,
                'project' => 1,
                'pos' => 1,
                'production' => 1,
                'chatgpt' => 1,
                'image' => null,
            ]
        );

        Plan::updateOrCreate(
            [
                'name' => 'Billing Plan',
            ],
            [
                'price' => 25,
                'duration' => 'month',
                'max_users' => 5,
                'max_customers' => 250,
                'max_venders' => 250,
                'max_clients' => 250,
                'trial' => 1,
                'trial_days' => 7,
                'is_disable' => 1,
                'storage_limit' => 2048,
                'crm' => 1,
                'hrm' => 1,
                'account' => 1,
                'project' => 1,
                'pos' => 1,
                'production' => 1,
                'chatgpt' => 1,
                'image' => null,
            ]
        );

        Plan::updateOrCreate(
            [
                'name' => 'Silver Plan',
            ],
            [
                'price' => 50,
                'duration' => 'month',
                'max_users' => 15,
                'max_customers' => 15,
                'max_venders' => 15,
                'max_clients' => 15,
                'trial' => 1,
                'trial_days' => 7,
                'is_disable' => 1,
                'storage_limit' => 5120,
                'crm' => 1,
                'hrm' => 1,
                'account' => 1,
                'project' => 1,
                'pos' => 1,
                'production' => 1,
                'chatgpt' => 1,
                'image' => 'silver_plan.png',
            ]
        );

        Plan::updateOrCreate(
            [
                'name' => 'Gold Plan',
            ],
            [
                'price' => 100,
                'duration' => 'month',
                'max_users' => 50,
                'max_customers' => 50,
                'max_venders' => 50,
                'max_clients' => 50,
                'trial' => 1,
                'trial_days' => 14,
                'is_disable' => 1,
                'storage_limit' => 10240,
                'crm' => 1,
                'hrm' => 1,
                'account' => 1,
                'project' => 1,
                'pos' => 1,
                'production' => 1,
                'chatgpt' => 1,
                'image' => 'gold_plan.png',
            ]
        );

        Plan::updateOrCreate(
            [
                'name' => 'Business Plan',
            ],
            [
                'price' => 299,
                'duration' => 'year',
                'max_users' => 200,
                'max_customers' => 2000,
                'max_venders' => 1000,
                'max_clients' => 2000,
                'trial' => 0,
                'trial_days' => null,
                'is_disable' => 1,
                'storage_limit' => 51200,
                'crm' => 1,
                'hrm' => 1,
                'account' => 1,
                'project' => 1,
                'pos' => 1,
                'production' => 1,
                'chatgpt' => 1,
                'image' => null,
            ]
        );

        Plan::updateOrCreate(
            [
                'name' => 'Platinum Plan',
            ],
            [
                'price' => 500,
                'duration' => 'year',
                'max_users' => -1,
                'max_customers' => -1,
                'max_venders' => -1,
                'max_clients' => -1,
                'trial' => 0,
                'trial_days' => null,
                'is_disable' => 1,
                'storage_limit' => -1,
                'crm' => 1,
                'hrm' => 1,
                'account' => 1,
                'project' => 1,
                'pos' => 1,
                'production' => 1,
                'chatgpt' => 1,
                'image' => 'platinum_plan.png',
            ]
        );
    }
}
