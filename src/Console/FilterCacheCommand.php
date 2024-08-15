<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Meius\LaravelFilter\Services\Filter\CachedFilterManager;
use Meius\LaravelFilter\Services\ModelManager;

class FilterCacheCommand extends Command
{
    protected $signature = 'filter:cache';

    protected $description = 'Create a cache file for faster filter loading';

    public function __construct(
        private Filesystem $filesystem,
        private CachedFilterManager $filterManager,
        private ModelManager $modelManager
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->callSilent('filter:clear');

        if (empty($filters = $this->filters())) {
            $this->components->info('No filters found. Filters cache not generated.');

            return self::SUCCESS;
        }

        try {
            $this->filesystem->put(
                Config::get('filter.cache.path'),
                '<?php return '.var_export($filters, true).';'.PHP_EOL
            );
        } catch (\Throwable $exception) {
            $this->error('Failed to write filters cache file: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info('Filters cached successfully.');

        return self::SUCCESS;
    }

    /**
     * Retrieve and map filters to their respective models.
     */
    private function filters(): array
    {
        $filters = $this->filterManager->filters();
        $models = $this->modelManager->get();
        $modelFilterMap = [];

        foreach ($filters as $filter) {
            $filteredModels = $this->filterManager->filterModelsBySettings($models, $filter);

            foreach ($filteredModels as $model) {
                if (! isset($modelFilterMap[$model])) {
                    $modelFilterMap[$model] = [];
                }

                $modelFilterMap[$model][] = (new \ReflectionClass($filter))->getFileName();
            }
        }

        return $modelFilterMap;
    }
}
