<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Filters;

use Illuminate\Database\Eloquent\Builder;
use Meius\LaravelFilter\Attributes\Settings\ExcludeFrom;
use Meius\LaravelFilter\Filters\Filter;
use Meius\LaravelFilter\Tests\Support\Models\User;

#[ExcludeFrom(User::class)]
class ContentFilter extends Filter
{
    protected string $key = 'content';

    protected function query(Builder $builder, $value): Builder
    {
        return $builder->where('content', 'LIKE', "%$value%");
    }
}