<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Helpers;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FinderHelper
{
    public function __construct(private Finder $finder) {}

    /**
     * Configure the Finder instance to search for PHP filter files.
     */
    public function configureFinderFiles(string|array $dirs): Finder
    {
        return $this->finder->files()
            ->in($dirs)
            ->name('*.php');
    }

    /**
     * Get the namespace of a PHP file.
     */
    public function getNamespace(SplFileInfo $file): string
    {
        return $this->extractNamespaceFromFile($file).'\\'.$file->getBasename('.php');
    }

    /**
     * Extract the namespace from a PHP file.
     */
    private function extractNamespaceFromFile(SplFileInfo $file): string
    {
        if (! $file->isReadable()) {
            throw new \RuntimeException('File is not readable.');
        }

        $fileObject = $file->openFile();

        foreach ($fileObject as $line) {
            if (preg_match('/^namespace\s+(.+?);/', $line, $matches)) {
                return $matches[1];
            }
        }

        throw new \RuntimeException('Namespace not found.');
    }
}