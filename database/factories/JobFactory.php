<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::pluck('id')->random(),
            'job_category_id' => JobCategory::pluck('id')->random(),
            'subscription_plan_id' => SubscriptionPlan::pluck('id')->random(),
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraph,
            'posted_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'deadline' => $this->faker->dateTimeBetween('now', '+30 days'),
            'salary' => $this->faker->numberBetween(30000, 80000),
            'contact_email' => $this->faker->companyEmail,
            'contact_phone' => $this->faker->phoneNumber,
            'experience_required' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['Open', 'Closed', 'Under Review']),
        ];
    }
}
