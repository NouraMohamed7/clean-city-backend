<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class TokenHelper
{
    /**
     * Generate unique tracking token
     */
    public static function generateTrackingToken(int $length = 16): string
    {
        return Str::random($length);
    }

    /**
     * Generate API token for Sanctum
     */
    public static function generateApiToken(): string
    {
        return Str::random(64);
    }
}
