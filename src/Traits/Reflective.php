<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Traits;

use ReflectionAttribute;

trait Reflective
{
    /**
     * @param  array<ReflectionAttribute>  $reflectionAttributes
     */
    private function parseAttributes(array $reflectionAttributes): array
    {
        $attributes = [];

        foreach ($reflectionAttributes as $reflectionAttribute) {
            $attributes = array_merge($attributes, $reflectionAttribute->getArguments());
        }

        return array_unique($attributes);
    }
}
