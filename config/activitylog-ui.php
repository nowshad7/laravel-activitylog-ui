<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable the UI
    |--------------------------------------------------------------------------
    |
    | When disabled, no routes are registered and the dashboard is unreachable.
    |
    */

    'enabled' => env('ACTIVITYLOG_UI_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    */

    'route' => [
        'prefix' => env('ACTIVITYLOG_UI_PATH', 'admin/activity-log'),
        'domain' => env('ACTIVITYLOG_UI_DOMAIN'),
        'middleware' => ['web', 'auth'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization gate
    |--------------------------------------------------------------------------
    |
    | If a gate with this name is defined (e.g. in your AuthServiceProvider),
    | only users passing it can access the dashboard. If no gate is defined,
    | every user that passes the route middleware is allowed in.
    |
    |   Gate::define('viewActivityLogUi', fn ($user) => $user->is_admin);
    |
    */

    'gate' => 'viewActivityLogUi',

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    'per_page' => 15,

    'per_page_options' => [10, 15, 25, 50, 100],

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    |
    | "like"     - portable LIKE search, works on every database (default).
    | "fulltext" - MySQL/MariaDB MATCH ... AGAINST; requires a FULLTEXT index
    |              on the activity_log.description column.
    |
    */

    'search_driver' => 'like',

    /*
    |--------------------------------------------------------------------------
    | Causer display
    |--------------------------------------------------------------------------
    |
    | The first non-empty attribute found on the causer model is displayed.
    |
    */

    'causer_display_attributes' => ['name', 'full_name', 'username', 'email'],

    /*
    |--------------------------------------------------------------------------
    | Presentation
    |--------------------------------------------------------------------------
    */

    'date_format' => 'Y-m-d H:i:s',

    'show_stats' => true,

    /*
    |--------------------------------------------------------------------------
    | CSV export
    |--------------------------------------------------------------------------
    |
    | Allows exporting the currently filtered logs as CSV. "export_limit"
    | caps the number of rows written to protect against huge exports.
    |
    */

    'export' => [
        'enabled' => true,
        'limit' => 10000,
    ],

];
