<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Meius\LaravelFilter\Traits\Filterable;

class Controller extends BaseController
{
    use AuthorizesRequests, Filterable, ValidatesRequests;
}
