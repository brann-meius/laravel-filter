<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Attributes\Settings;

use Meius\LaravelFilter\Attributes\Setting;

/**
 * Attribute to exclude specific classes from filter application.
 *
 * Used to annotate classes that should be excluded from filtering.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ExcludeFrom extends Setting {}
