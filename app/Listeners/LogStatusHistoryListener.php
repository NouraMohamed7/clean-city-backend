<?php

namespace App\Listeners;

use App\Events\ReportAssigned;
use App\Events\ReportResolved;
use App\Models\StatusHistory;

class LogStatusHistoryListener
{
    public function handle(ReportAssigned|ReportResolved $event): void
    {
        $report = $event->report;
        $oldStatus = $report->getOriginal('status') ?? 'pending';

        StatusHistory::create([
            'report_id' => $report->id,
            'from_status' => $oldStatus,
            'to_status' => $report->status,
            'changed_by' => auth()->id(),
            'note' => "Status changed via event",
        ]);
    }
}
