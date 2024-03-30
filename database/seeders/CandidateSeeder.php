<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\User;
use App\Models\Language;
use App\Models\WorkExperience;
use App\Models\EducationHistory;
use App\Models\Skill;
use App\Models\SocialNetwork;
use Faker\Factory;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        // Crear una instancia de Faker
        $faker = Factory::create();

        // Obtener todos los usuarios que aún no son candidatos
        $users = User::whereDoesntHave('candidate')->get();

        // Verificar si hay usuarios disponibles que no sean candidatos
        if ($users->count() < 10) {
            // Si no hay suficientes usuarios disponibles, crea algunos usuarios nuevos
            User::factory()->count(10 - $users->count())->create()->each(function ($user) {
                $user->assignRole('candidate');
            });
        }

        // Obtener todos los usuarios actualizados
        $users = User::whereDoesntHave('candidate')->get();

        // Crear candidatos para los usuarios disponibles
        $users->each(function ($user) use ($faker) {
            $candidate = Candidate::factory()->create(['user_id' => $user->id]);

            // Asociar idiomas
            $languages = Language::inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($languages as $language) {
                $level = $faker->randomElement(['basic', 'intermediate', 'advanced', 'native']);
                $candidate->languages()->attach($language->id, ['level' => $level]);
            }

            // Crear historial académico
            EducationHistory::factory()->count(rand(1, 2))->create([
                'candidate_id' => $candidate->id,
                'institution' => $faker->company,
                'degree_title' => $faker->sentence(3),
                'field_of_study' => $faker->word,
                'start_date' => $faker->dateTimeBetween('-10 years', '-5 years'),
                'end_date' => $faker->dateTimeBetween('-4 years', 'now'),
            ]);

            // Crear experiencia laboral
            WorkExperience::factory()->count(rand(1, 2))->create([
                'candidate_id' => $candidate->id,
                'company' => $faker->company,
                'position' => $faker->jobTitle,
                'responsibility' => $faker->paragraph,
                'start_date' => $faker->dateTimeBetween('-5 years', '-2 years'),
                'end_date' => $faker->dateTimeBetween('-2 years', 'now'),
            ]);

            // Asociar habilidades
            $skills = Skill::inRandomOrder()->limit(3)->get();
            $candidate->skills()->attach($skills->pluck('id'));

        });
    }
}
