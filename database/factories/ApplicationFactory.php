<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition()
    {
        $candidate = Candidate::inRandomOrder()->first();
        $job = Job::inRandomOrder()->first();

        return [
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
            'cover_letter' => $this->faker->text(200),
            'status' => $this->faker->randomElement(['Pending', 'Reviewed', 'Accepted', 'Rejected']),
            'application_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'rejection_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
