<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create free plan
        SubscriptionPlan::create([
            'name' => 'Free',
            'description' => 'Free plan with standard features.',
            'price' => 0.00,
            'duration' => 360,
        ]);

        // Create premium plan
        SubscriptionPlan::create([
            'name' => 'Premium',
            'description' => 'Access premium features for an affordable price.',
            'price' => 19.99,
            'duration' => 360,
        ]);

        // Create VIP plan
        SubscriptionPlan::create([
            'name' => 'VIP',
            'description' => 'VIP experience with all features.',
            'price' => 49.99,
            'duration' => 360,
        ]);
    }
}
