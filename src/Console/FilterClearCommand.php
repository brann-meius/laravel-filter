<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

class FilterClearCommand extends Command
{
    protected $signature = 'filter:clear';

    protected $description = 'Remove the filters cache file';

    public function handle(Filesystem $filesystem): int
    {
        try {
            $filesystem->delete(
                Config::get('filter.cache.path')
            );
        } catch (\Throwable $exception) {
            $this->components->error('Failed to clear filters cache file: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info('Filters cache cleared successfully.');

        return self::SUCCESS;
    }
}
