<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rating\StoreRatingRequest;
use App\Models\Report;
use App\Models\Rating;
use App\Services\PointsService;
use App\Traits\ApiResponseTrait;

class RatingController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private PointsService $pointsService) {}

    public function store(StoreRatingRequest $request, Report $report)
    {
        if ($report->status !== 'resolved') {
            return $this->errorResponse('Can only rate resolved reports', 422);
        }

        if ($report->user_id !== $request->user()->id) {
            return $this->errorResponse('You can only rate your own reports', 403);
        }

        if ($report->rating) {
            return $this->errorResponse('Report already rated', 422);
        }

        $rating = Rating::create([
            'report_id' => $report->id,
            'user_id' => $request->user()->id,
            'company_id' => $report->assigned_company_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $company = $report->assignedCompany;
        $avg = $company->ratings()->avg('rating');
        $company->update(['rating_average' => round($avg, 1)]);

        $this->pointsService->award($request->user(), 'rating_given', $report);

        return $this->successResponse($rating, 'Rating submitted', 201);
    }
}
