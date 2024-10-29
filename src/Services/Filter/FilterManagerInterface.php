<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services\Filter;

use Generator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Meius\LaravelFilter\Filters\FilterInterface;

/**
 * Defines the contract for managing filters in the Laravel Filter package.
 */
interface FilterManagerInterface
{
    /**
     * Apply filters to the given models based on the request.
     *
     * @param class-string<Model>[] $models
     */
    public function apply(array $models, Request $request): void;

    /**
     * Get a generator for iterating over the filters.
     */
    public function filters(): Generator;

    /**
     * Filter the given models based on the settings of the filter class.
     *
     * This method checks if the filter class has specific setting attributes.
     * If it does, it filters the models accordingly using those attributes.
     * Otherwise, it returns the original list of models.
     *
     * @param class-string<Model>[] $models
     * @return class-string<Model>[]
     */
    public function filterModelsBySettings(array $models, FilterInterface $filter): array;

    /**
     * Get the base directory for filters.
     */
    public function baseFilterDirectory(): string;
}
