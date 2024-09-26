<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services\Filter;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;

/**
 * Manages the application and retrieval of filters for Eloquent models.
 */
class CachedFilterManager extends FilterManager
{
    private string $cachePath;

    public function __construct(
        protected Filesystem $filesystem,
        protected Finder $finder,
        protected LoggerInterface $logger,
        private FilterManager $filterManager,
    ) {
        parent::__construct($filesystem, $finder, $logger);

        $this->cachePath = Config::get('filter.cache.path', '');
    }

    /**
     * Apply the given filter to the specified models based on the request.
     */
    #[\Override]
    public function apply(array $pathsToModels, Request $request): void
    {
        try {
            $filters = $this->filesystem->requireOnce($this->cachePath);
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to load filters from cache path.', [
                'path' => $this->cachePath,
                'message' => $exception->getMessage(),
            ]);

            $this->filterManager->apply($pathsToModels, $request);

            return;
        }

        foreach ($pathsToModels as $pathToModel) {
            if (empty($filters[$pathToModel])) {
                continue;
            }

            foreach ($filters[$pathToModel] as $pathToFilter) {
                $filter = $this->filter($pathToFilter);

                if ($filter) {
                    $this->applyFilterToModels($filter, [$pathToModel], $request);
                }
            }
        }
    }
}
