<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services\Filter;

use Illuminate\Contracts\Container\BindingResolutionException;
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
                ->with(Config::get('filter.cache.path'))
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

        /** @var CachedFilterManager $cachedFilterManager*/
        $cachedFilterManager = $this->app->make(CachedFilterManager::class);
        $cachedFilterManager->apply([User::class, Post::class, Comment::class], Request::instance());

        $this->assertCorrectConnections();
    }

    /**
     * @throws BindingResolutionException
     */
    public function testAppliesNoFiltersWhenCacheIsEmpty(): void
    {
        $this->partialMock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('requireOnce')
                ->with(Config::get('filter.cache.path'))
                ->andReturn([
                    User::class => [],
                    Comment::class => [],
                ]);
        });

        /** @var CachedFilterManager $cachedFilterManager*/
        $cachedFilterManager = $this->app->make(CachedFilterManager::class);
        $cachedFilterManager->apply([User::class, Post::class, Comment::class], Request::instance());

        $this->assertFalse(User::hasGlobalScope(User::generateFilterScopeKey('id')));
        $this->assertFalse(User::hasGlobalScope(User::generateFilterScopeKey('title')));
        $this->assertFalse(User::hasGlobalScope(User::generateFilterScopeKey('content')));
        $this->assertFalse(User::hasGlobalScope(User::generateFilterScopeKey('last_name')));
        $this->assertFalse(User::hasGlobalScope(User::generateFilterScopeKey('email')));

        $this->assertFalse(Post::hasGlobalScope(Post::generateFilterScopeKey('id')));
        $this->assertFalse(Post::hasGlobalScope(Post::generateFilterScopeKey('title')));
        $this->assertFalse(Post::hasGlobalScope(Post::generateFilterScopeKey('content')));

        $this->assertFalse(Comment::hasGlobalScope(Comment::generateFilterScopeKey('id')));
        $this->assertFalse(Comment::hasGlobalScope(Comment::generateFilterScopeKey('content')));
        $this->assertFalse(Comment::hasGlobalScope(Comment::generateFilterScopeKey('title')));
    }

    /**
     * @throws BindingResolutionException
     */
    public function testFallsBackToFilterManagerWhenCachePathFails(): void
    {
        $this->partialMock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('requireOnce')
                ->with(Config::get('filter.cache.path', ''))
                ->andThrow(new \Exception('Test exception'));
        });

        /** @var CachedFilterManager $cachedFilterManager*/
        $cachedFilterManager = $this->app->make(CachedFilterManager::class);
        $cachedFilterManager->apply([User::class, Post::class, Comment::class], Request::instance());

        $this->assertCorrectConnections();
    }
}
