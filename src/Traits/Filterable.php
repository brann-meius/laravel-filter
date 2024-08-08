<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Traits;

use Meius\LaravelFilter\ControllerManager;

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
        $manager = app(ControllerManager::class);

        $manager->handle($this, $method);
    }
}
