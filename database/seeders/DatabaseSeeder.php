<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            ZipCodeSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SubscriptionPlanSeeder::class,
            JobCategorySeeder::class,
            JobTypeSeeder::class,
            EducationLevelSeeder::class,
            SkillCategorySeeder::class,
            SkillSeeder::class,
            CandidateSeeder::class,
            CompanySeeder::class,
            JobSeeder::class,
            ApplicationSeeder::class,
        ]);
    }
}
