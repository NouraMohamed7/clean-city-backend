<?php

namespace App\Jobs;

use App\Services\SeasonalAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SeasonalAnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(SeasonalAnalysisService $service): void
    {
        $alerts = $service->analyze();

        foreach ($alerts as $alert) {
            $service->createAlert($alert);
        }
    }
}
