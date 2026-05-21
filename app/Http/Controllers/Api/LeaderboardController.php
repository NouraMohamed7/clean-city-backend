<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PointsService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private PointsService $pointsService) {}

    public function index(Request $request)
    {
        $leaderboard = $this->pointsService->getLeaderboard(
            $request->limit ?? 50,
            $request->city_id
        );

        return $this->successResponse($leaderboard, 'Leaderboard retrieved');
    }
}
