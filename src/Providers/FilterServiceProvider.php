<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Providers;

use Illuminate\Support\ServiceProvider;
use Meius\LaravelFilter\Console\FilterCacheCommand;
use Meius\LaravelFilter\Console\FilterClearCommand;
use Meius\LaravelFilter\Console\FilterMakeCommand;
use Meius\LaravelFilter\Services\Filter\FilterManager;

/**
 * @codeCoverageIgnore
 */
class FilterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/filter.php',
            'filter'
        );

        $this->app->singleton(FilterManager::class);
    }


    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/filter.php' => $this->app->configPath('filter.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                FilterMakeCommand::class,
                FilterClearCommand::class,
                FilterCacheCommand::class,
            ]);
        }
    }
}
