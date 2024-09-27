<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services\Filter;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Meius\LaravelFilter\Tests\TestCase;

abstract class TestFilterManager extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('filter.path', __DIR__ . '/../../../Support/Filters');

        Request::instance()
            ->merge([
                'filter' => [
                    'users' => [
                        'id' => 1,
                        'last_name' => 'Doe',
                        'email' => 'ex',
                    ],
                    'posts' => [
                        'id' => 1,
                        'title' => 'It`s a story',
                        'content' => 'Once upon a time...',
                    ],
                    'comments' => [
                        'id' => 14,
                        'title' => 'ggvp',
                        'content' => 'Never give up!',
                    ],
                ],
            ]);
    }
}