<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'company');
        })->inRandomOrder()->first();

        if (!$user) {
            $user = User::factory()->create();
            $user->assignRole('company');
        }

        return [
            'user_id' => $user->id,
            'name' => $this->faker->company,
            'industry' => $this->faker->word,
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
            'website' => $this->faker->url,
            'description' => $this->faker->paragraph,
            'contact_person' => $this->faker->name,
            'logo_path' => $this->faker->imageUrl(),
            'banner_path' => $this->faker->imageUrl(),
            'social_networks' =>  json_encode(['twitter' => $this->faker->userName, 'linkedin' => $this->faker->userName]),
            'status' => $this->faker->randomElement(['Active', 'Blocked']),
        ];
    }
}
