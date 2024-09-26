<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Console;

use Meius\LaravelFilter\Providers\AppServiceProvider;
use Meius\LaravelFilter\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;

class FilterClearCommandTest extends TestCase
{
    public function testClearsCacheFileSuccessfully(): void
    {
        $this->mock(Filesystem::class, function(MockInterface $mock): void {
            $mock->shouldReceive('delete')
                ->andReturn(true);
        });

        $this->artisan('filter:clear')
            ->expectsOutputToContain('Filters cache cleared successfully.')
            ->assertSuccessful();
    }

    public function testFailsToClearCacheFileWhenExceptionIsThrown(): void
    {
        $this->mock(Filesystem::class, function(MockInterface $mock): void {
            $mock->shouldReceive('delete')
                ->andThrow(new \Exception('Failed to clear filters cache file.'));
        });

        $this->artisan('filter:clear')
            ->expectsOutputToContain('Failed to clear filters cache file: Failed to clear filters cache file.')
            ->assertFailed();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->register(AppServiceProvider::class);
    }
}