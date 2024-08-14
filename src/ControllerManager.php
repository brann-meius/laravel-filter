<?php

declare(strict_types=1);

namespace Meius\LaravelFilter;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Meius\LaravelFilter\Attributes\ApplyFiltersTo;
use Meius\LaravelFilter\Traits\Reflective;
use Psr\Log\LoggerInterface;
use ReflectionMethod;

final class ControllerManager
{
    use Reflective;

    private array $reflectionCache = [];

    public function __construct(
        private FilterManager $filterManager,
        private LoggerInterface $logger,
    ) {}

    /**
     * Handle the application of filters to the specified controller method.
     */
    public function handle(Controller $context, string $method): void
    {
        $reflectionMethod = $this->getReflectionMethod($context, $method);

        // Check if an attribute exists.
        if (empty($reflectionAttributes = $reflectionMethod->getAttributes(ApplyFiltersTo::class))) {
            return;
        }

        // Check if attributes contain models.
        if (empty($pathsToRequiredModels = $this->parseAttributes($reflectionAttributes))) {
            return;
        }

        if ($this->filterManager->isCacheExist()) {
            try {
                $this->filterManager->applyFiltersFromCache($pathsToRequiredModels, Request::instance());

                return;
            } catch (\Throwable $exception) {
                $this->logger->error('Failed to apply filters from cache.', [
                    'exception' => $exception,
                ]);
            }
        }

        foreach ($this->filterManager->filters() as $filter) {
            $this->filterManager->apply($filter, $pathsToRequiredModels, Request::instance());
        }
    }



    private function getReflectionMethod(Controller $context, string $method): ReflectionMethod
    {
        $cacheKey = get_class($context).'::'.$method;

        if (!isset($this->reflectionCache[$cacheKey])) {
            $this->reflectionCache[$cacheKey] = new ReflectionMethod($context, $method);
        }

        return $this->reflectionCache[$cacheKey];
    }
}
