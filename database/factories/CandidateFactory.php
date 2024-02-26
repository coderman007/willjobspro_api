<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateFactory extends Factory
{
    public function definition(): array
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'candidate');
        })->inRandomOrder()->first();

        if (!$user) {
            $user = User::factory()->create();
            $user->assignRole('candidate');
        }

        $cvPath = 'candidate_uploads/cvs/' . $this->faker->file('public/storage/candidate_uploads/cvs', storage_path('app/public/candidate_uploads/cvs'), false);
        $photoPath = 'candidate_uploads/profile_photos/' . $this->faker->image('public/storage/candidate_uploads/profile_photos', 100, 100, null, false);
        $bannerPath = 'candidate_uploads/banners/' . $this->faker->image('public/storage/candidate_uploads/banners', 800, 400, null, false);

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
            'cv_path' => $cvPath,
            'photo_path' => $photoPath,
            'banner_path' => $bannerPath,
            'social_networks' => json_encode(['facebook' => 'example.com/facebook']),
            'status' => $this->faker->randomElement(['Active', 'Blocked']),
        ];
    }
}
