<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = \App\Models\User::role('company')->get();

        foreach ($users as $user) {
            // Verificar si ya existe una compañía para este usuario
            $existingCompany = Company::where('user_id', $user->id)->first();

            if (!$existingCompany) {
                Company::factory()->create([
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
