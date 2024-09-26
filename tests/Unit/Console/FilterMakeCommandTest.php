<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Console;

use Meius\LaravelFilter\FilterCreator;
use Meius\LaravelFilter\Providers\AppServiceProvider;
use Meius\LaravelFilter\Tests\TestCase;
use Mockery\MockInterface;

class FilterMakeCommandTest extends TestCase
{
    public function testCreatesFilterSuccessfully(): void
    {
        $this->mock(FilterCreator::class, function (MockInterface $mock): void {
            $mock->shouldReceive('create')->andReturn('/path/to/filter');
        });

        $this->artisan('make:filter', ['name' => 'TestFilter'])
            ->expectsOutputToContain('Filter [/path/to/filter] created successfully.')
            ->assertSuccessful();
    }

    public function testFailsToCreateFilterWhenExceptionIsThrown(): void
    {
        $this->mock(FilterCreator::class, function (MockInterface $mock): void {
            $mock->shouldReceive('create')->andThrow(new \Exception('Failed to create filter.'));
        });

        $this->artisan('make:filter', ['name' => 'TestFilter'])
            ->expectsOutputToContain('Failed to create filter.')
            ->assertFailed();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->register(AppServiceProvider::class);
    }
}