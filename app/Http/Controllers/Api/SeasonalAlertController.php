<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SeasonalAnalysisService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SeasonalAlertController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private SeasonalAnalysisService $seasonal) {}

    public function index()
    {
        $alerts = $this->seasonal->getAlerts();
        return $this->successResponse($alerts, 'Seasonal alerts retrieved');
    }

    public function store(Request $request)
    {
        $alert = $this->seasonal->createAlert($request->validate([
            'city_id' => 'required|exists:cities,id',
            'month' => 'required|integer|between:1,12',
            'event_name' => 'required|string|max:255',
            'predicted_increase_percent' => 'required|integer|min:0',
            'recommendation' => 'required|string',
        ]));

        return $this->successResponse($alert, 'Alert created', 201);
    }
}
