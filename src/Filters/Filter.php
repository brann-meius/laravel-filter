<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Meius\LaravelFilter\Traits\Filters\FilterCriteria;
use Meius\LaravelFilter\Traits\Filters\FilterPathUtilities;

abstract class Filter implements FilterInterface
{
    use FilterCriteria;
    use FilterPathUtilities;

    protected Model $model;

    /**
     * Define the query logic for the filter.
     *
     * This method should be implemented by subclasses to specify how the filter
     * should modify the query builder based on the provided value.
     */
    abstract protected function query(Builder $builder, $value): Builder;

    public function __invoke(string $model, Request $request): void
    {
        if (! $this->initializeModel($model)) {
            return;
        }

        $this->apply($request);
    }

    /**
     * Determine if the filter can continue.
     */
    protected function canContinue(Request $request): bool
    {
        return true;
    }

    /**
     * Initialize the model instance.
     *
     * @param class-string<Model> $model
     */
    private function initializeModel(string $model): bool
    {
        if (! class_exists($model) || ! is_subclass_of($model, Model::class)) {
            return false;
        }

        $this->model = App::make($model);

        return true;
    }

    /**
     * Apply filters if applicable.
     */
    private function apply(Request $request): void
    {
        if (! $this->canContinue($request)) {
            return;
        }

        $request->whenHas(
            $this->extractFilterPathFromRequest(),
            function (mixed $field): void {
                $this->addGlobalScope($field);
            }
        );
    }


    /**
     * Add a global scope for the model with the given field.
     */
    private function addGlobalScope(mixed $field): void
    {
        $this->model::addGlobalScope(
            $this->generateFilterScopeName(),
            fn (Builder $builder): Builder => $this->query($builder, $field)
        );
    }
}
