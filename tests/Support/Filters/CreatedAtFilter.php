<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Meius\LaravelFilter\Attributes\Settings\OnlyFor;
use Meius\LaravelFilter\Filters\Filter;
use Meius\LaravelFilter\Tests\Support\Models\Post;

#[OnlyFor(Post::class)]
class CreatedAtFilter extends Filter
{
    protected string $key = 'created_at';

    protected function query(Builder $builder, $value): Builder
    {
        return $builder->whereDate('created_at', '=', $value);
    }

    protected function canContinue(Request $request): bool
    {
        return false;
    }
}