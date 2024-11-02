<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model as ParentModel;
use Meius\LaravelFilter\Helpers\FilterScopeHelper;

class Model extends ParentModel
{
    public static function getAllGlobalScopes()
    {
        return static::$globalScopes;
    }

    public static function generateFilterScopeKey(string $key): string
    {
        return FilterScopeHelper::generateName(new static(), $key);
    }
}
