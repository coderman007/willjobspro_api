<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
//            CountrySeeder::class,
//            StateSeeder::class,
//            CitySeeder::class,
//            ZipCodeSeeder::class,
            RoleSeeder::class,
//            UserSeeder::class,
            SkillCategorySeeder::class,
//            SubscriptionPlanSeeder::class,
            JobCategorySeeder::class,
            JobTypeSeeder::class,
            EducationLevelSeeder::class,
//            SocialNetworkSeeder::class,
            LanguageSeeder::class,
            SkillSeeder::class,
//            CandidateSeeder::class,
//            CompanySeeder::class,
//            JobSeeder::class,
//            ApplicationSeeder::class,
        ]);
    }
}
