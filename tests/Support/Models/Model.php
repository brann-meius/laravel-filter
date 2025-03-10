<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model as ParentModel;
use Meius\LaravelFilter\Helpers\FilterScopeHelper;

/** @phpstan-consistent-constructor */
abstract class Model extends ParentModel
{
    public static function getAllGlobalScopes()
    {
        return static::$globalScopes;
    }

    public static function generateFilterScopeKey(string $key): string
    {
        $model = static::class;

        return FilterScopeHelper::generateName(new $model(), $key);
    }

    public static function hasFilterScopeByKey(string $key): bool
    {
        return static::hasGlobalScope(static::generateFilterScopeKey($key));
    }
}
