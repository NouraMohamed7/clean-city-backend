<?php

namespace App\Helpers;

class HaversineHelper
{
    /**
     * Earth radius in kilometers
     */
    private const EARTH_RADIUS = 6371;

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    public static function distance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $latDiff = deg2rad($lat2 - $lat1);
        $lngDiff = deg2rad($lng2 - $lng1);

        $a = sin($latDiff / 2) ** 2
            + cos(deg2rad($lat1))
            * cos(deg2rad($lat2))
            * sin($lngDiff / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round(self::EARTH_RADIUS * $c, 2);
    }

    /**
     * Calculate total route distance
     */
    public static function routeDistance(array $points): float
    {
        $total = 0;
        $count = count($points);

        for ($i = 0; $i < $count - 1; $i++) {
            $total += self::distance(
                $points[$i]->latitude,
                $points[$i]->longitude,
                $points[$i + 1]->latitude,
                $points[$i + 1]->longitude
            );
        }

        return round($total, 2);
    }
}
