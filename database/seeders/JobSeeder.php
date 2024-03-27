<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use App\Models\Job;
use App\Models\Language;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Crear 10 ofertas de trabajo
        $jobs = Job::factory(10)->create();

        // Para cada oferta de trabajo, asociar niveles de estudio y lenguajes requeridos
        foreach ($jobs as $job) {
            // Obtener un número aleatorio de niveles de estudio (entre 1 y 5)
            $educationLevels = EducationLevel::inRandomOrder()->limit(rand(1, 5))->get();

            // Obtener un número aleatorio de lenguajes requeridos (entre 1 y 3)
            $languages = Language::inRandomOrder()->limit(rand(1, 3))->get();

            // Asociar los niveles de estudio y los lenguajes requeridos a la oferta de trabajo actual
            $job->educationLevels()->attach($educationLevels->pluck('id'));
            $job->languages()->attach($languages->pluck('id'));
        }
    }
}
