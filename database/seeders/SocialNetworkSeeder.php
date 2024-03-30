<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SocialNetwork;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class SocialNetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Obtener todos los usuarios
        $users = User::all();

        // Iterar sobre cada usuario y asociarle una o varias redes sociales
        foreach ($users as $user) {
            // Determinar cuántas redes sociales se asociarán a este usuario (entre 1 y 3)
            $numSocialNetworks = rand(1, 3);

            // Crear y asociar las redes sociales
            for ($i = 0; $i < $numSocialNetworks; $i++) {
                $socialNetwork = new SocialNetwork([
                    'url' => $faker->url, // Generar una URL aleatoria con Faker
                ]);

                // Guardar la relación
                $user->socialNetworks()->save($socialNetwork);
            }
        }
    }
}
