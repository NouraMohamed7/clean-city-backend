<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            [
                'name' => 'القاهرة',
                'name_en' => 'Cairo',
                'latitude' => 30.0444,
                'longitude' => 31.2357,
                'is_active' => true,
            ],
            [
                'name' => 'الإسكندرية',
                'name_en' => 'Alexandria',
                'latitude' => 31.2001,
                'longitude' => 29.9187,
                'is_active' => true,
            ],
            [
                'name' => 'الجيزة',
                'name_en' => 'Giza',
                'latitude' => 30.0131,
                'longitude' => 31.2089,
                'is_active' => true,
            ],
            [
                'name' => 'المنصورة',
                'name_en' => 'Mansoura',
                'latitude' => 31.0409,
                'longitude' => 31.3785,
                'is_active' => true,
            ],
            [
                'name' => 'طنطا',
                'name_en' => 'Tanta',
                'latitude' => 30.7865,
                'longitude' => 31.0004,
                'is_active' => true,
            ],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}