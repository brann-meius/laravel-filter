<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Attributes;

use Illuminate\Database\Eloquent\Model;
use Meius\LaravelFilter\Exceptions\InvalidModelException;

#[\Attribute]
abstract class Setting
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<Model>[]  $models
     *
     * @throws InvalidModelException
     */
    public function __construct(string ...$models)
    {
        if (empty($models)) {
            throw new InvalidModelException('The models array cannot be empty.');
        }

        foreach ($models as $model) {
            if (! class_exists($model)) {
                throw new InvalidModelException("The class {$model} does not exist.");
            }

            if (! is_subclass_of($model, Model::class)) {
                throw new InvalidModelException("The class {$model} must be a subclass of ".Model::class);
            }
        }
    }
}
