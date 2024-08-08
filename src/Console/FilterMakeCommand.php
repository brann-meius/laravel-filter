<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Console;

use Illuminate\Console\Command;
use Meius\LaravelFilter\FilterCreator;

class FilterMakeCommand extends Command
{
    protected $signature = 'make:filter {name : The name of the filter}';

    protected $description = 'Create a new filter';

    public function handle(FilterCreator $filterCreator): int
    {
        try {
            $filterCreator->create($this->argument('name'));
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
