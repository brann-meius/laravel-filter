<?php

declare(strict_types=1);

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
    | Filter URL Prefix
    |--------------------------------------------------------------------------
    |
    | This value defines the prefix used for filtering parameters in URL queries.
    | By default, 'filter' is used as the root key to group all filter parameters.
    | For example, setting 'prefix' => 'filter' would format the filter query as:
    | ?filter[model][key]=value. You can customize this to any string that suits
    | your application needs.
    |
    */

    'prefix' => 'filter',


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
        'path' => base_path('bootstrap/cache/filters.php'),
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
