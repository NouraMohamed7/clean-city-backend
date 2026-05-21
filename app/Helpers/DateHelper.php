<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format date to human readable
     */
    public static function humanReadable(?Carbon $date): string
    {
        if (!$date) {
            return 'N/A';
        }

        return $date->diffForHumans();
    }

    /**
     * Format date for API response
     */
    public static function apiFormat(?Carbon $date): ?string
    {
        if (!$date) {
            return null;
        }

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get month name in Arabic
     */
    public static function arabicMonth(int $month): string
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        return $months[$month] ?? '';
    }
}
