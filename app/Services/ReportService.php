<?php


namespace App\Services;

use App\Models\User;
use App\Models\Report;
use App\Models\ReportImage;
use App\Models\StatusHistory;
use App\Helpers\TokenHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ReportService
{
    public function __construct(
        private AutoAssignmentService $autoAssignment,
        private PointsService $pointsService,
        private NotificationService $notificationService
    ) {}

    /**
     * Create new report
     */
    public function create(array $data, User $user, $images = null): Report{
       return DB::transaction(function () use ($data, $user, $images) {
            // Create report
            $report = Report::create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'severity' => $data['severity'],
                'city_id' => $data['city_id'],
                'category_id' => $data['category_id'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'address' => $data['address'] ?? null,
                'status' => 'pending',
                'tracking_token' => TokenHelper::generateTrackingToken(),
                'upvotes_count' => 0,
            ]);

            // Upload images

// Upload images
if ($images) {

    foreach ($images as $image) {

        $path = $image->store('reports/' . $report->id, 'public');

        ReportImage::create([
            'report_id' => $report->id,
            'image_path' => $path,
            'type' => 'before',
        ]);
    }
}

            // Log status history
            StatusHistory::create([
                'report_id' => $report->id,
                'from_status' => 'pending',
                'to_status' => 'pending',
                'changed_by' => $user->id,
                'note' => 'Report submitted by user',
            ]);

            // Award points
            $this->pointsService->award($user, 'report_submitted', $report);

            // Auto assign
            $this->autoAssignment->assignReport($report);

            return $report->load(['images', 'city', 'category', 'user']);
        });
    }

    /**
     * Update report status
     */
    public function updateStatus(Report $report, string $status, ?string $note = null, ?User $changedBy = null): Report
    {
        $oldStatus = $report->status;

        $report->update([
            'status' => $status,
            'started_at' => $status === 'in_progress' ? now() : $report->started_at,
            'resolved_at' => $status === 'resolved' ? now() : $report->resolved_at,
        ]);

        StatusHistory::create([
            'report_id' => $report->id,
            'from_status' => $oldStatus,
            'to_status' => $status,
            'changed_by' => $changedBy?->id,
            'note' => $note ?? "Status changed from {$oldStatus} to {$status}",
        ]);

        // Notify user
        $this->notificationService->reportStatusChanged($report);

        // Award points if resolved
        if ($status === 'resolved') {
            $this->pointsService->award($report->user, 'report_resolved', $report);
        }

        return $report->fresh();
    }

    /**
     * Upload after image
     */
    public function uploadAfterImage(Report $report, $image): ReportImage
    {
        $path = $image->store('reports/' . $report->id, 'public');

        return ReportImage::create([
            'report_id' => $report->id,
            'image_path' => $path,
            'type' => 'after',
        ]);
    }

    /**
     * Get report by tracking token
     */
    public function trackByToken(string $token): ?Report
    {
        return Report::where('tracking_token', $token)
            ->with(['images', 'city', 'category', 'assignedCompany', 'statusHistory.changer'])
            ->first();
    }
}
