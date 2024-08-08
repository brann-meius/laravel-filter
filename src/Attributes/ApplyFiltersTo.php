<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Attributes;

use Illuminate\Database\Eloquent\Model;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ApplyFiltersTo
{
    /**
     * @param  class-string<Model>[]  $models
     */
    public function __construct(string ...$models) {}
}
