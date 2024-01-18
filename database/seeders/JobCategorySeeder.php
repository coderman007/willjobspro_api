<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobCategory;

class JobCategorySeeder extends Seeder
{
    public function run()
    {
        JobCategory::create(['name' => 'Software Development', 'description' => 'Jobs related to software development', 'status' => 'Active']);
        JobCategory::create(['name' => 'Marketing', 'description' => 'Jobs related to marketing', 'status' => 'Active']);
        JobCategory::create(['name' => 'Graphic Design', 'description' => 'Jobs related to graphic design', 'status' => 'Active']);
        JobCategory::create(['name' => 'Data Analysis', 'description' => 'Jobs related to data analysis', 'status' => 'Active']);
        JobCategory::create(['name' => 'Customer Support', 'description' => 'Jobs related to customer support', 'status' => 'Active']);
        JobCategory::create(['name' => 'Sales', 'description' => 'Jobs related to sales', 'status' => 'Active']);
        JobCategory::create(['name' => 'Human Resources', 'description' => 'Jobs related to human resources', 'status' => 'Active']);
        JobCategory::create(['name' => 'Finance', 'description' => 'Jobs related to finance', 'status' => 'Active']);
        JobCategory::create(['name' => 'Project Management', 'description' => 'Jobs related to project management', 'status' => 'Active']);
        JobCategory::create(['name' => 'Healthcare', 'description' => 'Jobs related to healthcare', 'status' => 'Active']);
        JobCategory::create(['name' => 'Education', 'description' => 'Jobs related to education', 'status' => 'Active']);
        JobCategory::create(['name' => 'E-commerce', 'description' => 'Jobs related to e-commerce', 'status' => 'Active']);
        JobCategory::create(['name' => 'Quality Assurance', 'description' => 'Jobs related to quality assurance', 'status' => 'Active']);
        JobCategory::create(['name' => 'Content Creation', 'description' => 'Jobs related to content creation', 'status' => 'Active']);
        JobCategory::create(['name' => 'Legal', 'description' => 'Jobs related to legal services', 'status' => 'Active']);
        JobCategory::create(['name' => 'Manufacturing', 'description' => 'Jobs related to manufacturing', 'status' => 'Active']);
        JobCategory::create(['name' => 'Research and Development', 'description' => 'Jobs related to research and development', 'status' => 'Active']);
        JobCategory::create(['name' => 'Social Media', 'description' => 'Jobs related to social media', 'status' => 'Active']);
    }
}
