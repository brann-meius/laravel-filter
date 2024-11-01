<?php

declare(strict_types=1);

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

    'path' => 'Filters',

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
    | Route Groups
    |--------------------------------------------------------------------------
    |
    | Here you may configure the route groups that the filters will be applied to.
    | The filters will be applied to all routes within the specified groups.
    |
    */
    'route_groups' => [
        'web',
        'api',
    ],
];
