<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use App\Models\Company;
use App\Models\StatusHistory;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get admin dashboard stats
     */
    public function getAdminStats(): array
    {
        return [
            'total_reports' => Report::count(),
            'active_reports' => Report::whereIn('status', ['pending', 'assigned', 'in_progress'])->count(),
            'resolved_today' => Report::where('status', 'resolved')->whereDate('resolved_at', today())->count(),
            'total_citizens' => User::where('role', 'user')->count(),
            'total_companies' => Company::where('is_active', true)->count(),
            'avg_resolution_hours' => $this->averageResolutionTime(),
            'reports_trend' => $this->getReportsTrend(30),
        ];
    }

    /**
     * Get company stats
     */
    public function getCompanyStats(Company $company): array
    {
        $reports = $company->reports();

        return [
            'total_assigned' => $reports->count(),
            'in_progress' => $reports->where('status', 'in_progress')->count(),
            'resolved_today' => $reports->where('status', 'resolved')->whereDate('resolved_at', today())->count(),
            'avg_resolution_hours' => $this->averageResolutionTime($company->id),
            'rating_average' => $company->rating_average,
            'total_resolved' => $company->total_resolved,
        ];
    }

    /**
     * Get reports trend
     */
    public function getReportsTrend(int $days = 30): array
    {
        $data = [];
        $end = Carbon::now();
        $start = $end->copy()->subDays($days);

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'submitted' => Report::whereDate('created_at', $date)->count(),
                'resolved' => Report::where('status', 'resolved')->whereDate('resolved_at', $date)->count(),
            ];
        }

        return $data;
    }

    /**
     * Average resolution time in hours
     */
    public function averageResolutionTime(?int $companyId = null): float
    {
        $query = StatusHistory::where('to_status', 'resolved');

        if ($companyId) {
            $query->whereHas('report', fn($q) => $q->where('assigned_company_id', $companyId));
        }

        $histories = $query->with('report')->get();

        if ($histories->isEmpty()) {
            return 0;
        }

        $totalHours = 0;
        $count = 0;

        foreach ($histories as $history) {
            $createdAt = $history->report->created_at;
            $resolvedAt = $history->created_at;
            $totalHours += $createdAt->diffInHours($resolvedAt);
            $count++;
        }

        return round($totalHours / $count, 1);
    }

    /**
     * Get reports by city
     */
    public function getReportsByCity(): array
    {
        return Report::with('city')
            ->selectRaw('city_id, COUNT(*) as count')
            ->groupBy('city_id')
            ->get()
            ->map(fn($r) => [
                'city' => $r->city->name,
                'count' => $r->count,
            ])
            ->toArray();
    }

    /**
     * Get reports by severity
     */
    public function getReportsBySeverity(): array
    {
        return Report::selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->get()
            ->map(fn($r) => [
                'severity' => $r->severity,
                'count' => $r->count,
            ])
            ->toArray();
    }
}
