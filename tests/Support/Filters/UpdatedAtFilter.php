<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Filters;

use Illuminate\Database\Eloquent\Builder;
use Meius\LaravelFilter\Filters\Filter;
use Meius\LaravelFilter\Tests\Support\Models\Post;
use Meius\LaravelFilter\Tests\Support\Models\User;

class UpdatedAtFilter extends Filter
{
    protected array $onlyFor = [
        Post::class,
        User::class,
    ];

    protected array $excludeFrom = [
        User::class,
    ];

    protected string $key = 'updated_at';

    protected function query(Builder $builder, $value): Builder
    {
        return $builder->whereDate('updated_at', '=', $value);
    }
}
