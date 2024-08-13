<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class FilterClearCommand extends Command
{
    protected $signature = 'filter:clear';

    protected $description = 'Remove the filters cache file';

    public function handle(Filesystem $filesystem): int
    {
        $filesystem->delete(
            app()->bootstrapPath('cache/filters.php')
        );

        return self::SUCCESS;
    }
}
