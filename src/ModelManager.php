<?php

declare(strict_types=1);

namespace Meius\LaravelFilter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class ModelManager
{
    public function __construct(
        private FilterManager $filterManager,
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
        $directoryIterator = new RecursiveDirectoryIterator(App::path());
        $filterIterator = new RecursiveCallbackFilterIterator($directoryIterator, function ($current, $key, $iterator) use ($ignoredDirectories) {
            /** @var SplFileInfo $current */
            if ($iterator->hasChildren()) {
                foreach ($ignoredDirectories as $ignoredDirectory) {
                    if (str_starts_with($current->getPathname(), $ignoredDirectory)) {
                        return false;
                    }
                }
            }

            return true;
        });

        $iterator = new RecursiveIteratorIterator($filterIterator);

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $namespace = $this->getNamespaceFromFile($file);
                $className = $namespace.'\\'.$file->getBasename('.php');

                if (class_exists($className)) {
                    $reflectionClass = new \ReflectionClass($className);

                    if ($reflectionClass->isSubclassOf(Model::class) && ! $reflectionClass->isAbstract()) {
                        $models[] = $className;
                    }
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
}
