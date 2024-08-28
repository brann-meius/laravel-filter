<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Attributes;

use Meius\LaravelFilter\Attributes\ApplyFiltersTo;

class ApplyFiltersToTest extends AttributeTest
{
    protected string $attribute = ApplyFiltersTo::class;
}
