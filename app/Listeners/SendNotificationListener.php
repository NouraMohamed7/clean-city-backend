<?php

namespace App\Listeners;

use App\Events\ReportAssigned;
use App\Events\ReportResolved;
use App\Services\NotificationService;

class SendNotificationListener
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(ReportAssigned|ReportResolved $event): void
    {
        $this->notificationService->reportStatusChanged($event->report);
    }
}
