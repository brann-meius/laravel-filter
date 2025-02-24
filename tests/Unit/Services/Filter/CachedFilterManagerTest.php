<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services\Filter;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Meius\LaravelFilter\Services\Filter\CachedFilterManager;
use Meius\LaravelFilter\Tests\Support\Filters\ContentFilter;
use Meius\LaravelFilter\Tests\Support\Filters\IdFilter;
use Meius\LaravelFilter\Tests\Support\Filters\TitleFilter;
use Meius\LaravelFilter\Tests\Support\Models\Comment;
use Meius\LaravelFilter\Tests\Support\Models\Post;
use Meius\LaravelFilter\Tests\Support\Models\User;
use Mockery\MockInterface;

class CachedFilterManagerTest extends TestFilterManager
{
    /**
     * @throws BindingResolutionException
     */
    public function testAppliesFiltersFromCachePath(): void
    {
        $this->partialMock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('requireOnce')
                ->with(Config::get('filter.cache.path', base_path('bootstrap/cache/filters.php')))
                ->andReturn([
                    User::class => [
                        IdFilter::class,
                    ],
                    Post::class => [
                        IdFilter::class,
                        TitleFilter::class,
                        ContentFilter::class,
                        ContentFilter::class . 'NonExistent',
                    ],
                    Comment::class => [
                        IdFilter::class,
                        ContentFilter::class,
                    ],
                ]);
        });

        /** @var CachedFilterManager $cachedFilterManager */
        $cachedFilterManager = $this->app->make(CachedFilterManager::class);
        $cachedFilterManager->apply([User::class, Post::class, Comment::class], Request::instance());

        $this->assertCorrectConnections();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws BindingResolutionException
     */
    public function testAppliesNoFiltersWhenCacheIsEmpty(): void
    {
        $this->partialMock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('requireOnce')
                ->with(Config::get('filter.cache.path', base_path('bootstrap/cache/filters.php')))
                ->andReturn([
                    User::class => [],
                    Comment::class => [],
                ]);
        });

        /** @var CachedFilterManager $cachedFilterManager */
        $cachedFilterManager = $this->app->make(CachedFilterManager::class);
        $cachedFilterManager->apply([User::class, Post::class, Comment::class], Request::instance());

        $this->assertFalse(User::hasFilterScopeByKey('id'));
        $this->assertFalse(User::hasFilterScopeByKey('title'));
        $this->assertFalse(User::hasFilterScopeByKey('content'));
        $this->assertFalse(User::hasFilterScopeByKey('last_name'));
        $this->assertFalse(User::hasFilterScopeByKey('email'));

        $this->assertFalse(Post::hasFilterScopeByKey('id'));
        $this->assertFalse(Post::hasFilterScopeByKey('title'));
        $this->assertFalse(Post::hasFilterScopeByKey('content'));

        $this->assertFalse(Comment::hasFilterScopeByKey('id'));
        $this->assertFalse(Comment::hasFilterScopeByKey('content'));
        $this->assertFalse(Comment::hasFilterScopeByKey('title'));
    }

    /**
     * @throws BindingResolutionException
     */
    public function testFallsBackToFilterManagerWhenCachePathFails(): void
    {
        $this->partialMock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('requireOnce')
                ->with(Config::get('filter.cache.path', base_path('bootstrap/cache/filters.php')))
                ->andThrow(new FileNotFoundException('Test exception'));
        });

        /** @var CachedFilterManager $cachedFilterManager */
        $cachedFilterManager = $this->app->make(CachedFilterManager::class);
        $cachedFilterManager->apply([User::class, Post::class, Comment::class], Request::instance());

        $this->assertCorrectConnections();
    }
}
