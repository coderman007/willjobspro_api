<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Language;
use App\Models\WorkExperience;
use App\Models\EducationHistory;
use App\Models\Skill;
use App\Models\EducationLevel;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener una instancia de Faker
        $faker = FakerFactory::create();

        // Obtener todos los usuarios con el rol de 'candidato' que no tienen un candidato asociado
        $users = \App\Models\User::role('candidate')->whereDoesntHave('candidate')->get();

        // Para cada usuario con el rol de 'candidato' y sin candidato asociado
        foreach ($users as $user) {
            // Crear un nuevo candidato para el usuario
            $candidate = Candidate::factory()->create(['user_id' => $user->id]);

            // Asociar idiomas
            $languages = Language::inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($languages as $language) {
                $level = $faker->randomElement(['basic', 'intermediate', 'advanced', 'native']);
                $candidate->languages()->attach($language->id, ['level' => $level]);
            }

            // Obtener todos los niveles de educación
            $educationLevels = EducationLevel::all();

            // Crear entre uno y tres historiales académicos
            $numberOfEducationHistories = rand(1, 3);
            for ($i = 0; $i < $numberOfEducationHistories; $i++) {
                // Obtener un nivel de educación aleatorio
                $educationLevel = EducationLevel::inRandomOrder()->first();

                // Crear historial académico
                EducationHistory::factory()->create([
                    'candidate_id' => $candidate->id,
                    'education_level_id' => $educationLevel->id,
                    'institution' => $faker->company,
                    'field_of_study' => $faker->word,
                    'start_date' => $faker->dateTimeBetween('-10 years', '-5 years'),
                    'end_date' => $faker->dateTimeBetween('-4 years', 'now'),
                ]);
            }

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
        }
    }
}
