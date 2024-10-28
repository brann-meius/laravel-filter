<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Providers;

use Illuminate\Support\ServiceProvider;
use Meius\LaravelFilter\Console\FilterCacheCommand;
use Meius\LaravelFilter\Console\FilterClearCommand;
use Meius\LaravelFilter\Console\FilterMakeCommand;

/**
 * @codeCoverageIgnore
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/filter.php',
            'filter'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FilterMakeCommand::class,
                FilterClearCommand::class,
                FilterCacheCommand::class,
            ]);
        }
    }
}
