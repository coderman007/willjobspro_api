<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidate;
use App\Models\Skill;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::role('candidate')->get();

        foreach ($users as $user) {
            // Verificar si ya existe un candidato para este usuario
            $existingCandidate = Candidate::where('user_id', $user->id)->first();

            if (!$existingCandidate) {
                $candidate = Candidate::factory()->create([
                    'user_id' => $user->id,
                ]);

                $skills = Skill::inRandomOrder()->limit(3)->get(); // Asociar 3 habilidades al azar
                $candidate->skills()->attach($skills->pluck('id'));
            }
        }
    }
}
