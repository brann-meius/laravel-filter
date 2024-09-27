<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Meius\LaravelFilter\Attributes\Settings\OnlyFor;
use Meius\LaravelFilter\Filters\Filter;
use Meius\LaravelFilter\Tests\Support\Http\Models\Post;

return new #[OnlyFor(Post::class)] class extends Filter
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
};