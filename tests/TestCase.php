<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Meius\LaravelFilter\Providers\AppServiceProvider;
use Meius\LaravelFilter\Providers\FilterServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected Request $request;

    /**
     * @var string[] List of destination directories to be cleaned up after tests.
     */
    private array $destinations = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->register(AppServiceProvider::class);
        $this->app->register(FilterServiceProvider::class);

        $this->moveFilters();

        $this->request = RequestFacade::instance();
    }

    protected function addToRequest(array $data): self
    {
        $this->request->merge([
            'filter' => $data,
        ]);

        return $this;
    }

    protected function moveFilters(string $source = '/Support/Filters', string $destination = 'Filters'): self
    {
        $this->moveDirectory(
            __DIR__ . $source,
            $this->app->path($destination)
        );

        return $this;
    }

    protected function moveModels(string $source = '/Support/Models', string $destination = 'Models'): self
    {
        $this->moveDirectory(
            __DIR__ . $source,
            $this->app->path($destination)
        );

        return $this;
    }

    /**
     * Recursively move a directory from source to destination.
     *
     * @param string $source The source directory path.
     * @param string $destination The destination directory path.
     */
    private function moveDirectory(string $source, string $destination): void
    {
        $this->destinations[] = $destination;
        $dir = opendir($source);
        @mkdir($destination);

        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $srcFile = $source . DIRECTORY_SEPARATOR . $file;
                $destFile = $destination . DIRECTORY_SEPARATOR . $file;
                if (is_dir($srcFile)) {
                    $this->moveDirectory($srcFile, $destFile);
                } else {
                    copy($srcFile, $destFile);
                }
            }
        }

        closedir($dir);
    }

    /**
     * Tear down the test environment.
     *
     * Cleans up the moved directories.
     */
    protected function tearDown(): void
    {
        foreach ($this->destinations as $destination) {
            exec(sprintf('rm -rf %s', escapeshellarg($destination)));
        }

        parent::tearDown();
    }
}
