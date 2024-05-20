<?php

namespace Database\Seeders;

use App\Models\Benefit;
use Illuminate\Database\Seeder;

class BenefitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos de ejemplo para los idiomas
        $benefits = [
            ['name' => 'Benefit Sample 1'],
            ['name' => 'Benefit Sample 2'],
            ['name' => 'Benefit Sample 3'],
            ['name' => 'Benefit Sample 4'],
            ['name' => 'Benefit Sample 5'],
            ['name' => 'Benefit Sample 6'],
            ['name' => 'Benefit Sample 7'],
            ['name' => 'Benefit Sample 8'],
        ];

        // Insertar los datos en la base de datos
        foreach ($benefits as $benefit) {
            Benefit::create($benefit);
        }
    }
}
