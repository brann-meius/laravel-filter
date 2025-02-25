<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services\Filter;

use Generator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Meius\LaravelFilter\Services\FinderService;

class CachedFilterManager extends FilterManager
{
    public function __construct(
        FinderService $finderService,
        protected Filesystem $filesystem
    ) {
        parent::__construct($finderService);
    }

    public function apply(array $models, Request $request): void
    {
        try {
            foreach ($models as $model) {
                $this->applyFiltersForModel($model, $request);
            }
        } catch (\Throwable) {
            parent::apply($models, $request);
        }
    }

    /**
     * @throws FileNotFoundException
     */
    public function filters(string $model = null): Generator
    {
        static $filters = [];

        if (empty($filters)) {
            $path = Config::get('filter.cache.path', base_path('bootstrap/cache/filters.php'));

            $filters =  $this->filesystem->requireOnce($path);
        }

        if ($model !== null) {
            yield from $filters[$model] ?? [];
        } else {
            yield from array_unique(
                array_merge(
                    ...array_values($filters)
                )
            );
        }
    }

    /**
     * Apply all relevant filters to a given model.
     *
     * @param class-string<Model> $model
     * @throws FileNotFoundException
     */
    private function applyFiltersForModel(string $model, Request $request): void
    {
        foreach ($this->filters($model) as $filter) {
            if (! class_exists($filter)) {
                continue;
            }

            $this->applyFilterToModels($filter, [$model], $request);
        }
    }
}
