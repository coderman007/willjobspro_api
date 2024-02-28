<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = base_path('database/seeders/seeds/countries.json');

        // Verificar si el archivo JSON existe
        if (file_exists($jsonPath)) {
            // Leer el contenido del archivo JSON
            $jsonData = file_get_contents($jsonPath);

            // Decodificar el JSON
            $data = json_decode($jsonData, true);

            foreach ($data as $countryData) {
                foreach ($countryData['regions'] as $regionData) {
                    // Obtener el estado o crearlo si no existe
                    $state = State::where(['country_id' => $countryData['id'], 'name' => $regionData['region']])->first();

                    if ($state) {
                        // Iterar sobre las ciudades y crearlas
                        foreach ($regionData['cities'] as $cityData) {
                            City::updateOrCreate(
                                ['state_id' => $state->id, 'name' => $cityData['city']]
                            );
                        }
                    }
                }
            }
        }
    }
}
