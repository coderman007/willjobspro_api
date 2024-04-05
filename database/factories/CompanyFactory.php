<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
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
            'contact_person' => $this->faker->name,
            'phone_number' => $this->faker->phoneNumber,
            'industry' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'website' => $this->faker->url,
            'status' => $this->faker->randomElement(['Active', 'Blocked']),
            'logo_file' => 'company_uploads/logos/' . $this->faker->image('public/storage/company_uploads/logos', 100, 100, null, false),
            'banner_file' => 'company_uploads/banners/' . $this->faker->image('public/storage/company_uploads/banners', 800, 400, null, false),
        ];
    }
}
