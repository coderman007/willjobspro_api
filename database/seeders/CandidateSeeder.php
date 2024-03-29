<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\EducationLevel;
use App\Models\Language;
use App\Models\Skill;
use App\Models\SocialNetwork;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create(); // Importación y creación de una instancia de Faker

        $users = \App\Models\User::role('candidate')->get();

        foreach ($users as $user) {
            $existingCandidate = Candidate::where('user_id', $user->id)->first();

            if (!$existingCandidate) {
                $candidate = Candidate::factory()->create([
                    'user_id' => $user->id,
                ]);

                $educationLevels = EducationLevel::inRandomOrder()->limit(rand(1, 5))->get();
                $languages = Language::inRandomOrder()->limit(rand(1, 3))->get();
                $skills = Skill::inRandomOrder()->limit(3)->get();
                $socialNetworks = SocialNetwork::inRandomOrder()->limit(rand(1, 3))->get();

                foreach ($languages as $language) {
                    $level = $faker->randomElement(['basic', 'intermediate', 'advanced', 'native']); // Uso de Faker para generar el nivel
                    $candidate->languages()->attach($language->id, ['level' => $level]);
                }

                $candidate->socialNetworks()->attach($socialNetworks->pluck('id'));
                $candidate->educationLevels()->attach($educationLevels->pluck('id'));
                $candidate->skills()->attach($skills->pluck('id'));
            }
        }
    }
}

