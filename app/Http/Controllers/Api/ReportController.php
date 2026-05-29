<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\StoreReportRequest;
use App\Http\Requests\Report\UpdateReportRequest;
use App\Models\Report;
use App\Services\ReportService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private ReportService $reportService) {}

    public function index(Request $request)
    {
        $query = Report::with(['images', 'city', 'category', 'user', 'assignedCompany']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        $reports = $query->latest()->paginate($request->per_page ?? 10);
        return $this->paginatedResponse($reports, 'Reports retrieved');
    }

    public function store(StoreReportRequest $request)
    {
        $report = $this->reportService->create(
    $request->validated(),
    $request->user(),
    $request->file('images')
);
        return $this->successResponse($report, 'Report submitted successfully', 201);
    }

    public function show(Report $report)
    {
        $report->load(['images', 'city', 'category', 'user', 'assignedCompany', 'statusHistory.changer', 'upvotes']);
        return $this->successResponse($report, 'Report retrieved');
    }

    public function updateStatus(UpdateReportRequest $request, Report $report)
    {
        $updated = $this->reportService->updateStatus(
            $report,
            $request->status,
            $request->note,
            $request->user()
        );

        if ($request->hasFile('after_image')) {
            $this->reportService->uploadAfterImage($report, $request->file('after_image'));
        }

        return $this->successResponse($updated, 'Status updated successfully');
    }

    public function myReports(Request $request)
    {
        $reports = $request->user()
            ->reports()
            ->with(['images', 'city', 'category'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($reports, 'My reports retrieved');
    }

    public function track(string $token)
    {
        $report = $this->reportService->trackByToken($token);

        if (!$report) {
            return $this->errorResponse('Report not found', 404);
        }

        return $this->successResponse($report, 'Report tracked successfully');
    }
}
