<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Meius\LaravelFilter\Attributes\ApplyFiltersTo;
use Meius\LaravelFilter\Exceptions\InvalidControllerMethodException;
use Meius\LaravelFilter\Exceptions\InvalidFilterBindingException;
use Meius\LaravelFilter\Factories\FilterManagerFactory;
use Meius\LaravelFilter\Traits\Reflective;
use ReflectionException;
use ReflectionMethod;

class ControllerManager
{
    use Reflective;

    public function __construct(
        private FilterManagerFactory $filterManagerFactory
    ) {
        //
    }

    /**
     * Handle the application of filters to the specified controller method.
     *
     * @throws InvalidControllerMethodException
     * @throws InvalidFilterBindingException
     */
    public function handle(Controller $context, string $method): void
    {
        try {
            $reflectionMethod = $this->getReflectionMethod($context, $method);
        } catch (ReflectionException $exception) {
            throw new InvalidControllerMethodException(
                message: 'Failed to retrieve reflection method for controller method.',
                previous: $exception
            );
        }

        // Check if an attribute exists.
        if (empty($reflectionAttributes = $reflectionMethod->getAttributes(ApplyFiltersTo::class))) {
            return;
        }

        // Check if attributes contain models.
        if (empty($models = $this->parseAttributes($reflectionAttributes))) {
            return;
        }

        try {
            $this->filterManagerFactory
                ->create()
                ->apply($models, Request::instance());
        } catch (BindingResolutionException $exception) {
            throw new InvalidFilterBindingException(
                message: 'Failed to resolve filter manager instance.',
                previous: $exception
            );
        }
    }

    /**
     * Retrieve the reflection method for the specified controller method.
     *
     * @throws ReflectionException
     */
    private function getReflectionMethod(Controller $context, string $method): ReflectionMethod
    {
        return new ReflectionMethod($context, $method);
    }
}
