<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit;

use InvalidArgumentException;
use Meius\LaravelFilter\FilterCreator;
use Meius\LaravelFilter\Services\Filter\FilterManager;
use Meius\LaravelFilter\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;

class FilterCreatorTest extends TestCase
{
    public function testCreatesFilterFileSuccessfully(): void
    {
        $this->partialMock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('ensureDirectoryExists')
                ->andReturn(true);
            $mock->shouldReceive('put')
                ->andReturn(true);
        });

        /** @var FilterCreator $filterCreator */
        $filterCreator = $this->app->make(FilterCreator::class);
        $result = $filterCreator->create('TestFilter');

        $this->assertEquals('/path/to/filters/TestFilter.php', $result);
    }

    public function testThrowsExceptionWhenFilterAlreadyExists(): void
    {
        $this->mock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('exists')
                ->andReturn(true);
        });


        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The TestFilter filter already exists.');

        /** @var FilterCreator $filterCreator */
        $filterCreator = $this->app->make(FilterCreator::class);
        $filterCreator->create('TestFilter');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(FilterManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('baseFilterDirectory')
                ->andReturn('/path/to/filters');
        });
    }
}
