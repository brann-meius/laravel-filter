<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Filters;

use Illuminate\Database\Eloquent\Builder;
use Meius\LaravelFilter\Filters\Filter;

class IdFilter extends Filter
{
    protected string $key = 'id';

    protected function query(Builder $builder, $value): Builder
    {
        return $builder->where('id', '=', $value);
    }
}