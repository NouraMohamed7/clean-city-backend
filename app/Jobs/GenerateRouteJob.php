<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\CleanRouteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateRouteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Company $company) {}

    public function handle(CleanRouteService $routeService): void
    {
        $route = $routeService->calculate($this->company);

        // Cache the route for quick access
        cache()->put("route:{$this->company->id}", $route, now()->addHours(1));
    }
}
