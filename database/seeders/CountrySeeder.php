<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/seeds/countries.json');

        // Verificar si el archivo JSON existe
        if (file_exists($jsonPath)) {
            // Leer el contenido del archivo JSON
            $jsonData = file_get_contents($jsonPath);

            // Decodificar el JSON
            $data = json_decode($jsonData, true);

            foreach ($data as $countryData) {
                try {
                    // Crear o actualizar el país
                    Country::updateOrCreate(
                        ['iso_alpha_2' => $countryData['iso_alpha_2']],
                        ['name' => $countryData['country']]
                    );
                } catch (\Exception $e) {
                    // Manejar el error, por ejemplo, loguearlo
                    \Log::error("Error al procesar el país: {$countryData['country']}, ISO: {$countryData['iso_alpha_2']}. Error: {$e->getMessage()}");
                }
            }
        }

    }
}
