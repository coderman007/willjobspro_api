<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobType;

class JobTypeSeeder extends Seeder
{
    public function run()
    {
        JobType::create(['name' => 'Full-time', 'description' => 'Full-time job', 'status' => 'Active']);
        JobType::create(['name' => 'Part-time', 'description' => 'Part-time job', 'status' => 'Active']);
        JobType::create(['name' => 'Remote', 'description' => 'Remote work', 'status' => 'Active']);
        JobType::create(['name' => 'Freelance', 'description' => 'Freelance job', 'status' => 'Active']);
        JobType::create(['name' => 'Contract', 'description' => 'Contractual job', 'status' => 'Active']);
        JobType::create(['name' => 'Internship', 'description' => 'Internship opportunity', 'status' => 'Active']);
        JobType::create(['name' => 'Temporary', 'description' => 'Temporary job', 'status' => 'Active']);
        JobType::create(['name' => 'Consulting', 'description' => 'Consulting position', 'status' => 'Active']);
        JobType::create(['name' => 'Volunteer', 'description' => 'Volunteer work', 'status' => 'Active']);
        JobType::create(['name' => 'Seasonal', 'description' => 'Seasonal job', 'status' => 'Active']);
        JobType::create(['name' => 'Entry Level', 'description' => 'Entry-level position', 'status' => 'Active']);
        JobType::create(['name' => 'Experienced', 'description' => 'Experienced professional job', 'status' => 'Active']);
        JobType::create(['name' => 'Supervisor', 'description' => 'Supervisory role', 'status' => 'Active']);
        JobType::create(['name' => 'Managerial', 'description' => 'Managerial position', 'status' => 'Active']);
        JobType::create(['name' => 'Executive', 'description' => 'Executive role', 'status' => 'Active']);
        JobType::create(['name' => 'Temporary-to-Permanent', 'description' => 'Temporary-to-permanent job', 'status' => 'Active']);
        JobType::create(['name' => 'Co-op', 'description' => 'Cooperative education position', 'status' => 'Active']);
    }
}
