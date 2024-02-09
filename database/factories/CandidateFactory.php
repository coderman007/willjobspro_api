<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Candidate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'candidate');
        })->inRandomOrder()->first();

        if (!$user) {
            $user = User::factory()->create();
            $user->assignRole('candidate');
        }

        return [
            'user_id' => $user->id,
            'full_name' => $this->faker->name,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'date_of_birth' => $this->faker->date(),
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
            'work_experience' => $this->faker->text,
            'education' => $this->faker->text,
            'certifications' => $this->faker->text,
            'languages' => $this->faker->text,
            'references' => $this->faker->text,
            'expected_salary' => $this->faker->randomFloat(2, 1000, 10000),
            'cv_path' => 'N/A',
            'photo_path' => $this->faker->imageUrl(),
            'banner_path' => $this->faker->imageUrl(),
            'social_networks' => json_encode(['facebook' => 'example.com/facebook']),
            'status' => $this->faker->randomElement(['Active', 'Blocked']),
        ];
    }
}
