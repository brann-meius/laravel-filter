<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class Filter implements FilterInterface
{
    protected Model $model;

    protected string $key;

    abstract protected function query(Builder $builder, $value): Builder;

    public function create(string $pathToModel): self
    {
        $this->model = app($pathToModel);

        return $this;
    }

    public function apply(Request $request): void
    {
        if (empty($this->model)) {
            return;
        }

        if (! $this->canContinue($request)) {
            return;
        }

        $request->whenHas($this->pathFromRequest(), function (mixed $field): void {
            $this->model::addGlobalScope(
                $this->scopeName(),
                fn (Builder $builder): Builder => $this->query($builder, $field)
            );
        });
    }

    protected function canContinue(Request $request): bool
    {
        return true;
    }

    protected function pathFromRequest(): string
    {
        return "filter.{$this->model->getTable()}.$this->key";
    }

    protected function scopeName(): string
    {
        return "filter:{$this->model->getTable()}-by-$this->key";
    }
}
