<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Console;

use Illuminate\Database\Eloquent\Builder;
use Meius\LaravelFilter\Filters\Filter;
use Meius\LaravelFilter\Providers\AppServiceProvider;
use Meius\LaravelFilter\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Meius\LaravelFilter\Services\Filter\CachedFilterManager;
use Meius\LaravelFilter\Services\ModelManager;
use Meius\LaravelFilter\Console\FilterCacheCommand;
use Mockery\MockInterface;

class FilterCacheCommandTest extends TestCase
{
    public function testCreatesCacheFileWhenFiltersExist(): void
    {
        $this->mock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('put')
                ->andReturn(true);
        });

        $this->mock(CachedFilterManager::class, function (MockInterface $mock) {
            $mock->shouldReceive('filters')
                ->andReturn($this->filters());
            $mock->shouldReceive('filterModelsBySettings')
                ->andReturn($this->models());
        });

        $this->artisan(FilterCacheCommand::class)
            ->expectsOutputToContain('Filters cached successfully.')
            ->assertSuccessful();
    }

    public function testCreatesCacheFileWhenFiltersDoesNotExist(): void
    {
        $this->mock(CachedFilterManager::class, function (MockInterface $mock) {
            $mock->shouldReceive('filters')
                ->andReturn($this->emptyFilters());
        });

        $this->artisan(FilterCacheCommand::class)
            ->expectsOutputToContain('No filters found. Filters cache not generated.')
            ->assertSuccessful();
    }

    public function testFailsToCreateCacheFileWhenExceptionIsThrown(): void
    {
        $this->mock(CachedFilterManager::class, function (MockInterface $mock) {
            $mock->shouldReceive('filters')
                ->andReturn($this->filters());
            $mock->shouldReceive('filterModelsBySettings')
                ->andReturn($this->models());
        });

        $this->mock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('put')
                ->andThrow(new \Exception('Failed to write filters cache file.'));
        });

        $this->artisan(FilterCacheCommand::class)
            ->expectsOutputToContain('Failed to write filters cache file: Failed to write filters cache file.')
            ->assertFailed();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->register(AppServiceProvider::class);

        $this->mock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('delete')
                ->andReturn(true);
        });

        $this->mock(ModelManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('get')
                ->andReturn($this->models());
        });
    }

    private function models(): array
    {
        return [
          'App\Models\Model1',
          'App\Models\Model2',
        ];
    }

    private function filters(): \Generator
    {
        $filter = function (): Filter {
            return new class extends Filter {
                protected function query(Builder $builder, $value): Builder
                {
                    $operator = array_rand(['=', '!=', '>', '<', '>=', '<=']);

                    return $builder->where('column', $operator, $value);
                }
            };
        };

        yield from [
            $filter(),
            $filter(),
            $filter(),
            $filter(),
        ];
    }

    private function emptyFilters(): \Generator
    {
        yield from [];
    }
}