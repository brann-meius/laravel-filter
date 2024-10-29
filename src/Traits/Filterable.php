<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Traits;

use Illuminate\Support\Facades\App;
use Meius\LaravelFilter\Exceptions\InvalidControllerMethodException;
use Meius\LaravelFilter\Exceptions\InvalidFilterBindingException;
use Meius\LaravelFilter\Services\ControllerManager;

trait Filterable
{
    /**
     * @throws InvalidFilterBindingException
     * @throws InvalidControllerMethodException
     */
    #[\Override]
    public function callAction($method, $parameters)
    {
        $this->applyFilters($method);

        return parent::callAction($method, $parameters);
    }

    /**
     * @throws InvalidFilterBindingException
     * @throws InvalidControllerMethodException
     */
    private function applyFilters(string $method): void
    {
        /* @var ControllerManager $manager */
        $manager = App::make(ControllerManager::class);

        $manager->handle($this, $method);
    }
}
