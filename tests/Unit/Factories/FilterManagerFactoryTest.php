<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Meius\LaravelFilter\Factories\FilterManagerFactory;
use Meius\LaravelFilter\Services\Filter\CachedFilterManager;
use Meius\LaravelFilter\Services\Filter\FilterManager;
use Meius\LaravelFilter\Tests\TestCase;
use Mockery\MockInterface;

class FilterManagerFactoryTest extends TestCase
{
    private FilterManagerFactory $factory;

    public function testFilterManagerIsCreatedWhenCacheFileExists(): void
    {
        $this->assertInstanceOf(CachedFilterManager::class, $this->factory->create());
    }

    public function testFilterManagerIsCreatedWhenCacheFileDoesNotExist(): void
    {
        $this->assertInstanceOf(FilterManager::class, $this->factory->create());
    }

    public function testThrowsExceptionWhenBindingResolutionFailsWhenCacheFileExists(): void
    {
        $this->expectException(BindingResolutionException::class);
        $this->factory->create();
    }

    public function testThrowsExceptionWhenBindingResolutionFailsWhenCacheFileDoesNotExist(): void
    {
        $this->expectException(BindingResolutionException::class);
        $this->factory->create();
    }

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->getName() === 'testThrowsExceptionWhenBindingResolutionFailsWhenCacheFileExists'
            || $this->getName() === 'testThrowsExceptionWhenBindingResolutionFailsWhenCacheFileDoesNotExist') {
            $this->mock(Application::class, function (MockInterface $mock) {
                $mock->shouldReceive('make')
                    ->andThrow(BindingResolutionException::class);
            });
        }

        $this->mock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('exists')->andReturn(true, false, true);
        });

        $this->factory = App::make(FilterManagerFactory::class);
    }
}
