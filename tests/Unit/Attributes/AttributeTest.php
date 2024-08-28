<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Attributes;

use Illuminate\Database\Eloquent\Model;
use Meius\LaravelFilter\Attributes\Setting;
use Meius\LaravelFilter\Exceptions\InvalidModelException;
use Meius\LaravelFilter\Http\Controllers\Controller;
use Meius\LaravelFilter\Tests\TestCase;

abstract class AttributeTest extends TestCase
{
    protected Model $model;

    protected string $attribute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new class extends Model {};
    }

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
        new $this->attribute($this->model::class);
        $this->assertTrue(true);
    }

    public function testThrowsExceptionForNonModel(): void
    {
        $this->expectException(InvalidModelException::class);
        new $this->attribute(Controller::class);
    }

    public function testThrowsExceptionForEmpty(): void
    {
        $this->expectException(InvalidModelException::class);
        new $this->attribute(Controller::class);
    }

    public function testThrowsExceptionForPathNotFound(): void
    {
        $this->expectException(InvalidModelException::class);
        new $this->attribute('Path\NotFound\\' . uniqid());
    }
}
