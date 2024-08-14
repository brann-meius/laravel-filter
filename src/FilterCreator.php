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

    /**
     * Create a new filter file with the given name.
     */
    public function create(string $name): string
    {
        $this->ensureFilterDoesntAlreadyExist($name);

        $path = $this->path($name);

        $this->filesystem->ensureDirectoryExists(dirname($path));
        $this->filesystem->put($path, $this->stub());

        return $this->path($name);
    }

    /**
     * Ensure that a filter with the given name doesn't already exist.
     *
     * @throws InvalidArgumentException
     */
    protected function ensureFilterDoesntAlreadyExist(string $name): void
    {
        if ($this->filesystem->exists($this->path($name))) {
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

    /**
     * Get the stub content for the filter.
     */
    protected function stub(): string
    {
        return $this->filesystem->get(__DIR__.'/filter.stub');
    }
}
