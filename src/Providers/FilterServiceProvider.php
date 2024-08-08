<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Providers;

use Illuminate\Support\ServiceProvider;
use Meius\LaravelFilter\FilterManager;

class FilterServiceProvider extends ServiceProvider
{
    protected FilterManager $filterManager;

    protected bool $findInBaseDirectory = true;

    public function register(): void
    {
        $this->app->singletonIf(FilterManager::class);
    }

    public function boot(): void
    {
        $this->filterManager = app(FilterManager::class);

        $this->filterManager->addFiltersDirectory(...$this->discoveredFilters());
    }

    protected function discoveredFilters(): array
    {
        return $this->findInBaseDirectory
            ? [$this->baseFilterDirectory()]
            : $this->discoverFiltersWithin();
    }

    protected function discoverFiltersWithin(): array
    {
        return [
            $this->baseFilterDirectory(),
        ];
    }

    private function baseFilterDirectory(): string
    {
        return $this->filterManager->baseFilterDirectory();
    }
}
