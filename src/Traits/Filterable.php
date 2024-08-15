<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Traits;

use Illuminate\Support\Facades\App;
use Meius\LaravelFilter\Services\ControllerManager;

trait Filterable
{
    #[\Override]
    public function callAction($method, $parameters)
    {
        $this->applyFilters($method);

        return parent::callAction($method, $parameters);
    }

    private function applyFilters(string $method): void
    {
        /* @var ControllerManager $manager */
        $manager = App::make(ControllerManager::class);

        $manager->handle($this, $method);
    }
}
