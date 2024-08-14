<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;

class FilterClearCommand extends Command
{
    protected $signature = 'filter:clear';

    protected $description = 'Remove the filters cache file';

    public function handle(Filesystem $filesystem): int
    {
        $filesystem->delete(
            App::bootstrapPath('cache/filters.php')
        );

        $this->components->info('Filters cache cleared successfully.');

        return self::SUCCESS;
    }
}
