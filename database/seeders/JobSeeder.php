<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use App\Models\Job;
use App\Models\JobType;
use App\Models\Language;
use App\Models\Skill;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = FakerFactory::create(); // Importación y creación de una instancia de Faker

        // Obtener todos los niveles académicos disponibles
        $educationLevels = EducationLevel::all();

        // Obtener todas las habilidades disponibles
        $skills = Skill::all();

        // Crear 10 ofertas de trabajo utilizando el factory
        Job::factory(10)->create()->each(function ($job) use ($faker, $educationLevels, $skills) {
            // Asociar idiomas
            $languages = Language::inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($languages as $language) {
                $level = $faker->randomElement(['basic', 'intermediate', 'advanced', 'native']); // Uso de Faker para generar el nivel
                $job->languages()->attach($language->id, ['level' => $level]);
            }

            // Asociar tipos de trabajo
            $jobTypes = JobType::inRandomOrder()->limit(rand(1, 2))->pluck('id');
            $job->jobTypes()->attach($jobTypes);

            // Asociar un nivel académico aleatorio a la oferta de trabajo
            $educationLevel = $educationLevels->random();
            $job->educationLevels()->attach($educationLevel);

            // Asociar habilidades específicas a la oferta de trabajo
            $jobSkills = $skills->random(rand(1, 5)); // Seleccionar un número aleatorio de habilidades
            foreach ($jobSkills as $skill) {
                $job->skills()->attach($skill);
            }
        });
    }
}
