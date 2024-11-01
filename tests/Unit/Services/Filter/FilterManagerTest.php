<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services\Filter;

use Illuminate\Support\Facades\Request;
use Meius\LaravelFilter\Services\Filter\FilterManager;
use Meius\LaravelFilter\Tests\Support\Models\Comment;
use Meius\LaravelFilter\Tests\Support\Models\Post;
use Meius\LaravelFilter\Tests\Support\Models\User;

class FilterManagerTest extends TestFilterManager
{
    public function testAppliesFiltersToModels(): void
    {
        /** @var FilterManager $filterManager */
        $filterManager = $this->app->make(FilterManager::class);

        $filterManager->apply([
            User::class,
            Post::class,
            Comment::class,
        ], Request::instance());

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
