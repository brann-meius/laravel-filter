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

        $this->assertCorrectConnections();
    }
}
