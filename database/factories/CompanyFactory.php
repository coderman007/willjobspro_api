<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
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
            'name' => $this->faker->company,
            'industry' => $this->faker->word,
            'phone_number' => $this->faker->phoneNumber,
            'website' => $this->faker->url,
            'description' => $this->faker->paragraph,
            'contact_person' => $this->faker->name,
            'logo_path' => $logoPath,
            'banner_path' => $bannerPath,
            'social_networks' => json_encode(['twitter' => $this->faker->userName, 'linkedin' => $this->faker->userName]),
            'status' => $this->faker->randomElement(['Active', 'Blocked']),
        ];
    }
}
