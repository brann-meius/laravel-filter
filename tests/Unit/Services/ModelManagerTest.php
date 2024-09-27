<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services;

use Illuminate\Support\Facades\App;
use Meius\LaravelFilter\Services\ModelManager;
use Meius\LaravelFilter\Tests\Support\Http\Models\Comment;
use Meius\LaravelFilter\Tests\Support\Http\Models\Post;
use Meius\LaravelFilter\Tests\Support\Http\Models\User;
use Meius\LaravelFilter\Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class ModelManagerTest extends TestCase
{
    public function testReturnsAllEloquentModels(): void
    {
        App::partialMock()
            ->shouldReceive('path')
            ->andReturn(__DIR__ . '/../../Support/');

        /** @var ModelManager $modelManager */
        $modelManager = $this->app->make(ModelManager::class);

        $this->assertTrue(in_array(User::class, $modelManager->get()));
        $this->assertTrue(in_array(Comment::class, $modelManager->get()));
        $this->assertTrue(in_array(Post::class, $modelManager->get()));
        $this->assertFalse(in_array(\ModelWithoutNamespace::class, $modelManager->get()));
    }

    public function testReturnsEmptyArrayWhenNoModelsFound(): void
    {
        /** @var ModelManager $modelManager */
        $modelManager = $this->app->make(ModelManager::class);

        $this->assertEmpty($modelManager->get());
    }

    public function testSkipsNonEloquentClasses(): void
    {
        App::partialMock()
            ->shouldReceive('path')
            ->andReturn(__DIR__ . '/../../Support/');

        $this->partialMock(Finder::class, function (MockInterface $mock) {
            $mock->shouldReceive('getIterator')
                ->andReturn(new \ArrayIterator([
                    Mockery::mock(SplFileInfo::class, function (MockInterface $mock): void {
                        $mock->shouldReceive('getPathname')
                            ->andReturn(__DIR__ . '/../../Support/NonEloquentClass.php');
                        $mock->shouldReceive('getBasename')
                            ->with('.php')
                            ->andReturn('NonEloquentClass.php');
                    }),
                    new SplFileInfo(\ModelWithoutNamespace::class),
                ]));
        });

        /** @var ModelManager $modelManager */
        $modelManager = $this->app->make(ModelManager::class);

        $this->assertEmpty($modelManager->get());
    }
}