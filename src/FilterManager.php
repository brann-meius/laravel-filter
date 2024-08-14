<?php

declare(strict_types=1);

namespace Meius\LaravelFilter;

use Generator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
class FilterManager
{
    use Reflective;

    private array $directoriesWithFilters = [];

    private string $cachePath;

    public function __construct(
        private Filesystem $filesystem,
        private Finder $finder,
        private LoggerInterface $logger,
    ) {
        $this->cachePath = App::bootstrapPath('cache/filters.php');
    }

    /**
     * Apply the given filter to the specified models based on the request.
     */
    public function apply(FilterInterface $filter, array $pathsToModels, Request $request): array
    {
        $reflection = new \ReflectionClass($filter);

        if ($this->hasSettingAttributes($reflection)) {
            $pathsToModels = $this->filterModelsByAttributes($reflection, $pathsToModels);
        }

        $this->applyFilterToModels($filter, $pathsToModels, $request);

        return $pathsToModels;
    }

    /**
     * @param  class-string<Model>[]  $pathToModels
     *
     * @throws FileNotFoundException
     */
    public function applyFiltersFromCache(array $pathToModels, Request $request): void
    {
        $filters = $this->filesystem->requireOnce($this->cachePath);

        foreach ($pathToModels as $pathToModel) {
            if (empty($filters[$pathToModel])) {
                continue;
            }

            foreach ($filters[$pathToModel] as $pathToFilter) {
                $filter = $this->filter($pathToFilter);

                if ($filter) {
                    $this->apply($filter, [$pathToModel], $request);
                }
            }
        }
    }

    /**
     * Check if the cache file exists.
     */
    public function isCacheExist(): bool
    {
        return $this->filesystem->exists($this->cachePath);
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
        return App::path('Filters');
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
        static $as = [];

        try {
            $filter = $this->filesystem->requireOnce($pathToFilter);

            if ($filter instanceof FilterInterface) {
                $as[$pathToFilter] = $filter::class;
            }

            if ($filter === EXTR_SKIP || $filter === true) {
                $filter = new $as[$pathToFilter];
            }

            return $filter;
        } catch (FileNotFoundException) {
            $this->logger->error("The filter file at $pathToFilter could not be found.");

            return false;
        }
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
        $this->finder->files()
            ->in($path)
            ->name('*.php');

        foreach ($this->finder as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filters[] = $file->getRealPath();
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
