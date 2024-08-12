<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Meius\LaravelFilter\FilterManager;
use Psr\Log\LoggerInterface;

class LoggerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->when(FilterManager::class)
            ->needs(LoggerInterface::class)
            ->give(fn (): LoggerInterface => Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/filter/'.Carbon::now()->format('Y-m-d').'.log'),
            ]));
    }
}
