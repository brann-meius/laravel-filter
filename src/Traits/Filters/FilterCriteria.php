<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Traits\Filters;

use Illuminate\Database\Eloquent\Model;
use Meius\LaravelFilter\Attributes\Settings\ExcludeFrom;
use Meius\LaravelFilter\Attributes\Settings\OnlyFor;
use Meius\LaravelFilter\Traits\Reflective;
use ReflectionClass;

trait FilterCriteria
{
    use Reflective;

    /**
     * The models to which the filter should exclusively apply.
     *
     * @var Model[]
     */
    protected array $onlyFor = [];

    /**
     * The models to which the filter should not be applied.
     *
     * @var Model[]
     */
    protected array $excludeFrom = [];

    public function onlyFor(): array
    {
        if (empty($this->onlyFor)) {
            $this->onlyFor = $this->retrieveModelsFromAttribute(OnlyFor::class);
        }

        return $this->onlyFor;
    }

    public function excludeFrom(): array
    {
        if (empty($this->excludeFrom)) {
            $this->excludeFrom = $this->retrieveModelsFromAttribute(ExcludeFrom::class);
        }

        return $this->excludeFrom;
    }

    public function hasSettingAttributes(): bool
    {
        return $this->onlyFor() || $this->excludeFrom();
    }

    /**
     * Retrieve models based on the specified attribute.
     *
     * @return Model[]
     */
    private function retrieveModelsFromAttribute(string $attribute): array
    {
        $reflection = new ReflectionClass($this);

        return $this->extractModelsFromAttributes($reflection->getAttributes($attribute));
    }
}
