<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class Filter implements FilterInterface
{
    protected Model $model;

    /**
     * The key used to identify the filter parameter in the request.
     */
    protected string $key;

    /**
     * Define the query logic for the filter.
     *
     * This method should be implemented by subclasses to specify how the filter
     * should modify the query builder based on the provided value.
     */
    abstract protected function query(Builder $builder, $value): Builder;

    /**
     * Create a new filter instance.
     */
    public function create(string $pathToModel): self
    {
        $this->model = app($pathToModel);

        return $this;
    }

    /**
     * Apply the filter to the request.
     */
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

    /**
     * Determine if the filter can continue.
     */
    protected function canContinue(Request $request): bool
    {
        return true;
    }

    /**
     * Get the scope name for the filter.
     */
    protected function scopeName(): string
    {
        return "filter:{$this->model->getTable()}-by-$this->key";
    }

    /**
     * Get the path from the request.
     */
    final protected function pathFromRequest(): string
    {
        return "filter.{$this->model->getTable()}.$this->key";
    }
}
