<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Meius\LaravelFilter\Console\FilterCacheCommand;
use Meius\LaravelFilter\Console\FilterClearCommand;
use Meius\LaravelFilter\Console\FilterMakeCommand;
use Meius\LaravelFilter\Http\Middleware\ScopedFilterMiddleware;
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


    public function boot(Router $router): void
    {
        $this->publishConfiguration();
        $this->registerRouteMiddleware($router);
        $this->registerConsoleCommands();
    }

    protected function publishConfiguration(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/filter.php' => $this->app->configPath('filter.php'),
        ], 'filter-config');
    }

    protected function registerRouteMiddleware(Router $router): void
    {
        foreach (Config::get('filter.route_groups', []) as $group) {
            $router->pushMiddlewareToGroup($group, ScopedFilterMiddleware::class);
        }
    }

    protected function registerConsoleCommands(): void
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
