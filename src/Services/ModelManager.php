<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Meius\LaravelFilter\Services\Filter\FilterManager;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class ModelManager
{
    private static array $classExistsCache = [];
    private static array $reflectionClassCache = [];

    public function __construct(
        private FilterManager $filterManager,
        private Finder $finder,
    ) {}

    /**
     * Get all Eloquent models in the application.
     *
     * @return array<class-string<Model>>
     */
    public function get(): array
    {
        $models = [];
        $ignoredDirectories = array_map('realpath', $this->filterManager->getDirectoriesWithFilters());
        $this->finder->files()
            ->in(App::path())
            ->exclude($ignoredDirectories)
            ->name('*.php');

        /** @var SplFileInfo $file */
        foreach ($this->finder as $file) {
            $namespace = $this->getNamespaceFromFile($file);
            $className = $namespace.'\\'.$file->getBasename('.php');

            if ($this->classExists($className)) {
                $reflectionClass = $this->getReflectionClass($className);

                if ($reflectionClass->isSubclassOf(Model::class) && ! $reflectionClass->isAbstract()) {
                    $models[] = $className;
                }
            }
        }

        return $models;
    }

    /**
     * Extract the namespace from a PHP file.
     */
    protected function getNamespaceFromFile(SplFileInfo $file): string
    {
        $contents = @file_get_contents($file->getPathname());

        if ($contents === false) {
            return '';
        }

        if (preg_match('/^namespace\s+(.+?);/m', $contents, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Check if a class exists with caching.
     */
    private function classExists(string $className): bool
    {
        if (!isset(self::$classExistsCache[$className])) {
            self::$classExistsCache[$className] = class_exists($className);
        }

        return self::$classExistsCache[$className];
    }

    /**
     * Get the reflection class with caching.
     */
    private function getReflectionClass(string $className): \ReflectionClass
    {
        if (!isset(self::$reflectionClassCache[$className])) {
            self::$reflectionClassCache[$className] = new \ReflectionClass($className);
        }

        return self::$reflectionClassCache[$className];
    }
}
