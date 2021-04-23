<?php

namespace App\Jobs;

use App\Services\Category\CountingColorsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Задание: пересчитать основные цвета в категориях.
 *
 * @package App\Jobs
 * @codeCoverageIgnore
 */
class CountingColorsByCategoryJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        CountingColorsService::recalculate();
    }
}
