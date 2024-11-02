<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services\Filter;

use Meius\LaravelFilter\Tests\Support\Models\Comment;
use Meius\LaravelFilter\Tests\Support\Models\Post;
use Meius\LaravelFilter\Tests\Support\Models\User;
use Meius\LaravelFilter\Tests\TestCase;

abstract class TestFilterManager extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->addToRequest([
            'users' => [
                'id' => 1,
                'last_name' => 'Doe',
                'email' => 'ex',
            ],
            'posts' => [
                'id' => 1,
                'title' => 'It`s a story',
                'content' => 'Once upon a time...',
            ],
            'comments' => [
                'id' => 14,
                'title' => 'ggvp',
                'content' => 'Never give up!',
            ],
        ]);
    }

    protected function assertCorrectConnections(): void
    {
        $this->assertTrue(User::hasGlobalScope(User::generateFilterScopeKey('id')));
        $this->assertFalse(User::hasGlobalScope(User::generateFilterScopeKey('title')));
        $this->assertFalse(User::hasGlobalScope(User::generateFilterScopeKey('content')));
        $this->assertFalse(User::hasGlobalScope(User::generateFilterScopeKey('last_name')));
        $this->assertFalse(User::hasGlobalScope(User::generateFilterScopeKey('email')));
        $this->assertFalse(User::hasGlobalScope(User::generateFilterScopeKey('created_at')));
        $this->assertFalse(User::hasGlobalScope(User::generateFilterScopeKey('updated_at')));

        $this->assertTrue(Post::hasGlobalScope(Post::generateFilterScopeKey('id')));
        $this->assertTrue(Post::hasGlobalScope(Post::generateFilterScopeKey('title')));
        $this->assertTrue(Post::hasGlobalScope(Post::generateFilterScopeKey('content')));
        $this->assertFalse(Post::hasGlobalScope(Post::generateFilterScopeKey('created_at')));
        $this->assertFalse(Post::hasGlobalScope(Post::generateFilterScopeKey('updated_at')));

        $this->assertTrue(Comment::hasGlobalScope(Comment::generateFilterScopeKey('id')));
        $this->assertTrue(Comment::hasGlobalScope(Comment::generateFilterScopeKey('content')));
        $this->assertFalse(Comment::hasGlobalScope(Comment::generateFilterScopeKey('title')));
        $this->assertFalse(Comment::hasGlobalScope(Comment::generateFilterScopeKey('created_at')));
        $this->assertFalse(Comment::hasGlobalScope(Comment::generateFilterScopeKey('updated_at')));
    }
}
