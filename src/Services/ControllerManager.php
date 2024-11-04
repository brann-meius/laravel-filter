<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Meius\LaravelFilter\Exceptions\InvalidFilterBindingException;
use Meius\LaravelFilter\Factories\FilterManagerFactory;

class ControllerManager
{
    public function __construct(
        private FilterManagerFactory $filterManagerFactory
    ) {
        //
    }

    /**
     * Handle the application of filters to the specified controller method.
     *
     * @throws InvalidFilterBindingException
     */
    public function handle(Request $request, array $models): void
    {
        try {
            $this->filterManagerFactory
                ->create()
                ->apply($models, $request);
        } catch (BindingResolutionException $exception) {
            throw new InvalidFilterBindingException(
                message: 'Failed to resolve filter manager instance.',
                previous: $exception
            );
        }
    }
}
