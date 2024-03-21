<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\ZipCode;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario administrador
        $adminUser = User::factory()->create([
            'name' => 'jaime Sierra',
            'email' => 'coderman1980@gmail.com',
            'password' => bcrypt('Coderman1980$'),
            'country_id' => 5,
            'state_id' => 5,
            'city_id' => 5,
            'zip_code_id' => 5,
        ]);
        $adminUser->assignRole('admin');

        // Crear 10 usuarios con rol 'company'
        $companyUsers = User::factory(10)->create();
        $companyUsers->each(function ($user) {
            $user->assignRole('company');
            $this->associateLocation($user);
        });

        // Crear 10 usuarios con rol 'candidate'
        $candidateUsers = User::factory(10)->create();
        $candidateUsers->each(function ($user) {
            $user->assignRole('candidate');
            $this->associateLocation($user);
        });
    }

    /**
     * Associate location (country, state, city, zip code) with the user.
     *
     * @param User $user
     * @return void
     */
    private function associateLocation(User $user): void
    {
        // Asociar ubicación aleatoria (solo como ejemplo)
        $country = Country::inRandomOrder()->first();
        if ($country) {
            $state = $country->states()->inRandomOrder()->first();
            if ($state) {
                $city = $state->cities()->inRandomOrder()->first();
                if ($city) {
                    $zipCode = $city->zipCodes()->inRandomOrder()->first();
                    if ($zipCode) {
                        // Actualizar los campos de ubicación del usuario
                        $user->update([
                            'country_id' => $country->id,
                            'state_id' => $state->id,
                            'city_id' => $city->id,
                            'zip_code_id' => $zipCode->id,
                        ]);
                    }
                }
            }
        }
    }
}
