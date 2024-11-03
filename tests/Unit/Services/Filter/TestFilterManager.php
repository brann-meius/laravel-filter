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
            'c' => [
                'id' => 14,
                'title' => 'ggvp',
                'content' => 'Never give up!',
            ],
        ]);
    }

    protected function assertCorrectConnections(): void
    {
        $this->assertTrue(User::hasFilterScopeByKey('id'));
        $this->assertFalse(User::hasFilterScopeByKey('title'));
        $this->assertFalse(User::hasFilterScopeByKey('content'));
        $this->assertFalse(User::hasFilterScopeByKey('last_name'));
        $this->assertFalse(User::hasFilterScopeByKey('email'));
        $this->assertFalse(User::hasFilterScopeByKey('created_at'));
        $this->assertFalse(User::hasFilterScopeByKey('updated_at'));

        $this->assertTrue(Post::hasFilterScopeByKey('id'));
        $this->assertTrue(Post::hasFilterScopeByKey('title'));
        $this->assertTrue(Post::hasFilterScopeByKey('content'));
        $this->assertFalse(Post::hasFilterScopeByKey('created_at'));
        $this->assertFalse(Post::hasFilterScopeByKey('updated_at'));

        $this->assertTrue(Comment::hasFilterScopeByKey('id'));
        $this->assertTrue(Comment::hasFilterScopeByKey('content'));
        $this->assertFalse(Comment::hasFilterScopeByKey('title'));
        $this->assertFalse(Comment::hasFilterScopeByKey('created_at'));
        $this->assertFalse(Comment::hasFilterScopeByKey('updated_at'));
    }
}
