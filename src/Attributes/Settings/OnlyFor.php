<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Attributes\Settings;

use Meius\LaravelFilter\Attributes\Setting;

#[\Attribute(\Attribute::TARGET_CLASS)]
class OnlyFor extends Setting {}
