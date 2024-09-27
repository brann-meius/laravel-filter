<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Meius\LaravelFilter\Attributes\Settings\ExcludeFor;
use Meius\LaravelFilter\Filters\Filter;
use Meius\LaravelFilter\Tests\Support\Http\Models\User;

return new #[ExcludeFor(User::class)] class extends Filter
{
    protected string $key = 'content';

    protected function query(Builder $builder, $value): Builder
    {
        return $builder->where('content', 'LIKE', "%$value%");
    }
};