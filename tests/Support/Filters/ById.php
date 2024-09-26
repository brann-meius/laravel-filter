<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Meius\LaravelFilter\Filters\Filter;

return new class extends Filter
{
    protected string $key = 'id';

    protected function query(Builder $builder, $value): Builder
    {
        return $builder->where('id', '=', $value);
    }
};