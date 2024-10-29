<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Attributes\Settings;

use Meius\LaravelFilter\Attributes\Setting;

/**
 * Attribute to apply filters only for specified models.
 *
 * Used to annotate classes that should have filters applied exclusively to them.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class OnlyFor extends Setting
{
    //
}
