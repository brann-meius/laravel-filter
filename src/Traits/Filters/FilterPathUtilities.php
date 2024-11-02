<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Traits\Filters;

use Meius\LaravelFilter\Helpers\FilterScopeHelper;

trait FilterPathUtilities
{
    /**
     * The key used to identify the filter parameter in the request.
     */
    protected string $key;

    /**
     * Generate the scope name for the filter.
     */
    protected function generateFilterScopeName(): string
    {
        return FilterScopeHelper::generateName($this->model, $this->key);
    }

    /**
     * Extract the path from the request.
     */
    protected function extractFilterPathFromRequest(): string
    {
        return "filter.{$this->model->getTable()}.$this->key";
    }
}
