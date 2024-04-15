<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

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
                        ['name' => $countryData['country']],
                        ['iso_alpha_2' => $countryData['iso_alpha_2'], 'dial_code' => $countryData['dial_code']]
                    );

                } catch (\Exception $e) {
                    // Manejar el error, por ejemplo, loguearlo
                    Log::error("Error while trying processing Country: {$countryData['country']}, ISO: {$countryData['iso_alpha_2']}, Dial Code: {$countryData['dial_code']}'. Error: {$e->getMessage()}");
                }
            }
        }
    }
}
