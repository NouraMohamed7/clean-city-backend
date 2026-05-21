<?php

namespace App\Services;

use App\Models\StatusHistory;
use App\Models\SeasonalAlert;
use App\Models\City;
use Carbon\Carbon;

class SeasonalAnalysisService
{
    /**
     * Analyze historical patterns and generate alerts
     */
    public function analyze(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get historical data
        $patterns = StatusHistory::selectRaw('
            city_id,
            MONTH(created_at) as month,
            YEAR(created_at) as year,
            COUNT(*) as report_count
        ')
            ->where('to_status', 'pending')
            ->groupBy('city_id', 'month', 'year')
            ->get();

        $alerts = [];

        foreach ($patterns->groupBy('city_id') as $cityId => $cityPatterns) {
            foreach ($cityPatterns->groupBy('month') as $month => $monthPatterns) {
                if ($month != $currentMonth && $month != ($currentMonth + 1)) {
                    continue; // Only check current and next month
                }

                $years = $monthPatterns->pluck('year')->unique();
                $avgCount = $monthPatterns->avg('report_count');
                $lastYearCount = $monthPatterns->where('year', $currentYear - 1)->first()?->report_count ?? 0;

                if ($lastYearCount > 0) {
                    $increase = (($avgCount - $lastYearCount) / $lastYearCount) * 100;

                    if ($increase > 50) { // Significant increase
                        $alerts[] = [
                            'city_id' => $cityId,
                            'month' => $month,
                            'predicted_increase' => round($increase),
                            'event_name' => $this->getEventName($month),
                            'recommendation' => $this->generateRecommendation($cityId, $increase),
                        ];
                    }
                }
            }
        }

        return $alerts;
    }

    /**
     * Get existing alerts
     */
    public function getAlerts(?int $cityId = null): array
    {
        $query = SeasonalAlert::with('city')->where('is_active', true);

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        return $query->get()->map(fn($a) => [
            'id' => $a->id,
            'city' => $a->city->name,
            'month' => $a->month,
            'event_name' => $a->event_name,
            'predicted_increase' => $a->predicted_increase_percent,
            'recommendation' => $a->recommendation,
            'based_on_years' => $a->based_on_years,
        ])->toArray();
    }

    /**
     * Create manual alert
     */
    public function createAlert(array $data): SeasonalAlert
    {
        return SeasonalAlert::create([
            'city_id' => $data['city_id'],
            'month' => $data['month'],
            'event_name' => $data['event_name'],
            'predicted_increase_percent' => $data['predicted_increase_percent'],
            'recommendation' => $data['recommendation'],
            'based_on_years' => $data['based_on_years'] ?? [],
            'is_active' => true,
        ]);
    }

    /**
     * Get event name by month
     */
    private function getEventName(int $month): string
    {
        $events = [
            1 => 'New Year',
            6 => 'Summer Season',
            7 => 'Eid Al-Adha',
            8 => 'Summer Peak',
            12 => 'New Year Prep',
        ];

        return $events[$month] ?? 'Seasonal Peak';
    }

    /**
     * Generate recommendation
     */
    private function generateRecommendation(int $cityId, float $increase): string
    {
        $city = City::find($cityId);
        $extraCompanies = ceil($increase / 100);

        return "Deploy {$extraCompanies} extra cleanup companies in {$city->name} area.";
    }
}
