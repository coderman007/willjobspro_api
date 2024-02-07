<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        ]);
        $adminUser->assignRole('admin');

        // Crear 10 usuarios con rol 'company'
        $companyUsers = User::factory(10)->create();
        $companyUsers->each(function ($user) {
            $user->assignRole('company');
        });

        // Crear 10 usuarios con rol 'candidate'
        $candidateUsers = User::factory(10)->create();
        $candidateUsers->each(function ($user) {
            $user->assignRole('candidate');
        });
    }
}
