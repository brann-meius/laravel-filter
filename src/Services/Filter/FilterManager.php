<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services\Filter;

use Generator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Meius\LaravelFilter\Attributes\Setting;
use Meius\LaravelFilter\Attributes\Settings\ExcludeFor;
use Meius\LaravelFilter\Attributes\Settings\OnlyFor;
use Meius\LaravelFilter\Filters\FilterInterface;
use Meius\LaravelFilter\Traits\Reflective;
use Psr\Log\LoggerInterface;
use ReflectionAttribute;
use Symfony\Component\Finder\Finder;

/**
 * Manages the application and retrieval of filters for Eloquent models.
 */
class FilterManager implements FilterManagerInterface
{
    use Reflective;

    protected array $directoriesWithFilters = [];

    public function __construct(
        protected Filesystem $filesystem,
        protected Finder $finder,
        protected LoggerInterface $logger,
    ) {}

    /**
     * Apply the given filter to the specified models based on the request.
     */
    public function apply(array $pathsToModels, Request $request): void
    {
        foreach ($this->filters() as $filter) {
            $this->applyFilterToModels($filter, $this->filterModelsBySettings($pathsToModels, $filter), $request);
        }
    }

    /**
     * Retrieve all available filters.
     *
     * @return Generator<FilterInterface>
     */
    public function filters(): Generator
    {
        foreach ($this->pathsToFilters() as $pathToFilter) {
            $filter = $this->filter($pathToFilter);

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
        $validPaths = array_filter($paths, 'is_dir');
        $uniquePaths = array_unique($validPaths);
        $this->directoriesWithFilters = array_merge($this->directoriesWithFilters, $uniquePaths);

        return $this;
    }

    /**
     * Filter the given models based on the settings of the filter class.
     *
     * This method checks if the filter class has specific setting attributes.
     * If it does, it filters the models accordingly using those attributes.
     * Otherwise, it returns the original list of models.
     *
     * @return class-string<Model>[]
     */
    public function filterModelsBySettings(array $pathsToModels, FilterInterface $filter): array
    {
        $reflection = new \ReflectionClass($filter);

        if ($this->hasSettingAttributes($reflection)) {
            return $this->filterModelsByAttributes($reflection, $pathsToModels);
        }

        return $pathsToModels;
    }

    /**
     * Get the base directory for filters.
     */
    public function baseFilterDirectory(): string
    {
        return Config::get('filter.path');
    }

    /**
     * Retrieve the list of directories that contain filter classes.
     */
    public function getDirectoriesWithFilters(): array
    {
        return $this->directoriesWithFilters;
    }

    /**
     * Retrieve available filter.
     */
    protected function filter(string $pathToFilter): FilterInterface|false
    {
        static $loadedFilters = [];

        if (isset($loadedFilters[$pathToFilter])) {
            return new $loadedFilters[$pathToFilter];
        }

        try {
            $filter = $this->filesystem->requireOnce($pathToFilter);

            if ($filter instanceof FilterInterface) {
                $loadedFilters[$pathToFilter] = $filter::class;
            }

            if ($filter === EXTR_SKIP || $filter === true) {
                throw new FileNotFoundException('Filter file not found.');
            }

            return $filter;
        } catch (FileNotFoundException) {
            $this->logger->error("The filter file at $pathToFilter could not be found.");

            return false;
        }
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
     * Get paths to all filter files.
     */
    protected function pathsToFilters(): array
    {
        return $this->pullOutFiltersPaths($this->getDirectoriesWithFilters());
    }

    /**
     * Recursively retrieve paths to filter files in the given directory.
     */
    final protected function pullOutFiltersPaths(array $paths): array
    {
        $filters = [];
        $this->finder->files()
            ->in($paths)
            ->name('*.php');

        foreach ($this->finder as $file) {
            $filters[] = $file->getRealPath();
        }

        return $filters;
    }

    /**
     * Apply the filter to the specified models.
     */
    final protected function applyFilterToModels(FilterInterface $filter, array $pathsToModels, Request $request): void
    {
        foreach ($pathsToModels as $pathToModel) {
            $filter->create($pathToModel)->apply($request);
        }
    }

    /**
     * Check if the filter class has setting attributes.
     */
    final protected function hasSettingAttributes(\ReflectionClass $reflection): bool
    {
        return ! empty($reflection->getAttributes(Setting::class, ReflectionAttribute::IS_INSTANCEOF));
    }
}
