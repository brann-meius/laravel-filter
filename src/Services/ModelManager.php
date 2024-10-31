<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use ReflectionClass;
use SplFileInfo;

class ModelManager
{
    private static array $classExistsCache = [];
    private static array $reflectionClassCache = [];

    public function __construct(
        private FinderService $finderService
    ) {
        //
    }

    /**
     * Get all Eloquent models in the application.
     *
     * @return array<class-string<Model>>
     */
    public function getModels(): array
    {
        $models = [];

        /** @var SplFileInfo $file */
        foreach ($this->finderService->configureFinderFiles(App::path()) as $file) {
            try {
                $className = $this->finderService->getNamespace($file);
            } catch (\RuntimeException) {
                continue;
            }

            if ($this->isClassValidModel($className)) {
                $models[] = $className;
            }
        }

        return $models;
    }

    /**
     * Check if a class is a valid Eloquent model.
     */
    private function isClassValidModel(string $class): bool
    {
        if (! $this->isClassExists($class)) {
            return false;
        }

        $reflectionClass = $this->getReflectionClass($class);

        return $this->isEloquentModel($reflectionClass);
    }

    /**
     * Check if a reflection class is an Eloquent model.
     */
    private function isEloquentModel(ReflectionClass $reflection): bool
    {
        return $reflection->isSubclassOf(Model::class) && ! $reflection->isAbstract();
    }

    /**
     * Check if a class exists with caching.
     */
    private function isClassExists(string $class): bool
    {
        return self::$classExistsCache[$class] ??= class_exists($class);
    }

    /**
     * Get the reflection class with caching.
     */
    private function getReflectionClass(string $class): ReflectionClass
    {
        return self::$reflectionClassCache[$class] ??= new ReflectionClass($class);
    }
}
