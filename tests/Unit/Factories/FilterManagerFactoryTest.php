<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Meius\LaravelFilter\Factories\FilterManagerFactory;
use Meius\LaravelFilter\Services\Filter\CachedFilterManager;
use Meius\LaravelFilter\Services\Filter\FilterManager;
use Meius\LaravelFilter\Tests\TestCase;
use Mockery\MockInterface;

class FilterManagerFactoryTest extends TestCase
{
    /**
     * @throws BindingResolutionException
     */
    public function testFilterManagerIsCreatedWhenCacheFileExists(): void
    {
        $this->assertInstanceOf(CachedFilterManager::class, $this->getFactory()->create());
    }

    /**
     * @throws BindingResolutionException
     */
    public function testFilterManagerIsCreatedWhenCacheFileDoesNotExist(): void
    {
        $this->assertInstanceOf(FilterManager::class, $this->getFactory()->create());
    }

    public function testThrowsExceptionWhenBindingResolutionFailsWhenCacheFileExists(): void
    {
        $this->mockApplicationMakeToThrowException();
        $this->expectException(BindingResolutionException::class);
        $this->getFactory()->create();
    }

    public function testThrowsExceptionWhenBindingResolutionFailsWhenCacheFileDoesNotExist(): void
    {
        $this->mockApplicationMakeToThrowException();
        $this->expectException(BindingResolutionException::class);
        $this->getFactory()->create();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('exists')->andReturn(true, false, true);
        });
    }

    protected function mockApplicationMakeToThrowException(): void
    {
        $this->mock(Application::class, function (MockInterface $mock): void {
            $mock->shouldReceive('make')
                ->andThrow(BindingResolutionException::class);
        });
    }

    /**
     * @throws BindingResolutionException
     */
    protected function getFactory(): FilterManagerFactory
    {
        return $this->app->make(FilterManagerFactory::class);
    }
}
