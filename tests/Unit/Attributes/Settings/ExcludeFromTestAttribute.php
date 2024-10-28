<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Attributes\Settings;

use Meius\LaravelFilter\Attributes\Settings\ExcludeFrom;
use Meius\LaravelFilter\Tests\Unit\Attributes\TestAttribute;

class ExcludeFromTestAttribute extends TestAttribute
{
    protected string $attribute = ExcludeFrom::class;

    public function testAnnotationUsage(): void
    {
        $reflection = new \ReflectionClass($this->attribute);
        $attributes = $reflection->getAttributes(\Attribute::class);

        $this->assertNotEmpty($attributes);
        $this->assertEquals(\Attribute::TARGET_CLASS, $attributes[0]->newInstance()->flags);
    }
}
