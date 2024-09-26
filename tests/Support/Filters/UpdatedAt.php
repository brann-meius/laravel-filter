<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Meius\LaravelFilter\Attributes\Settings\OnlyFor;
use Meius\LaravelFilter\Filters\Filter;
use Meius\LaravelFilter\Tests\Support\Http\Models\Post;

return new #[OnlyFor(Post::class)] class extends Filter
{
    protected string $key = 'updated_at';

    public function __invoke(string $pathToModel, Request $request): Filter
    {
        $this->apply($request);

        return $this;
    }

    protected function query(Builder $builder, $value): Builder
    {
        return $builder->whereDate('updated_at', '=', $value);
    }
};