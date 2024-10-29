<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Filters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Interface FilterInterface
 *
 * Defines the contract for filter classes used in the Laravel Filter package.
 */
interface FilterInterface
{
    /**
     * Initialize the filter with the given model path and request.
     *
     * @param class-string<Model> $model
     */
    public function __invoke(string $model, Request $request): void;

    /**
     * Get the list of models for which this filter should be applied.
     *
     * @return class-string<Model>[]
     */
    public function onlyFor(): array;

    /**
     * Get the list of models for which this filter should be excluded.
     *
     * @return class-string<Model>[]
     */
    public function excludeFrom(): array;

    /**
     * Determine if the filter has setting attributes.
     *
     * @return bool True if the filter has setting attributes, false otherwise.
     */
    public function hasSettingAttributes(): bool;
}
