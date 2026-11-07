<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeneratePdfCvJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $userId) {}

    public function handle(): void
    {
        Log::info('Proses render CV ATS untuk User ID: '.$this->userId.' sedang berjalan di background...');

        sleep(3);

        Log::info('PDF CV ATS berhasil dibuat untuk User ID: '.$this->userId.'.');
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('GeneratePdfCvJob gagal untuk User ID: '.$this->userId.': '.($exception?->getMessage() ?? 'Unknown error'));
    }
}
