<?php

namespace App\Jobs;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessImageUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Report $report,
        private array $imagePaths
    ) {}

    public function handle(): void
    {
        foreach ($this->imagePaths as $path) {
            // Process image (resize, optimize, etc.)
            $fullPath = Storage::disk('public')->path($path);

            if (file_exists($fullPath)) {
                // Image optimization logic here
                // Example: resize to multiple sizes
            }
        }
    }
}
