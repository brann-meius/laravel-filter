<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Meius\LaravelFilter\Attributes\ApplyFiltersTo;
use Meius\LaravelFilter\Tests\Support\Models\User;

class UserController extends Controller
{
    #[ApplyFiltersTo(User::class)]
    public function index(): JsonResponse
    {
        return response()->json();
    }

    #[ApplyFiltersTo]
    public function edit(): JsonResponse
    {
        return response()->json();
    }

    public function store(): JsonResponse
    {
        return response()->json();
    }
}
