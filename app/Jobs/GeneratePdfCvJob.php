<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GeneratePdfCvJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $userId) {}

    public function handle(): void
    {
        Log::info('Proses render CV ATS untuk User ID: '.$this->userId.' sedang berjalan di background...');

        sleep(3);
    }
}
