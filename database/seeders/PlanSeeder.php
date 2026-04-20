<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'Starter',
                'slug'          => 'starter',
                'price'         => 30000,
                'billing_cycle' => 'monthly',
                'max_players'   => 100,
                'max_users'     => 5,
                'features'      => ['players', 'teams', 'planning', 'stock'],
                'is_active'     => true,
            ],
            [
                'name'          => 'Pro',
                'slug'          => 'pro',
                'price'         => 75000,
                'billing_cycle' => 'monthly',
                'max_players'   => 500,
                'max_users'     => 20,
                'features'      => ['players', 'teams', 'planning', 'stock', 'donations', 'transfers'],
                'is_active'     => true,
            ],
            [
                'name'          => 'Elite',
                'slug'          => 'elite',
                'price'         => 150000,
                'billing_cycle' => 'monthly',
                'max_players'   => 99999, // illimité
                'max_users'     => 99999,
                'features'      => ['players', 'teams', 'planning', 'stock', 'donations', 'transfers', 'api', 'white_label'],
                'is_active'     => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
