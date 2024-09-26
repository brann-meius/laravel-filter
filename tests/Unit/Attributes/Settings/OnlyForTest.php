<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Attributes\Settings;

use Meius\LaravelFilter\Attributes\Settings\OnlyFor;

class OnlyForTest extends ExcludeForTestAttribute
{
    protected string $attribute = OnlyFor::class;
}
