<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services\Filter;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Meius\LaravelFilter\Providers\FilterServiceProvider;
use Meius\LaravelFilter\Services\Filter\CachedFilterManager;
use Meius\LaravelFilter\Tests\Support\Http\Models\Comment;
use Meius\LaravelFilter\Tests\Support\Http\Models\Post;
use Meius\LaravelFilter\Tests\Support\Http\Models\User;
use Mockery;
use Mockery\MockInterface;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CachedFilterManagerTest extends TestFilterManager
{
    public function testAppliesFiltersFromCachePath(): void
    {
        $this->partialMock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('requireOnce')
                ->with(Config::get('filter.cache.path'))
                ->andReturn([
                    User::class => [
                        __DIR__ . '/../../../Support/Filters/ById.php',
                    ],
                    Post::class => [
                        __DIR__ . '/../../../Support/Filters/ById.php',
                        __DIR__ . '/../../../Support/Filters/ByTitle.php',
                        __DIR__ . '/../../../Support/Filters/ByContent.php',
                    ],
                    Comment::class => [
                        __DIR__ . '/../../../Support/Filters/ById.php',
                        __DIR__ . '/../../../Support/Filters/ByContent.php',
                    ],
                ]);
        });

        /** @var CachedFilterManager $cachedFilterManager*/
        $cachedFilterManager = $this->app->make(CachedFilterManager::class);
        $cachedFilterManager->apply([User::class, Post::class, Comment::class], Request::instance());

        $this->assertTrue(User::hasGlobalScope('filter:users-by-id'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-title'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-content'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-last_name'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-email'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-created_at'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-updated_at'));

        $this->assertTrue(Post::hasGlobalScope('filter:posts-by-id'));
        $this->assertTrue(Post::hasGlobalScope('filter:posts-by-title'));
        $this->assertTrue(Post::hasGlobalScope('filter:posts-by-content'));
        $this->assertFalse(Post::hasGlobalScope('filter:posts-by-created_at'));
        $this->assertFalse(Post::hasGlobalScope('filter:posts-by-updated_at'));

        $this->assertTrue(Comment::hasGlobalScope('filter:comments-by-id'));
        $this->assertTrue(Comment::hasGlobalScope('filter:comments-by-content'));
        $this->assertFalse(Comment::hasGlobalScope('filter:comments-by-title'));
        $this->assertFalse(Comment::hasGlobalScope('filter:comments-by-created_at'));
        $this->assertFalse(Comment::hasGlobalScope('filter:comments-by-updated_at'));
    }

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

        $this->assertFalse(User::hasGlobalScope('filter:users-by-id'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-title'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-content'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-last_name'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-email'));

        $this->assertFalse(Post::hasGlobalScope('filter:posts-by-id'));
        $this->assertFalse(Post::hasGlobalScope('filter:posts-by-title'));
        $this->assertFalse(Post::hasGlobalScope('filter:posts-by-content'));

        $this->assertFalse(Comment::hasGlobalScope('filter:comments-by-id'));
        $this->assertFalse(Comment::hasGlobalScope('filter:comments-by-content'));
        $this->assertFalse(Comment::hasGlobalScope('filter:comments-by-title'));
    }

    public function testFallsBackToFilterManagerWhenCachePathFails(): void
    {
        $this->app->register(FilterServiceProvider::class);

        Log::expects('error')
            ->once()
            ->with('Failed to load filters from cache path.',
                Mockery::on(function ($context): bool {
                    return isset($context['message']) && $context['message'] === 'Test exception';
                }));

        $this->partialMock(Filesystem::class, function (MockInterface $mock): void {
            $mock->shouldReceive('requireOnce')
                ->with(Config::get('filter.cache.path', ''))
                ->andThrow(new \Exception('Test exception'));
        });

        /** @var CachedFilterManager $cachedFilterManager*/
        $cachedFilterManager = $this->app->make(CachedFilterManager::class);
        $cachedFilterManager->apply([User::class, Post::class, Comment::class], Request::instance());

        $this->assertTrue(User::hasGlobalScope('filter:users-by-id'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-title'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-content'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-last_name'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-email'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-created_at'));
        $this->assertFalse(User::hasGlobalScope('filter:users-by-updated_at'));

        $this->assertTrue(Post::hasGlobalScope('filter:posts-by-id'));
        $this->assertTrue(Post::hasGlobalScope('filter:posts-by-title'));
        $this->assertTrue(Post::hasGlobalScope('filter:posts-by-content'));
        $this->assertFalse(Post::hasGlobalScope('filter:posts-by-created_at'));
        $this->assertFalse(Post::hasGlobalScope('filter:posts-by-updated_at'));

        $this->assertTrue(Comment::hasGlobalScope('filter:comments-by-id'));
        $this->assertTrue(Comment::hasGlobalScope('filter:comments-by-content'));
        $this->assertFalse(Comment::hasGlobalScope('filter:comments-by-title'));
        $this->assertFalse(Comment::hasGlobalScope('filter:comments-by-created_at'));
        $this->assertFalse(Comment::hasGlobalScope('filter:comments-by-updated_at'));
    }
}