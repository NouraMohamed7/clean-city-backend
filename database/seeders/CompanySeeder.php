<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'user_id' => 7, // company1@example.com
                'name' => 'النظافة المثالية',
                'city_id' => 1,
                'coverage_areas' => json_encode(['المعادي', 'مصر الجديدة', 'التجمع']),
                'phone' => '01234567890',
                'email' => 'info@ideal-clean.com',
                'rating_average' => 4.5,
                'total_resolved' => 45,
                'is_active' => true,
            ],
            [
                'user_id' => 8, // company2@example.com
                'name' => 'بيئة نظيفة',
                'city_id' => 2,
                'coverage_areas' => json_encode(['سموحة', 'العصافرة', 'المنتزه']),
                'phone' => '01234567891',
                'email' => 'contact@clean-env.com',
                'rating_average' => 3.8,
                'total_resolved' => 28,
                'is_active' => true,
            ],
            [
                'user_id' => 9, // company3@example.com
                'name' => 'الحل الأخضر',
                'city_id' => 3,
                'coverage_areas' => json_encode(['الدقي', 'المهندسين', 'العجوزة']),
                'phone' => '01234567892',
                'email' => 'hello@green-sol.com',
                'rating_average' => 4.2,
                'total_resolved' => 33,
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}