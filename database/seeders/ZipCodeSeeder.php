<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\ZipCode;

class ZipCodeSeeder extends Seeder
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
                    foreach ($regionData['cities'] as $cityData) {
                        // Obtener la ciudad o crearla si no existe
                        $city = City::where(['state_id' => $regionData['id'], 'name' => $cityData['city']])->first();

                        if ($city) {
                            // Iterar sobre los códigos postales y crearlos
                            foreach ($cityData['zip_codes'] as $zipCodeData) {
                                ZipCode::updateOrCreate(
                                    ['city_id' => $city->id, 'code' => $zipCodeData['zip_code1']]
                                    // Puedes agregar 'code2' si es necesario
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}
