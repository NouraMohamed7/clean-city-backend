<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Collection;

class CleanRouteService
{
    public function __construct(
        private AutoAssignmentService $autoAssign
    ) {}

    /**
     * Calculate optimized route for company
     */
    public function calculate(Company $company): array
    {
        $reports = $company->assignments()
            ->with('report')
            ->get()
            ->map(fn($a) => $a->report)
            ->filter(fn($r) =>
                $r && in_array($r->status, ['assigned', 'in_progress'])
                && $r->latitude && $r->longitude
            )->values();

        if ($reports->isEmpty()) {
            return [
                'stops' => [],
                'total_km' => 0,
                'estimated_hours' => 0,
                'fuel_saved_liters' => 0,
            ];
        }

        $ordered = $this->nearestNeighbor($reports);
        $totalKm = $this->totalDistance($ordered);

        return [
            'stops' => $ordered->map(fn($r, $i) => [
                'order' => $i + 1,
                'report_id' => $r->id,
                'title' => $r->title,
                'address' => $r->address,
                'latitude' => $r->latitude,
                'longitude' => $r->longitude,
                'severity' => $r->severity,
                'status' => $r->status,
                'distance_from_prev' => $i > 0 ? round($this->autoAssign->haversine(
                    $ordered[$i - 1]->latitude,
                    $ordered[$i - 1]->longitude,
                    $r->latitude,
                    $r->longitude
                ), 1) : 0,
            ])->values(),
            'total_km' => round($totalKm, 1),
            'estimated_hours' => round($totalKm / 25, 1), // 25 km/h avg in city
            'fuel_saved_liters' => round($totalKm * 0.08, 1), // ~8L per 100km
        ];
    }

    /**
     * Nearest Neighbor algorithm with severity priority
     */
    private function nearestNeighbor(Collection $reports): Collection
    {
        $remaining = $reports->all();
        $order = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];

        // Sort by severity first
        usort($remaining, fn($a, $b) =>
            ($order[$a->severity] ?? 3) <=> ($order[$b->severity] ?? 3)
        );

        $current = array_shift($remaining);
        $ordered = [$current];

        while (!empty($remaining)) {
            $nearestKey = null;
            $minDist = PHP_FLOAT_MAX;

            foreach ($remaining as $key => $report) {
                $dist = $this->autoAssign->haversine(
                    $current->latitude,
                    $current->longitude,
                    $report->latitude,
                    $report->longitude
                );
                if ($dist < $minDist) {
                    $minDist = $dist;
                    $nearestKey = $key;
                }
            }

            $current = $remaining[$nearestKey];
            $ordered[] = $current;
            unset($remaining[$nearestKey]);
        }

        return collect($ordered);
    }

    /**
     * Calculate total route distance
     */
    private function totalDistance(Collection $ordered): float
    {
        $total = 0;
        for ($i = 0; $i < $ordered->count() - 1; $i++) {
            $total += $this->autoAssign->haversine(
                $ordered[$i]->latitude,
                $ordered[$i]->longitude,
                $ordered[$i + 1]->latitude,
                $ordered[$i + 1]->longitude
            );
        }
        return $total;
    }
}
