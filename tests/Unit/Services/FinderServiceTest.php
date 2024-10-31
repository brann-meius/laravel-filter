<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services;

use Meius\LaravelFilter\Services\FinderService;
use Meius\LaravelFilter\Tests\TestCase;
use Mockery\MockInterface;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

class FinderServiceTest extends TestCase
{
    public function testThrowsExceptionWhenFileIsNotReadable(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File is not readable.');

        $this->getFinderService()->getNamespace($this->getFile());
    }

    private function getFinderService(): FinderService
    {
        /** @var FinderService $finderService */
        $finderService = $this->app->make(FinderService::class);

        return $finderService;
    }

    private function getFile(): SplFileInfo|MockInterface
    {
        return $this->mock(SplFileInfo::class, function ($mock): void {
            $mock->shouldReceive('isReadable')
                ->andReturn(false);
        });
    }
}
