<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Meius\LaravelFilter\Traits\Filterable;

abstract class Controller extends BaseController
{
    use Filterable;
}
