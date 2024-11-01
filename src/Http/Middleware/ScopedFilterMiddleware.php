<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Meius\LaravelFilter\Attributes\ApplyFiltersTo;
use Meius\LaravelFilter\Exceptions\InvalidFilterBindingException;
use Meius\LaravelFilter\Services\ControllerManager;
use Meius\LaravelFilter\Traits\Reflective;
use ReflectionException;
use ReflectionMethod;

class ScopedFilterMiddleware
{
    use Reflective;

    public function __construct(private ControllerManager $controllerManager)
    {
        //
    }

    /**
     * @throws InvalidFilterBindingException
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $reflectionMethod = $this->getReflectionMethod($request->route());
        } catch (ReflectionException) {
            return $next($request);
        }

        // Check if an attribute exists.
        if (empty($reflectionAttributes = $reflectionMethod->getAttributes(ApplyFiltersTo::class))) {
            return $next($request);
        }

        // Check if attributes contain models.
        if (empty($models = $this->parseAttributes($reflectionAttributes))) {
            return $next($request);
        }

        $this->controllerManager->handle($request, $models);

        return $next($request);
    }

    /**
     * @throws ReflectionException
     */
    private function getReflectionMethod(Route $route): ReflectionMethod
    {
        return new ReflectionMethod(
            $route->getControllerClass(),
            $route->getActionMethod()
        );
    }
}
