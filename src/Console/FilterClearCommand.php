<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'filter:clear',
    description: 'Remove the filters cache file',
)]
class FilterClearCommand extends Command
{
    public function handle(Filesystem $filesystem): int
    {
        try {
            $filesystem->delete(
                Config::get('filter.cache.path', base_path('bootstrap/cache/filters.php'))
            );
        } catch (\Throwable $exception) {
            $this->components->error('Failed to clear filters cache file: ' . $exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info('Filters cache cleared successfully.');

        return self::SUCCESS;
    }
}
