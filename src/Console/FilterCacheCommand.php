<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Meius\LaravelFilter\FilterManager;
use Meius\LaravelFilter\ModelManager;

class FilterCacheCommand extends Command
{
    protected $signature = 'filter:cache';

    protected $description = 'Create a cache file for faster filter loading';

    public function __construct(
        private Filesystem $filesystem,
        private FilterManager $filterManager,
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

        $this->filesystem->put(
            app()->bootstrapPath('cache/filters.php'),
            '<?php return '.var_export($filters, true).';'.PHP_EOL
        );

        $this->components->info('Filters cached successfully.');

        return self::SUCCESS;
    }

    private function filters(): array
    {
        $filters = $this->filterManager->filters();
        $models = $this->modelManager->get();
        $modelFilterMap = [];

        foreach ($filters as $filter) {
            $filteredModels = $this->filterManager->apply($filter, $models, request());

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
