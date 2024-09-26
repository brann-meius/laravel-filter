<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Attributes;

/**
 * Attribute to define filters for models.
 *
 * Used to annotate methods that define filters for models.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ApplyFiltersTo extends Setting {}
