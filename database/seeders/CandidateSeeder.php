<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\EducationLevel;
use App\Models\Language;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
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

                $candidate->educationLevels()->attach($educationLevels->pluck('id'));
                $candidate->languages()->attach($languages->pluck('id'));
                $candidate->skills()->attach($skills->pluck('id'));
            }
        }
    }
}
