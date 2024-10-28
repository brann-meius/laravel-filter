<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Helpers;

use Meius\LaravelFilter\Helpers\FinderHelper;
use Meius\LaravelFilter\Tests\TestCase;
use Mockery\MockInterface;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

class FinderHelperTest extends TestCase
{
    public function testThrowsExceptionWhenFileIsNotReadable(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File is not readable.');

        $this->getFinderHelper()->getNamespace($this->getFile());
    }

    private function getFinderHelper(): FinderHelper
    {
        /** @var FinderHelper $finderHelper */
        $finderHelper = $this->app->make(FinderHelper::class);

        return $finderHelper;
    }

    private function getFile(): SplFileInfo|MockInterface
    {
        return $this->mock(SplFileInfo::class, function ($mock): void {
            $mock->shouldReceive('isReadable')
                ->andReturn(false);
        });
    }
}