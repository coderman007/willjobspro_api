<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use Illuminate\Database\Seeder;

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
// Agrega más niveles de educación aquí
            [
                'name' => 'Associate\'s Degree',
                'description' => 'This is the Associate\'s Degree description'
            ],
            [
                'name' => 'High School Diploma',
                'description' => 'This is the High School Diploma description'
            ],
            [
                'name' => 'Vocational Certificate',
                'description' => 'This is the Vocational Certificate description'
            ],
// Agrega más niveles de educación según sea necesario
        ];

// Insertar los datos en la base de datos
        EducationLevel::create($levels);
    }
}
