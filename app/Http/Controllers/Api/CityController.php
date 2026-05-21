<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Traits\ApiResponseTrait;

class CityController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $cities = City::where('is_active', true)->get();
        return $this->successResponse($cities, 'Cities retrieved');
    }
}
