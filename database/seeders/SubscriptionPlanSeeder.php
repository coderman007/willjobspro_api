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
        // Create basic plan
        SubscriptionPlan::create([
            'name' => 'Basic',
            'description' => 'Free plan with standard features.',
            'price' => 0.00,
            'duration' => 30,
        ]);

        // Create premium plan
        SubscriptionPlan::create([
            'name' => 'Premium',
            'description' => 'Access premium features for an affordable price.',
            'price' => 19.99,
            'duration' => 60,
        ]);

        // Create VIP plan
        SubscriptionPlan::create([
            'name' => 'VIP',
            'description' => 'VIP experience with all features.',
            'price' => 49.99,
            'duration' => 90,
        ]);
    }
}
