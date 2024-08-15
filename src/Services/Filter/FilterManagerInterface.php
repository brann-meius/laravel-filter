<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services\Filter;

use Generator;
use Illuminate\Http\Request;
use Meius\LaravelFilter\Filters\FilterInterface;

interface FilterManagerInterface
{
    public function apply(array $pathsToModels, Request $request): void;

    public function filters(): Generator;

    public function addFiltersDirectory(string ...$paths): self;

    public function filterModelsBySettings(array $pathsToModels, FilterInterface $filter): array;

    public function baseFilterDirectory(): string;

    public function getDirectoriesWithFilters(): array;
}
