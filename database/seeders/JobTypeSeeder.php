<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobType;

class JobTypeSeeder extends Seeder
{
    public function run()
    {
        JobType::create(['name' => 'Full-time', 'description' => 'Full-time job']);
        JobType::create(['name' => 'Part-time', 'description' => 'Part-time job']);
        JobType::create(['name' => 'Remote', 'description' => 'Remote work']);
        JobType::create(['name' => 'Freelance', 'description' => 'Freelance job']);
        JobType::create(['name' => 'Contract', 'description' => 'Contractual job']);
        JobType::create(['name' => 'Internship', 'description' => 'Internship opportunity']);
        JobType::create(['name' => 'Temporary', 'description' => 'Temporary job']);
        JobType::create(['name' => 'Consulting', 'description' => 'Consulting position']);
        JobType::create(['name' => 'Volunteer', 'description' => 'Volunteer work']);
        JobType::create(['name' => 'Seasonal', 'description' => 'Seasonal job']);
        JobType::create(['name' => 'Entry Level', 'description' => 'Entry-level position']);
        JobType::create(['name' => 'Experienced', 'description' => 'Experienced professional job']);
        JobType::create(['name' => 'Supervisor', 'description' => 'Supervisory role']);
        JobType::create(['name' => 'Managerial', 'description' => 'Managerial position']);
        JobType::create(['name' => 'Executive', 'description' => 'Executive role']);
        JobType::create(['name' => 'Temporary-to-Permanent', 'description' => 'Temporary-to-permanent job']);
        JobType::create(['name' => 'Co-op', 'description' => 'Cooperative education position']);
    }
}
