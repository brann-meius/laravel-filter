<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Providers;

use Illuminate\Support\ServiceProvider;
use Meius\LaravelFilter\Services\Filter\FilterManager;

/**
 * @codeCoverageIgnore
 */
class FilterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singletonIf(FilterManager::class);
    }
}
