<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Log\LogManager;
use Illuminate\Support\ServiceProvider;
use Meius\LaravelFilter\Services\ControllerManager;
use Meius\LaravelFilter\Services\Filter\CachedFilterManager;
use Meius\LaravelFilter\Services\Filter\FilterManager;
use Psr\Log\LoggerInterface;

/**
 * @codeCoverageIgnore
 */
class LoggerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->when([
            FilterManager::class,
            CachedFilterManager::class,
            ControllerManager::class,
        ])
            ->needs(LoggerInterface::class)
            ->give(function (Application $app) {
                $logManager = new LogManager($app);

                return $logManager->build([
                    'driver' => Config::get('filter.logger.driver'),
                    'path' => Config::get('filter.logger.path'),
                ]);
            });
    }
}