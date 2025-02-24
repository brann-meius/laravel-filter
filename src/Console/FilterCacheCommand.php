<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Meius\LaravelFilter\Filters\FilterInterface;
use Meius\LaravelFilter\Services\Filter\FilterManager;
use Meius\LaravelFilter\Services\ModelManager;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'filter:cache',
    description: 'Create a cache file for faster filter loading',
)]
class FilterCacheCommand extends Command
{
    public function handle(
        Filesystem $filesystem,
        FilterManager $filterManager,
        ModelManager $modelManager
    ): int {
        $this->callSilent('filter:clear');

        $modelFiltersAssociation = $this->getFilters($filterManager, $modelManager);

        if (empty($modelFiltersAssociation)) {
            $this->components->info('No filters found. Filters cache not generated.');

            return self::SUCCESS;
        }

        return $this->writeFiltersCache($filesystem, $modelFiltersAssociation);
    }

    /**
     * Retrieve and map filters to their respective models.
     *
     * @return array<class-string<Model>, class-string<FilterInterface>[]>
     */
    private function getFilters(FilterManager $filterManager, ModelManager $modelManager): array
    {
        $filters = $filterManager->filters();
        $models = $modelManager->getModels();
        $modelFiltersAssociation = [];

        foreach ($filters as $filter) {
            $filteredModels = $filterManager->filterModelsBySettings($models, $filter);

            foreach ($filteredModels as $model) {
                $modelFiltersAssociation[$model][] = $filter::class;
            }
        }

        return $modelFiltersAssociation;
    }

    /**
     * Write the filters cache to the specified path.
     *
     * @param array<class-string<Model>, class-string<FilterInterface>[]> $modelFiltersAssociation
     */
    private function writeFiltersCache(Filesystem $filesystem, array $modelFiltersAssociation): int
    {
        try {
            $filesystem->put(
                Config::get('filter.cache.path'),
                '<?php return ' . var_export($modelFiltersAssociation, true) . ';' . PHP_EOL
            );
            $this->components->info('Filters cached successfully.');
        } catch (\Throwable $exception) {
            $this->components->error('Failed to write filters cache file: ' . $exception->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
