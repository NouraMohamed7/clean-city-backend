<?php

namespace App\Services;

use App\Models\User;
use App\Models\Report;
use App\Models\UserPoint;

class PointsService
{
    const POINTS = [
        'report_submitted' => 10,
        'report_resolved' => 50,
        'upvote_received' => 5,
        'rating_given' => 3,
    ];

    const DESCRIPTIONS = [
        'report_submitted' => 'Points for submitting a new report',
        'report_resolved' => 'Points for report resolution',
        'upvote_received' => 'Points for receiving an upvote',
        'rating_given' => 'Points for rating service',
    ];

    /**
     * Award points to user
     */
    public function award(User $user, string $reason, Report $report = null): void
    {
        $points = self::POINTS[$reason] ?? 0;
        if ($points === 0) return;

        UserPoint::create([
            'user_id' => $user->id,
            'points' => $points,
            'reason' => $reason,
            'report_id' => $report?->id,
            'description' => self::DESCRIPTIONS[$reason] ?? '',
        ]);

        $user->increment('total_points', $points);
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(int $limit = 50, ?int $cityId = null): array
    {
        $query = User::where('role', 'user')
            ->where('is_active', true)
            ->orderBy('total_points', 'desc');

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        $users = $query->take($limit)->get();

        return [
            'users' => $users->map(fn($u, $i) => [
                'rank' => $i + 1,
                'name' => $u->name,
                'city' => $u->city?->name,
                'points' => $u->total_points,
                'reports_count' => $u->reports()->count(),
                'avatar' => $u->avatar,
            ]),
            'total_participants' => User::where('role', 'user')->count(),
        ];
    }
}
