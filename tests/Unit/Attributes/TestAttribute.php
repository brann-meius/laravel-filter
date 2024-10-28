<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Attributes;

use Meius\LaravelFilter\Attributes\Setting;
use Meius\LaravelFilter\Exceptions\InvalidModelException;
use Meius\LaravelFilter\Tests\Support\Http\Controllers\UserController;
use Meius\LaravelFilter\Tests\Support\Models\User;
use Meius\LaravelFilter\Tests\TestCase;

abstract class TestAttribute extends TestCase
{
    protected string $attribute;

    public function testIsAttribute(): void
    {
        $reflection = new \ReflectionClass($this->attribute);

        $this->assertTrue((bool)$reflection->getAttributes(\Attribute::class));
    }

    public function testInstanceCreation(): void
    {
        $this->assertTrue(is_subclass_of($this->attribute, Setting::class));
    }

    public function testAnnotationUsage(): void
    {
        $reflection = new \ReflectionClass($this->attribute);
        $attributes = $reflection->getAttributes(\Attribute::class);

        $this->assertNotEmpty($attributes);
        $this->assertEquals(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE, $attributes[0]->newInstance()->flags);
    }

    public function testAcceptsModel(): void
    {
        new $this->attribute(User::class);
        $this->assertTrue(true);
    }

    public function testThrowsExceptionForNonModel(): void
    {
        $this->expectException(InvalidModelException::class);
        new $this->attribute(UserController::class);
    }

    public function testThrowsExceptionForEmpty(): void
    {
        $this->expectException(InvalidModelException::class);
        new $this->attribute();
    }

    public function testThrowsExceptionForPathNotFound(): void
    {
        $this->expectException(InvalidModelException::class);
        new $this->attribute('Path\NotFound\\' . uniqid());
    }
}
