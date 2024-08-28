<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Factories;

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
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(Filesystem::class, function (MockInterface $mock) {
            $mock->shouldReceive('exists')->andReturn(true, false);
        });

        $this->factory = App::make(FilterManagerFactory::class);
    }

    public function testFilterManagerIsCreatedWhenCacheFileExists(): void
    {
        $this->assertInstanceOf(CachedFilterManager::class, $this->factory->create());
    }

    public function filterManagerIsCreatedWhenCacheFileDoesNotExist(): void
    {
        $this->assertInstanceOf(FilterManager::class, $this->factory->create());
    }
}
