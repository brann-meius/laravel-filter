<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Helpers;

use Illuminate\Database\Eloquent\Model;

class FilterScopeHelper
{
    /**
     * Generates a filter scope name based on the provided table and key.
     */
    public static function generateName(Model $model, string $key): string
    {
        return "filter:{$model->getTable()}:by:{$key}";
    }
}
