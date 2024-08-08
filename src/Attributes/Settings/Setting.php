<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Attributes\Settings;

use Illuminate\Database\Eloquent\Model;

#[\Attribute]
abstract class Setting
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<Model>[]  $models
     */
    public function __construct(string ...$models) {}
}
