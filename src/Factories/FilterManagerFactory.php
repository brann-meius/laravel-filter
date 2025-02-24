<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Meius\LaravelFilter\Services\Filter\CachedFilterManager;
use Meius\LaravelFilter\Services\Filter\FilterManager;
use Meius\LaravelFilter\Services\Filter\FilterManagerInterface;

class FilterManagerFactory
{
    public function __construct(
        private Filesystem $filesystem,
        private Application $app,
    ) {
        //
    }

    /**
     * @throws BindingResolutionException
     */
    public function create(): FilterManagerInterface
    {
        return $this->app->make($this->getManager());
    }

    /**
     * Determine the appropriate filter manager class to use.
     *
     * @return class-string<FilterManagerInterface>
     */
    protected function getManager(): string
    {
        return match ($this->hasCache()) {
            true => CachedFilterManager::class,
            false => FilterManager::class,
        };
    }

    /**
     * Check if the filter cache exists.
     */
    protected function hasCache(): bool
    {
        return $this->filesystem->exists(Config::get('filter.cache.path', base_path('bootstrap/cache/filters.php')));
    }
}
