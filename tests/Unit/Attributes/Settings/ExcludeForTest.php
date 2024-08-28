<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Attributes\Settings;

use Meius\LaravelFilter\Attributes\Settings\ExcludeFor;
use Meius\LaravelFilter\Tests\Unit\Attributes\AttributeTest;

class ExcludeForTest extends AttributeTest
{
    protected string $attribute = ExcludeFor::class;

    public function testAnnotationUsage(): void
    {
        $reflection = new \ReflectionClass($this->attribute);
        $attributes = $reflection->getAttributes(\Attribute::class);

        $this->assertNotEmpty($attributes);
        $this->assertEquals(\Attribute::TARGET_CLASS, $attributes[0]->newInstance()->flags);
    }
}
