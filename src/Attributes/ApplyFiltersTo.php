<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ApplyFiltersTo extends Setting {}
