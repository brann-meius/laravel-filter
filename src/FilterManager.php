<?php

declare(strict_types=1);

namespace Meius\LaravelFilter;

use Generator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Meius\LaravelFilter\Attributes\Settings\ExcludeFor;
use Meius\LaravelFilter\Attributes\Settings\OnlyFor;
use Meius\LaravelFilter\Attributes\Settings\Setting;
use Meius\LaravelFilter\Filters\FilterInterface;
use Meius\LaravelFilter\Traits\Reflective;
use Psr\Log\LoggerInterface;
use ReflectionAttribute;

/**
 * Manages the application and retrieval of filters for Eloquent models.
 */
class FilterManager
{
    use Reflective;

    private array $directoriesWithFilters = [];

    public function __construct(
        private Filesystem $filesystem,
        private LoggerInterface $logger,
    ) {}

    /**
     * Apply the given filter to the specified models based on the request.
     */
    public function apply(FilterInterface $filter, array $pathsToModels, Request $request): void
    {
        $reflection = new \ReflectionClass($filter);

        if ($this->hasSettingAttributes($reflection)) {
            $pathsToModels = $this->filterModelsByAttributes($reflection, $pathsToModels);
        }

        $this->applyFilterToModels($filter, $pathsToModels, $request);
    }

    /**
     * Retrieve all available filters.
     *
     * @return Generator<FilterInterface>
     */
    public function filters(): Generator
    {
        foreach ($this->pathsToFilters() as $pathToFilter) {
            try {
                $filter = $this->filesystem->requireOnce($pathToFilter);
            } catch (FileNotFoundException) {
                $this->logger->error("The filter file at $pathToFilter could not be found.");

                continue;
            }

            if ($filter instanceof FilterInterface && ! (new \ReflectionClass($filter))->isAbstract()) {
                yield $filter;
            }
        }
    }

    /**
     * Add directories containing filter classes.
     */
    public function addFiltersDirectory(string ...$paths): self
    {
        foreach ($paths as $path) {
            $this->directoriesWithFilters[] = $path;
        }

        return $this;
    }

    /**
     * Get the base directory for filters.
     */
    public function baseFilterDirectory(): string
    {
        return app_path('Filters');
    }

    /**
     * Get paths to all filter files.
     */
    protected function pathsToFilters(): array
    {
        $pathsToFilters = [];

        foreach ($this->directoriesWithFilters as $path) {
            $pathsToFilters = array_merge($pathsToFilters, $this->pullOutFiltersPaths($path));
        }

        return $pathsToFilters;
    }

    /**
     * Filter the given models based on the attributes of the filter class.
     *
     * This method checks for `OnlyFor` and `ExcludeFor` attributes on the filter class
     * and adjusts the list of models accordingly. Models specified in `OnlyFor` will be
     * included, while models specified in `ExcludeFor` will be excluded.
     */
    protected function filterModelsByAttributes(\ReflectionClass $reflection, array $pathsToModels): array
    {
        if (! empty($onlyModels = $this->parseAttributes($reflection->getAttributes(OnlyFor::class)))) {
            $pathsToModels = array_intersect($pathsToModels, $onlyModels);
        }

        if (! empty($excludeModels = $this->parseAttributes($reflection->getAttributes(ExcludeFor::class)))) {
            $pathsToModels = array_diff($pathsToModels, $excludeModels);
        }

        return $pathsToModels;
    }

    /**
     * Check if the filter class has setting attributes.
     */
    private function hasSettingAttributes(\ReflectionClass $reflection): bool
    {
        return ! empty($reflection->getAttributes(Setting::class, ReflectionAttribute::IS_INSTANCEOF));
    }

    /**
     * Recursively retrieve paths to filter files in the given directory.
     */
    private function pullOutFiltersPaths(string $path): array
    {
        $filters = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filters[] = $file->getPathname();
            }
        }

        return $filters;
    }

    /**
     * Apply the filter to the specified models.
     */
    private function applyFilterToModels(FilterInterface $filter, array $pathsToModels, Request $request): void
    {
        foreach ($pathsToModels as $pathToModel) {
            $filter->create($pathToModel)->apply($request);
        }
    }
}
