<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Filters;

use Illuminate\Database\Eloquent\Builder;
use Meius\LaravelFilter\Attributes\Settings\OnlyFor;
use Meius\LaravelFilter\Filters\Filter;
use Meius\LaravelFilter\Tests\Support\Models\Post;

#[OnlyFor(Post::class)]
class TitleFilter extends Filter
{
    protected string $key = 'title';

    protected function query(Builder $builder, $value): Builder
    {
        return $builder->where('title', 'LIKE', "%$value%");
    }
}