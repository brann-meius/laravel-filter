<?php

declare(strict_types=1);

namespace Meius\LaravelFilter;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Meius\LaravelFilter\Attributes\Settings\ExcludeFor;
use Meius\LaravelFilter\Attributes\Settings\OnlyFor;
use Meius\LaravelFilter\Attributes\Settings\Setting;
use Meius\LaravelFilter\Filters\Filter;
use Meius\LaravelFilter\Filters\FilterInterface;
use Meius\LaravelFilter\Traits\Reflective;
use ReflectionAttribute;

class FilterManager
{
    use Reflective;

    private array $directoriesWithFilters = [];

    public function __construct(
        private Filesystem $filesystem,
    ) {}

    public function apply(Filter $filter, array $pathsToModels, Request $request): void
    {
        $reflection = new \ReflectionClass($filter);

        if (empty($reflection->getAttributes(Setting::class, ReflectionAttribute::IS_INSTANCEOF))) {
            goto apply;
        }

        if (! empty($onlyModels = $this->parseAttributes($reflection->getAttributes(OnlyFor::class)))) {
            $pathsToModels = array_intersect($pathsToModels, $onlyModels);
        }

        if (! empty($excludeModels = $this->parseAttributes($reflection->getAttributes(ExcludeFor::class)))) {
            $pathsToModels = array_diff($pathsToModels, $excludeModels);
        }

        apply:
        foreach ($pathsToModels as $pathToModel) {
            $filter->create($pathToModel)
                ->apply($request);
        }
    }

    public function filters(): array
    {
        $filters = [];

        foreach ($this->pathsToFilters() as $pathToFilter) {
            $filter = $this->filesystem->requireOnce($pathToFilter);

            if ($filter instanceof FilterInterface && ! (new \ReflectionClass($filter))->isAbstract()) {
                $filters[] = $filter;
            }
        }

        return $filters;
    }

    public function addFiltersDirectory(string ...$paths): self
    {
        foreach ($paths as $path) {
            $this->directoriesWithFilters[] = $path;
        }

        return $this;
    }

    public function baseFilterDirectory(): string
    {
        return app_path('Filters');
    }

    protected function pathsToFilters(): array
    {
        $pathsToFilters = [];

        foreach ($this->directoriesWithFilters as $path) {
            $pathsToFilters = array_merge($pathsToFilters, $this->pullOutFiltersPaths($path));
        }

        return $pathsToFilters;
    }

    private function pullOutFiltersPaths(string $path): array
    {
        $filters = $this->filesystem->glob($path.'/*.php');

        foreach ($this->filesystem->glob($path.'/*', GLOB_ONLYDIR) as $subdirectory) {
            $filters = array_merge($filters, $this->pullOutFiltersPaths($subdirectory));
        }

        return $filters;
    }
}
