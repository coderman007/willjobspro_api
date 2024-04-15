<?php

namespace Database\Seeders;

use App\Models\SkillCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtén las categorías para asociar habilidades a cada una
        $categories = SkillCategory::all();

        foreach ($categories as $category) {
            $skills = $this->getSkillsByCategory($category->name);

            foreach ($skills as $skill) {
                $category->skills()->create(['name' => $skill, 'description' => "Habilidad en $skill"]);
            }
        }
    }

    private function getSkillsByCategory($categoryName): array
    {
        // Define las habilidades para cada categoría
        $skillsByCategory = [
            'Business Management' => [
                'Strategic Planning',
                'Decision Making',
                'Project Management',
                'Leadership',
                'Financial Analysis',
            ],
            'Human Resources' => [
                'Recruitment',
                'Organizational Development',
                'Talent Management',
                'Labor Relations',
                'Training and Development',
            ],
            'Sales and Marketing' => [
                'Sales Strategies',
                'Digital Marketing',
                'Customer Development',
                'Market Research',
                'Effective Communication',
            ],
            'Accounting and Finance' => [
                'Tax Accounting',
                'Financial Analysis',
                'Budgeting',
                'Auditing',
                'Risk Management',
            ],
            'Customer Service' => [
                'Customer Support',
                'Problem Solving',
                'Empathetic Communication',
                'Complaint Management',
                'Customer Orientation',
            ],
            'Logistics and Supply Chain' => [
                'Inventory Management',
                'Route Optimization',
                'Logistics Coordination',
                'Quality Control',
                'Supply Chain Planning',
            ],
            'Education and Training' => [
                'Course Design',
                'Learning Facilitation',
                'Educational Evaluation',
                'Curriculum Development',
                'Educational Technologies',
            ],
            'Healthcare and Personal Care' => [
                'Primary Care',
                'Medical Diagnosis',
                'Care Planning',
                'Medical Records Management',
                'Patient Communication',
            ],
            'Fashion Design' => [
                'Pattern Design',
                'Fashion Trends',
                'Garment Making',
                'Fashion Illustration',
                'Textile Material Handling',
            ],
            'Architecture and Construction' => [
                'Architectural Design',
                'Urban Planning',
                'Construction Project Management',
                'Construction Techniques',
                'Construction Sustainability',
            ],
            'Environment and Sustainability' => [
                'Environmental Management',
                'Environmental Impact Assessment',
                'Renewable Energy',
                'Corporate Sustainability',
                'Natural Resource Conservation',
            ],
            'Research and Development' => [
                'Research Methodologies',
                'Product Development',
                'Market Research',
                'Technological Innovation',
                'Data Analysis',
            ],
            'Data Science' => [
                'Statistical Analysis',
                'Machine Learning',
                'Data Visualization',
                'Data Mining',
                'Python/R Programming',
            ],
            'Agriculture and Agribusiness' => [
                'Crop Management',
                'Agricultural Technology',
                'Agribusiness',
                'Agricultural Research',
                'Irrigation Systems',
            ],
            'Journalism and Communication' => [
                'Journalistic Writing',
                'Content Editing',
                'News Investigation',
                'Journalistic Photography',
                'Multimedia Communication',
            ],
            'Tourism and Hospitality' => [
                'Hotel Management',
                'Tourism Customer Service',
                'Event Organization',
                'Tourism Marketing',
                'Travel Planning',
            ],
            'Art and Entertainment' => [
                'Artistic Creation',
                'Audiovisual Production',
                'Graphic Design',
                'Stage Design',
                'Cultural Event Management',
            ],
            'Legal Services' => [
                'Legal Counseling',
                'Legal Research',
                'Legal Document Drafting',
                'Litigation',
                'Corporate Law',
            ],
            'Translation and Languages' => [
                'Specialized Translation',
                'Interpretation',
                'Foreign Language Proficiency',
                'Content Localization',
                'Linguistic Studies',
            ],
            'Psychology and Wellness' => [
                'Psychological Assessment',
                'Individual and Group Therapy',
                'Counseling',
                'Organizational Psychology',
                'Sports Psychology',
            ],
            'Market Research' => [
                'Data Analysis',
                'Market Research',
                'Applied Statistics',
                'Market Research Methodologies',
                'Market Segmentation',
            ],
            'Advertising and Public Relations' => [
                'Campaign Planning',
                'Media Relations',
                'Social Media Management',
                'Influencer Marketing',
                'Strategic Communication',
            ],
            'Civil Engineering' => [
                'Structural Design',
                'Engineering Project Management',
                'Surveying',
                'Environmental Impact Assessment in Construction Projects',
                'Infrastructure Design',
            ],
            'Renewable Energy' => [
                'Renewable Energy System Design',
                'Natural Resource Assessment',
                'Energy Efficiency',
                'Renewable Energy Project Management',
                'Innovation in Sustainable Technologies',
            ],
            'Recreation and Sports' => [
                'Sports Event Planning',
                'Sports Training',
                'Sports Facility Management',
                'Promotion of Active Lifestyles',
                'Coordination of Recreational Activities',
            ],
            'Financial Advisory' => [
                'Personal Financial Planning',
                'Investment Analysis',
                'Portfolio Management',
                'Retirement Planning',
                'Debt Reduction Strategies',
            ],
            'Politics and Public Affairs' => [
                'Political Analysis',
                'Electoral Campaigns',
                'Design and Implementation of Public Policies',
                'Government Relations',
                'Political Communication',
            ],
            'Community Development' => [
                'Community Planning',
                'Citizen Participation',
                'Local Socioeconomic Development',
                'Community Resource Management',
                'Community Empowerment',
            ],
            'Industrial Design' => [
                'Product Design',
                'Innovation in Industrial Design',
                'Rapid Prototyping',
                'Materials and Manufacturing Processes',
                'Ergonomic Design',
            ],
            'Business Consulting' => [
                'Business Diagnosis',
                'Process Optimization',
                'Business Strategy',
                'Continuous Improvement',
                'Change Management',
            ],
        ];


        return $skillsByCategory[$categoryName] ?? [];
    }
}
