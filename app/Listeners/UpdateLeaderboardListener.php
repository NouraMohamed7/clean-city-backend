<?php

namespace App\Listeners;

use App\Events\ReportResolved;
use App\Events\UpvoteAdded;
use Illuminate\Support\Facades\Cache;

class UpdateLeaderboardListener
{
    public function handle(ReportResolved|UpvoteAdded $event): void
    {
        Cache::forget('leaderboard');
    }
}
