<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Filters;

use Illuminate\Http\Request;

interface FilterInterface
{
    public function apply(Request $request): void;
}
