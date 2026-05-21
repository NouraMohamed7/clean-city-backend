<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Company;
use App\Models\Assignment;

class AutoAssignmentService
{
    /**
     * Assign report to nearest company
     */
    public function assignReport(Report $report): ?Assignment
    {
        // Delete old assignment if exists
        $report->assignments()->delete();

        $companies = Company::where('city_id', $report->city_id)
            ->where('is_active', true)
            ->with('city')
            ->get();

        if ($companies->isEmpty()) {
            return null;
        }

        $chosen = ($report->latitude && $report->longitude)
            ? $this->findNearest($report, $companies)
            : $this->findLeastLoaded($companies);

        return $this->createAssignment($report, $chosen);
    }

    /**
     * Assign all pending reports
     */
    public function assignAllPending(): int
    {
        $reports = Report::where('status', 'pending')->get();
        $count = 0;

        foreach ($reports as $report) {
            if ($this->assignReport($report)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Find nearest company by city location
     */
    private function findNearest(Report $report, $companies): Company
    {
        return $companies->sortBy(fn($c) =>
            $this->haversine(
                $report->latitude,
                $report->longitude,
                $c->city->latitude,
                $c->city->longitude
            )
        )->first();
    }

    /**
     * Find company with least active assignments
     */
    private function findLeastLoaded($companies): Company
    {
        return $companies->sortBy(fn($c) =>
            $c->assignments()
                ->whereHas('report', fn($q) =>
                    $q->whereIn('status', ['assigned', 'in_progress'])
                )->count()
        )->first();
    }

    /**
     * Create assignment record
     */
    private function createAssignment(Report $report, Company $company): Assignment
    {
        $assignment = Assignment::create([
            'report_id' => $report->id,
            'company_id' => $company->id,
            'status' => 'active',
        ]);

        $report->update([
            'status' => 'assigned',
            'assigned_company_id' => $company->id,
            'assigned_at' => now(),
        ]);

        StatusHistory::create([
            'report_id' => $report->id,
            'from_status' => 'pending',
            'to_status' => 'assigned',
            'changed_by' => null,
            'note' => "Auto-assigned to {$company->name}",
        ]);

        return $assignment;
    }

    /**
     * Haversine formula for distance calculation
     */
    public function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
