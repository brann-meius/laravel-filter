<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Meius\LaravelFilter\Attributes\Settings\OnlyFor;
use Meius\LaravelFilter\Filters\Filter;
use Meius\LaravelFilter\Tests\Support\Http\Models\Post;

return new #[OnlyFor(Post::class)] class extends Filter
{
    protected string $key = 'title';

    protected function query(Builder $builder, $value): Builder
    {
        return $builder->where('title', 'LIKE', "%$value%");
    }
};