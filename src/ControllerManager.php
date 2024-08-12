<?php

declare(strict_types=1);

namespace Meius\LaravelFilter;

use Illuminate\Routing\Controller;
use Meius\LaravelFilter\Attributes\ApplyFiltersTo;
use Meius\LaravelFilter\Traits\Reflective;
use ReflectionMethod;

final class ControllerManager
{
    use Reflective;

    public function __construct(
        private FilterManager $filterManager,
    ) {}

    /**
     * Handle the application of filters to the specified controller method.
     */
    public function handle(Controller $context, string $method): void
    {
        $reflectionMethod = new ReflectionMethod($context, $method);

        // Check if an attribute exists.
        if (empty($reflectionAttributes = $reflectionMethod->getAttributes(ApplyFiltersTo::class))) {
            return;
        }

        // Check if attributes contain models.
        if (empty($pathsToRequiredModels = $this->parseAttributes($reflectionAttributes))) {
            return;
        }

        foreach ($this->filterManager->filters() as $filter) {
            $this->filterManager->apply($filter, $pathsToRequiredModels, request());
        }
    }
}
