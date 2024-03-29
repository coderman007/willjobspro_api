<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\SocialNetwork;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = FakerFactory::create(); // Importación y creación de una instancia de Faker

        $users = \App\Models\User::role('company')->get();

        foreach ($users as $user) {
            // Verificar si ya existe una compañía para este usuario
            $existingCompany = Company::where('user_id', $user->id)->first();

            if (!$existingCompany) {
                $company = Company::factory()->create([
                    'user_id' => $user->id,
                ]);

                $socialNetworks = SocialNetwork::inRandomOrder()->limit(rand(1, 3))->get();

                $company->socialNetworks()->attach($socialNetworks->pluck('id'));

            }
        }
    }
}
