<?php

namespace App\Providers;

use App\Events\ReportCreated;
use App\Events\ReportAssigned;
use App\Events\ReportResolved;
use App\Events\UpvoteAdded;
use App\Events\UserRated;
use App\Listeners\SendNotificationListener;
use App\Listeners\AddPointsListener;
use App\Listeners\LogStatusHistoryListener;
use App\Listeners\UpdateLeaderboardListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ReportCreated::class => [
            AddPointsListener::class,
        ],
        ReportAssigned::class => [
            SendNotificationListener::class,
            LogStatusHistoryListener::class,
        ],
        ReportResolved::class => [
            SendNotificationListener::class,
            AddPointsListener::class,
            LogStatusHistoryListener::class,
            UpdateLeaderboardListener::class,
        ],
        UpvoteAdded::class => [
            AddPointsListener::class,
            UpdateLeaderboardListener::class,
        ],
        UserRated::class => [],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
