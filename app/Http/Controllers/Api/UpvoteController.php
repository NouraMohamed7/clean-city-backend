<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Upvote;
use App\Services\PointsService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class UpvoteController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private PointsService $pointsService) {}

    public function toggle(Request $request, Report $report)
    {
        $user = $request->user();

        $existing = Upvote::where('user_id', $user->id)
            ->where('report_id', $report->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $report->decrement('upvotes_count');
            return $this->successResponse(['upvoted' => false], 'Upvote removed');
        }

        Upvote::create([
            'user_id' => $user->id,
            'report_id' => $report->id,
        ]);

        $report->increment('upvotes_count');
        $this->pointsService->award($report->user, 'upvote_received', $report);

        return $this->successResponse(['upvoted' => true], 'Upvote added');
    }
}
