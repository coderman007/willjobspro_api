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

        // Storage::deleteDirectory('cvs');
        // Storage::makeDirectory('cvs');

        // Storage::deleteDirectory('photos');
        // Storage::makeDirectory('photos');

        // Storage::deleteDirectory('banners');
        // Storage::makeDirectory('banners');

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            SubscriptionPlanSeeder::class,
            JobCategorySeeder::class,
            SkillCategorySeeder::class,
            JobTypeSeeder::class,
            SkillSeeder::class,
            CandidateSeeder::class,
            // CompanySeeder::class,
        ]);
    }
}
