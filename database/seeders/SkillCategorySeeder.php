<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SkillCategory;

class SkillCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Business Management', 'description' => 'Skills related to business management.'],
            ['name' => 'Human Resources', 'description' => 'Skills related to human resources management.'],
            ['name' => 'Sales and Marketing', 'description' => 'Skills related to sales and marketing.'],
            ['name' => 'Accounting and Finance', 'description' => 'Skills related to accounting and finance.'],
            ['name' => 'Customer Service', 'description' => 'Skills related to customer service.'],
            ['name' => 'Logistics and Supply Chain', 'description' => 'Skills related to logistics and supply chain.'],
            ['name' => 'Education and Training', 'description' => 'Skills related to education and training.'],
            ['name' => 'Health and Personal Care', 'description' => 'Skills related to health and personal care.'],
            ['name' => 'Fashion Design', 'description' => 'Skills related to fashion design.'],
            ['name' => 'Architecture and Construction', 'description' => 'Skills related to architecture and construction.'],
            ['name' => 'Environment and Sustainability', 'description' => 'Skills related to environment and sustainability.'],
            ['name' => 'Research and Development', 'description' => 'Skills related to research and development.'],
            ['name' => 'Data Science', 'description' => 'Skills related to data science.'],
            ['name' => 'Agriculture and Agribusiness', 'description' => 'Skills related to agriculture and agribusiness.'],
            ['name' => 'Journalism and Communication', 'description' => 'Skills related to journalism and communication.'],
            ['name' => 'Tourism and Hospitality', 'description' => 'Skills related to tourism and hospitality.'],
            ['name' => 'Art and Entertainment', 'description' => 'Skills related to art and entertainment.'],
            ['name' => 'Legal Services', 'description' => 'Skills related to legal services.'],
            ['name' => 'Translation and Languages', 'description' => 'Skills related to translation and languages.'],
            ['name' => 'Psychology and Well-being', 'description' => 'Skills related to psychology and well-being.'],
            ['name' => 'Market Research', 'description' => 'Skills related to market research.'],
            ['name' => 'Advertising and Public Relations', 'description' => 'Skills related to advertising and public relations.'],
            ['name' => 'Civil Engineering', 'description' => 'Skills related to civil engineering.'],
            ['name' => 'Renewable Energy', 'description' => 'Skills related to renewable energy.'],
            ['name' => 'Recreation and Sports', 'description' => 'Skills related to recreation and sports.'],
            ['name' => 'Financial Advisory', 'description' => 'Skills related to financial advisory.'],
            ['name' => 'Politics and Public Affairs', 'description' => 'Skills related to politics and public affairs.'],
            ['name' => 'Community Development', 'description' => 'Skills related to community development.'],
            ['name' => 'Industrial Design', 'description' => 'Skills related to industrial design.'],
            ['name' => 'Business Consulting', 'description' => 'Skills related to business consulting.'],
        ];


        foreach ($categories as $category) {
            SkillCategory::firstOrCreate($category);
        }
    }
}
