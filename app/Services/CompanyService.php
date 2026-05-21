<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Report;

class CompanyService
{
    /**
     * Get company performance data
     */
    public function getPerformance(Company $company, int $days = 30): array
    {
        $reports = $company->reports()
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        return [
            'period_days' => $days,
            'total_assigned' => $reports->count(),
            'resolved' => $reports->where('status', 'resolved')->count(),
            'in_progress' => $reports->where('status', 'in_progress')->count(),
            'rejected' => $reports->where('status', 'rejected')->count(),
            'avg_resolution_hours' => $this->calculateAvgResolution($reports),
            'severity_breakdown' => [
                'critical' => $reports->where('severity', 'critical')->count(),
                'high' => $reports->where('severity', 'high')->count(),
                'medium' => $reports->where('severity', 'medium')->count(),
                'low' => $reports->where('severity', 'low')->count(),
            ],
            'ratings_distribution' => $this->getRatingsDistribution($company),
        ];
    }

    /**
     * Get company active reports
     */
    public function getActiveReports(Company $company): array
    {
        return $company->reports()
            ->whereIn('status', ['assigned', 'in_progress'])
            ->with(['images', 'city', 'category'])
            ->orderBy('severity', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Calculate average resolution time
     */
    private function calculateAvgResolution($reports): float
    {
        $resolved = $reports->where('status', 'resolved');

        if ($resolved->isEmpty()) {
            return 0;
        }

        $totalHours = 0;
        foreach ($resolved as $report) {
            $totalHours += $report->created_at->diffInHours($report->resolved_at);
        }

        return round($totalHours / $resolved->count(), 1);
    }

    /**
     * Get ratings distribution
     */
    private function getRatingsDistribution(Company $company): array
    {
        $ratings = $company->ratings()->selectRaw('rating, COUNT(*) as count')->groupBy('rating')->get();

        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($ratings as $r) {
            $distribution[$r->rating] = $r->count;
        }

        return $distribution;
    }
}
