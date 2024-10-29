<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services;

use Meius\LaravelFilter\Helpers\FinderHelper;
use Meius\LaravelFilter\Services\ModelManager;
use Meius\LaravelFilter\Tests\Support\Models\Comment;
use Meius\LaravelFilter\Tests\Support\Models\Post;
use Meius\LaravelFilter\Tests\Support\Models\User;
use Meius\LaravelFilter\Tests\TestCase;
use Mockery\MockInterface;
use Symfony\Component\Finder\Finder;

class ModelManagerTest extends TestCase
{
    public function testReturnsAllEloquentModels(): void
    {
        $this->moveModels();
        $modelManager = $this->getModelManager();

        $this->assertTrue(in_array(User::class, $modelManager->getModels()));
        $this->assertTrue(in_array(Comment::class, $modelManager->getModels()));
        $this->assertTrue(in_array(Post::class, $modelManager->getModels()));
    }

    public function testReturnsEmptyArrayWhenNoModelsFound(): void
    {
        $modelManager = $this->getModelManager();

        $this->assertEmpty($modelManager->getModels());
    }

    public function testSkipsNonEloquentClasses(): void
    {
        $this->moveModels('/Support/ModelsWithoutNamespace');
        $modelManager = $this->getModelManager();

        $this->assertEmpty($modelManager->getModels());
    }

    public function testModelDoesNotExist(): void
    {
        $this->moveModels();
        $this->mock(FinderHelper::class, function (MockInterface $mock): void {
            $mock->shouldReceive('configureFinderFiles')
                ->andReturn(
                    Finder::create()
                        ->files()
                        ->in($this->app->path())
                );
            $mock->shouldReceive('getNamespace')
                ->andReturn('App\Models\NonExistentModel' . uniqid());
        });

        $modelManager = $this->getModelManager();

        $this->assertEmpty($modelManager->getModels());
    }

    protected function getModelManager(): ModelManager
    {
        /** @var ModelManager $modelManager */
        $modelManager = $this->app->make(ModelManager::class);

        return $modelManager;
    }
}
