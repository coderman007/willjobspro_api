<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SubscriptionPlanSeeder::class,
            JobCategorySeeder::class,
            SkillCategorySeeder::class,
            JobTypeSeeder::class,
            SkillSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'jaime Sierra',
            'email' => 'coderman1980@gmail.com',
            'password' => bcrypt('Coderman1980$'),
        ]);
        $user->assignRole('admin');
    }
}
