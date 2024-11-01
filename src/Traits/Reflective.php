<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Traits;

use Meius\LaravelFilter\Attributes\ApplyFiltersTo;
use ReflectionAttribute;

trait Reflective
{
    /**
     * @param ReflectionAttribute[] $reflectionAttributes
     */
    private function extractModelsFromAttributes(array $reflectionAttributes): array
    {
        $nestedModels = [];

        foreach ($reflectionAttributes as $reflectionAttribute) {
            /** @var ApplyFiltersTo $attribute */
            $attribute = $reflectionAttribute->newInstance();
            $nestedModels[] = $attribute->getModels();
        }

        return array_unique(
            array_merge(...$nestedModels)
        );
    }
}
