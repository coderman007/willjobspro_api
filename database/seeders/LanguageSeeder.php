<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
// Datos de ejemplo para los idiomas
        $languages = [
            [
                'name' => 'English'
            ],
            [
                'name' => 'Spanish'
            ],
            [
                'name' => 'French'
            ],
            [
                'name' => 'Italian'
            ],
            [
                'name' => 'German'
            ],
            [
                'name' => 'Portuguese'
            ],
            [
                'name' => 'Chinese'
            ],
            [
                'name' => 'Japanese'
            ],
// Agrega más niveles de educación según sea necesario
        ];

// Insertar los datos en la base de datos
        Language::insert($languages);
    }
}
