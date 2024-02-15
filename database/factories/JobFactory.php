<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'company_id' => Company::inRandomOrder()->first()->id,
            'job_category_id' => JobCategory::inRandomOrder()->first()->id,
            'job_type_id' => JobType::inRandomOrder()->first()->id,
            'subscription_plan_id' => SubscriptionPlan::inRandomOrder()->first()->id,

            'title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraph,
            'posted_date' => $this->faker->date,
            'deadline' => $this->faker->date,
            'location' => $this->faker->city,
            'salary' => $this->faker->randomFloat(2, 30000, 100000),
            'contact_email' => $this->faker->email,
            'contact_phone' => $this->faker->phoneNumber,
            'status' => $this->faker->randomElement(['Open', 'Closed', 'Under Review']),
        ];
    }
}
