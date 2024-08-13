<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Attributes;

use Illuminate\Database\Eloquent\Model;

#[\Attribute]
abstract class Setting
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<Model>[]  $models
     */
    public function __construct(string ...$models)
    {
        foreach ($models as $model) {
            if (! is_subclass_of($model, Model::class)) {
                throw new \InvalidArgumentException("The class {$model} must be a subclass of ".Model::class);
            }
        }
    }
}
