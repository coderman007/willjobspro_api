<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use App\Models\JobType;
use App\Models\Language;
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

        // Crear 10 ofertas de trabajo utilizando el factory
        \App\Models\Job::factory(10)->create()->each(function ($job) use ($faker) {
            // Asociar idiomas
            $languages = Language::inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($languages as $language) {
                $level = $faker->randomElement(['basic', 'intermediate', 'advanced', 'native']); // Uso de Faker para generar el nivel
                $job->languages()->attach($language->id, ['level' => $level]);
            }

            // Asociar niveles de estudio
            $educationLevels = EducationLevel::inRandomOrder()->limit(rand(1, 2))->pluck('id');
            $job->educationLevels()->attach($educationLevels);

            // Asociar tipos de trabajo
            $jobTypes = JobType::inRandomOrder()->limit(rand(1, 2))->pluck('id');
            $job->jobTypes()->attach($jobTypes);
        });
    }
}
