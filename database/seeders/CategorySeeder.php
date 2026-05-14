<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'نفايات عامة',
                'icon' => '🗑️',
                'color' => '#6B7280',
                'is_active' => true,
            ],
            [
                'name' => 'مخلفات بناء',
                'icon' => '🏗️',
                'color' => '#F59E0B',
                'is_active' => true,
            ],
            [
                'name' => 'نفايات طبية',
                'icon' => '🏥',
                'color' => '#EF4444',
                'is_active' => true,
            ],
            [
                'name' => 'نفايات بلاستيك',
                'icon' => '♻️',
                'color' => '#10B981',
                'is_active' => true,
            ],
            [
                'name' => 'زيوت ومخلفات صناعية',
                'icon' => '⚠️',
                'color' => '#8B5CF6',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}