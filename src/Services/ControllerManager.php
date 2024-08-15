<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Meius\LaravelFilter\Attributes\ApplyFiltersTo;
use Meius\LaravelFilter\Factories\FilterManagerFactory;
use Meius\LaravelFilter\Traits\Reflective;
use Psr\Log\LoggerInterface;
use ReflectionMethod;

final class ControllerManager
{
    use Reflective;

    private array $reflectionCache = [];

    public function __construct(
        private FilterManagerFactory $filterManagerFactory,
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

        try {
            $this->filterManagerFactory
                ->create()
                ->apply($pathsToRequiredModels, Request::instance());
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to apply filters.', [
                'exception' => $exception,
            ]);
        }
    }

    /**
     * Retrieve the reflection method for the specified controller method.
     */
    private function getReflectionMethod(Controller $context, string $method): ReflectionMethod
    {
        $cacheKey = $context::class.'::'.$method;

        if (!isset($this->reflectionCache[$cacheKey])) {
            $this->reflectionCache[$cacheKey] = new ReflectionMethod($context, $method);
        }

        return $this->reflectionCache[$cacheKey];
    }
}
