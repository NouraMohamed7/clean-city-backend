<?php

namespace App\Traits;

use App\Helpers\HaversineHelper;

trait DistanceTrait
{
    /**
     * Calculate distance between two points
     */
    protected function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        return HaversineHelper::distance($lat1, $lng1, $lat2, $lng2);
    }

    /**
     * Find nearest point from array of points
     */
    protected function findNearest(float $lat, float $lng, array $points): ?object
    {
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($points as $point) {
            $distance = $this->calculateDistance($lat, $lng, $point->latitude, $point->longitude);

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $point;
            }
        }

        return $nearest;
    }
}
