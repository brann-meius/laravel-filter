<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

return [

    /*
    |--------------------------------------------------------------------------
    | Filters Path
    |--------------------------------------------------------------------------
    |
    | This option defines the path where the filter files are stored. You can
    | change this path to any location that suits your application's structure.
    |
    */
    'path' => App::path('Filters'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the cache settings for filters. The path specified
    | below will be used to store the cached filter files.
    |
    */

    'cache' => [
        'path' => App::bootstrapPath('cache/filters.php'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logger Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the logger settings for the filter and controller
    | managers. You can specify the log driver and the log path.
    |
    */

    'logger' => [
        'driver' => 'single',
        'path' => App::storagePath('logs/filter/'.Carbon::now()->format('Y-m-d').'.log'),
    ],
];
