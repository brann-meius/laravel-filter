<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services\Filter;

use Generator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Meius\LaravelFilter\Filters\FilterInterface;
use Meius\LaravelFilter\Helpers\FinderHelper;
use ReflectionClass;
use ReflectionException;

class FilterManager implements FilterManagerInterface
{
    public function __construct(protected FinderHelper $splFileInfoHelper) {}

    public function apply(array $models, Request $request): void
    {
        foreach ($this->filters() as $filter) {
            $this->applyFilterToModels($filter, $this->filterModelsBySettings($models, $filter), $request);
        }
    }

    public function filters(): Generator
    {
        foreach ($this->splFileInfoHelper->configureFinderFiles($this->baseFilterDirectory()) as $file) {
            $filter = $this->splFileInfoHelper->getNamespace($file);

            if ($this->isValidFilterClass($filter)) {
                yield new $filter;
            }
        }
    }

    public function filterModelsBySettings(array $models, FilterInterface $filter): array
    {
        if ($filter->hasSettingAttributes()) {
            return $this->filterModelsByAttributes($filter, $models);
        }

        return $models;
    }

    public function baseFilterDirectory(): string
    {
        return App::path(Config::get('filter.path', 'Filters'));
    }

    /**
     * Filter the given models based on the attributes of the filter class.
     *
     * This method checks for `OnlyFor` and `ExcludeFrom` attributes on the filter class
     * and adjusts the list of models accordingly. Models specified in `OnlyFor` will be
     * included, while models specified in `ExcludeFrom` will be excluded.
     *
     * @return class-string<Model>[]
     */
    protected function filterModelsByAttributes(FilterInterface $reflection, array $models): array
    {
        if (! empty($onlyModels = $reflection->onlyFor())) {
            $models = array_intersect($models, $onlyModels);
        }

        if (! empty($excludeModels = $reflection->excludeFrom())) {
            $models = array_diff($models, $excludeModels);
        }

        return $models;
    }

    /**
     * Apply the filter to the specified models.
     */
    protected function applyFilterToModels(FilterInterface $filter, array $models, Request $request): void
    {
        foreach ($models as $model) {
            $filter($model, $request);
        }
    }

    /**
     * Check if the given class is a valid filter class (implements FilterInterface and is not abstract).
     */
    private function isValidFilterClass(string $class): bool
    {
        try {
            return is_subclass_of($class, FilterInterface::class) && ! (new ReflectionClass($class))->isAbstract();
        } catch (ReflectionException) {
            return false;
        }
    }
}
