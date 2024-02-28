<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\State;

class StateSeeder extends Seeder
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
                // Obtener el país o crearlo si no existe
                $country = Country::where('iso_alpha_2', $countryData['iso_alpha_2'])->first();

                if ($country) {
                    // Iterar sobre las regiones y crear los estados
                    foreach ($countryData['regions'] as $regionData) {
                        State::updateOrCreate(
                            ['country_id' => $country->id, 'name' => $regionData['region']]
                        );
                    }
                }
            }
        }
    }
}
