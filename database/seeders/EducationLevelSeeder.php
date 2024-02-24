<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationLevel;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos de ejemplo para los niveles de estudio
        $levels = [
            [
                'name' => 'Bachelor\'s Degree',
                'description' => 'This is the Bachelor\'s Degree description'
            ],
            [
                'name' => 'Master\'s Degree',
                'description' => 'This is the Master\'s Degree description'
            ],

            [
                'name' => 'Doctorate Degree',
                'description' => 'This is the Doctorate Degree description'
            ],

            [
                'name' => 'Professional Degree',
                'description' => 'This is the Professional Degree description'
            ],
        ];

        // Insertar los datos en la base de datos
        EducationLevel::insert($levels);
    }
}
