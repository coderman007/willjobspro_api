<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
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

        $logoPath = $this->faker->image('public/storage/company_uploads/logos', 100, 100, null, false);
        $bannerPath = $this->faker->image('public/storage/company_uploads/banners', 800, 400, null, false);

        return [
            'user_id' => $user->id,
            'contact_person' => $this->faker->name,
            'phone_number' => $this->faker->phoneNumber,
            'industry' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'website' => $this->faker->url,
            'status' => $this->faker->randomElement(['Active', 'Blocked']),
            'logo_file' => $logoPath,
            'banner_file' => $bannerPath,
        ];
    }
}
