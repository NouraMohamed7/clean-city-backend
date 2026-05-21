<?php

namespace App\Listeners;

use App\Events\ReportCreated;
use App\Events\ReportResolved;
use App\Events\UpvoteAdded;
use App\Services\PointsService;

class AddPointsListener
{
    public function __construct(private PointsService $pointsService) {}

    public function handle(ReportCreated|ReportResolved|UpvoteAdded $event): void
    {
        match (get_class($event)) {
            ReportCreated::class => $this->pointsService->award($event->report->user, 'report_submitted', $event->report),
            ReportResolved::class => $this->pointsService->award($event->report->user, 'report_resolved', $event->report),
            UpvoteAdded::class => $this->pointsService->award($event->upvote->report->user, 'upvote_received', $event->upvote->report),
        };
    }
}
