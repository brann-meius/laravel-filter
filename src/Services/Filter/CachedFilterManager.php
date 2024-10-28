<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services\Filter;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Meius\LaravelFilter\Helpers\FinderHelper;

class CachedFilterManager extends FilterManager
{
    public function __construct(
        protected FinderHelper $splFileInfoHelper,
        protected Filesystem $filesystem,
        private FilterManager $filterManager
    ) {
        parent::__construct($splFileInfoHelper);
    }

    #[\Override]
    public function apply(array $models, Request $request): void
    {
        try {
            $filters = $this->loadFiltersFromCache();
        } catch (\Throwable) {
            $this->filterManager->apply($models, $request);

            return;
        }

        foreach ($models as $model) {
            $this->applyFiltersForModel($model, $filters, $request);
        }
    }

    /**
     * Load filters from the cache file, or return null if the cache is unavailable.
     *
     * @throws FileNotFoundException
     */
    private function loadFiltersFromCache(): array
    {
        return $this->filesystem->requireOnce(Config::get('filter.cache.path', []));
    }

    /**
     * Apply all relevant filters to a given model.
     *
     * @param class-string<Model> $model
     */
    private function applyFiltersForModel(string $model, array $filters, Request $request): void
    {
        if (empty($filters[$model])) {
            return;
        }

        foreach ($filters[$model] as $filter) {
            $this->applyFilterToModels(new $filter(), [$model], $request);
        }
    }
}
