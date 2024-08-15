<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Factories;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Meius\LaravelFilter\Services\Filter\CachedFilterManager;
use Meius\LaravelFilter\Services\Filter\FilterManager;

class FilterManagerFactory
{
    public function __construct(
        private Filesystem $filesystem,
        private Application $app,
    ) {}

    public function create(): FilterManager
    {
        if ($this->filesystem->exists(Config::get('filter.cache.path'))) {
            return $this->app->make(CachedFilterManager::class);
        }

        return $this->app->make(FilterManager::class);
    }
}
