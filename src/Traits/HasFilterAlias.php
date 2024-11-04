<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Traits;

/**
 * Provides functionality to handle filter aliases for models.
 *
 * @property string $filterAlias
 */
trait HasFilterAlias
{
    /**
     * Check if the model has a filter alias.
     */
    public function hasFilterAlias(): bool
    {
        return isset($this->filterAlias);
    }

    /**
     * Get the filter alias for the model.
     *
     * If the filter alias is not set, the table name will be returned.
     */
    public function getFilterAlias(): string
    {
        if (! $this->hasFilterAlias()) {
            return $this->getTable();
        }

        return $this->filterAlias;
    }
}
