<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobCategory;

class JobCategorySeeder extends Seeder
{
    public function run(): void
    {
        JobCategory::create(['name' => 'Software Development', 'description' => 'Jobs related to software development']);
        JobCategory::create(['name' => 'Marketing', 'description' => 'Jobs related to marketing']);
        JobCategory::create(['name' => 'Graphic Design', 'description' => 'Jobs related to graphic design']);
        JobCategory::create(['name' => 'Data Analysis', 'description' => 'Jobs related to data analysis']);
        JobCategory::create(['name' => 'Customer Support', 'description' => 'Jobs related to customer support']);
        JobCategory::create(['name' => 'Sales', 'description' => 'Jobs related to sales']);
        JobCategory::create(['name' => 'Human Resources', 'description' => 'Jobs related to human resources']);
        JobCategory::create(['name' => 'Finance', 'description' => 'Jobs related to finance']);
        JobCategory::create(['name' => 'Project Management', 'description' => 'Jobs related to project management']);
        JobCategory::create(['name' => 'Healthcare', 'description' => 'Jobs related to healthcare']);
        JobCategory::create(['name' => 'Education', 'description' => 'Jobs related to education']);
        JobCategory::create(['name' => 'E-commerce', 'description' => 'Jobs related to e-commerce']);
        JobCategory::create(['name' => 'Quality Assurance', 'description' => 'Jobs related to quality assurance']);
        JobCategory::create(['name' => 'Content Creation', 'description' => 'Jobs related to content creation']);
        JobCategory::create(['name' => 'Legal', 'description' => 'Jobs related to legal services']);
        JobCategory::create(['name' => 'Manufacturing', 'description' => 'Jobs related to manufacturing']);
        JobCategory::create(['name' => 'Research and Development', 'description' => 'Jobs related to research and development']);
        JobCategory::create(['name' => 'Social Media', 'description' => 'Jobs related to social media']);
    }
}
