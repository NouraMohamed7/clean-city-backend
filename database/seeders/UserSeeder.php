<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@cleancity.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'city_id' => 1,
            'total_points' => 0,
            'is_active' => true,
        ]);

        // Citizens
        $citizens = [
            ['name' => 'أحمد محمد', 'email' => 'ahmed@example.com', 'city_id' => 1],
            ['name' => 'سارة علي', 'email' => 'sara@example.com', 'city_id' => 1],
            ['name' => 'محمد عبدالله', 'email' => 'mohamed@example.com', 'city_id' => 2],
            ['name' => 'فاطمة أحمد', 'email' => 'fatma@example.com', 'city_id' => 2],
            ['name' => 'خالد سعيد', 'email' => 'khaled@example.com', 'city_id' => 3],
        ];

        foreach ($citizens as $index => $citizen) {
            User::create([
                'name' => $citizen['name'],
                'email' => $citizen['email'],
                'password' => Hash::make('password123'),
                'role' => 'user',
                'city_id' => $citizen['city_id'],
                'total_points' => ($index + 1) * 15,
                'is_active' => true,
            ]);
        }

        // Company Users (بدون company data لسه)
        $companyUsers = [
            ['name' => 'شركة النظافة الأولى', 'email' => 'company1@example.com', 'city_id' => 1],
            ['name' => 'شركة النظافة الثانية', 'email' => 'company2@example.com', 'city_id' => 2],
            ['name' => 'شركة النظافة الثالثة', 'email' => 'company3@example.com', 'city_id' => 3],
        ];

        foreach ($companyUsers as $companyUser) {
            User::create([
                'name' => $companyUser['name'],
                'email' => $companyUser['email'],
                'password' => Hash::make('password123'),
                'role' => 'company',
                'city_id' => $companyUser['city_id'],
                'total_points' => 0,
                'is_active' => true,
            ]);
        }
    }
}