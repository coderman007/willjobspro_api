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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'company_id' => Company::pluck('id')->random(),
            'job_category_id' => JobCategory::pluck('id')->random(),
            'subscription_plan_id' => SubscriptionPlan::pluck('id')->random(),

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

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Job $job) {
            // Obtener un número aleatorio de tipos de trabajo para asociar con la oferta de trabajo
            $numJobTypes = $this->faker->numberBetween(1, 3);

            // Obtener IDs aleatorios de tipos de trabajo
            $jobTypeIds = \App\Models\JobType::inRandomOrder()->limit($numJobTypes)->pluck('id')->toArray();

            // Asociar los tipos de trabajo con la oferta de trabajo recién creada
            $job->jobTypes()->sync($jobTypeIds);
        });
    }
}
