<?php

namespace App\Services;

use App\Models\User;
use App\Models\Report;
use App\Models\Notification;

class NotificationService
{
    /**
     * Send notification to user
     */
    public function send(User $user, string $title, string $message, string $type = 'general', Report $report = null): void
    {
        Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'report_id' => $report?->id,
            'is_read' => false,
        ]);
    }

    /**
     * Send report status change notification
     */
    public function reportStatusChanged(Report $report): void
    {
        $data = [
            'assigned' => [
                'title' => 'Report Assigned',
                'message' => "Your report \"{$report->title}\" has been assigned to a cleanup company.",
                'type' => 'report_assigned',
            ],
            'in_progress' => [
                'title' => 'Work Started',
                'message' => "Work has started on your report \"{$report->title}\".",
                'type' => 'report_in_progress',
            ],
            'resolved' => [
                'title' => 'Resolved!',
                'message' => "Your report \"{$report->title}\" has been resolved! Please rate the service.",
                'type' => 'report_resolved',
            ],
            'rejected' => [
                'title' => 'Report Rejected',
                'message' => "Your report \"{$report->title}\" has been rejected.",
                'type' => 'report_rejected',
            ],
        ];

        $info = $data[$report->status] ?? null;

        if ($info && $report->user) {
            $this->send($report->user, $info['title'], $info['message'], $info['type'], $report);
        }
    }

    /**
     * Send points notification
     */
    public function pointsEarned(User $user, int $points, string $reason): void
    {
        $this->send(
            $user,
            'Points Earned!',
            "You earned {$points} points for {$reason}.",
            'points_earned'
        );
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead(User $user): void
    {
        $user->notifications()->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
