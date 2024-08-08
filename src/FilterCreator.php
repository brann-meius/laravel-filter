<?php

declare(strict_types=1);

namespace Meius\LaravelFilter;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;

class FilterCreator
{
    public function __construct(
        private FilterManager $filterManager,
        private Filesystem $filesystem,
    ) {}

    public function create(string $name): void
    {
        $this->ensureFilterDoesntAlreadyExist($name);

        $path = $this->path($name);

        $this->filesystem->ensureDirectoryExists(dirname($path));
        $this->filesystem->put($path, $this->stub());
    }

    /**
     * Ensure that a filter with the given name doesn't already exist.
     *
     * @throws InvalidArgumentException
     */
    protected function ensureFilterDoesntAlreadyExist(string $name): void
    {
        if (! empty($this->filesystem->glob($this->path($name)))) {
            throw new InvalidArgumentException("The {$name} filter already exists.");
        }
    }

    /**
     * Get the full path to the filter.
     */
    protected function path(string $name): string
    {
        return $this->filterManager->baseFilterDirectory().'/'.$name.'.php';
    }

    protected function stub(): string
    {
        return $this->filesystem->get(__DIR__.'/filter.stub');
    }
}
