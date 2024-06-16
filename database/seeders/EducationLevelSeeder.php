<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationLevelSeeder extends Seeder
{
    public function run()
    {
        DB::table('education_levels')->insert([
            [
                'name' => 'High School',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bachelor\'s Degree',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Master\'s Degree',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Doctorate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
