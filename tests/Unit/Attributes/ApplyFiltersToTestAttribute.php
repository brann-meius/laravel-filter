<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Attributes;

use Meius\LaravelFilter\Attributes\ApplyFiltersTo;

class ApplyFiltersToTestAttribute extends TestAttribute
{
    protected string $attribute = ApplyFiltersTo::class;
}
