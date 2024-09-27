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
    ) {}

    /**
     * @throws BindingResolutionException
     */
    public function create(): FilterManagerInterface
    {
        if ($this->filesystem->exists(Config::get('filter.cache.path'))) {
            return $this->app->make(CachedFilterManager::class);
        }

        return $this->app->make(FilterManager::class);
    }
}
