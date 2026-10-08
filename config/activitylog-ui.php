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
    | Access control (allow-lists)
    |--------------------------------------------------------------------------
    |
    | Optional allow-lists layered on top of the gate, for teams that do not
    | want to write a gate. When a list is non-empty it is enforced; when it is
    | empty it is ignored. "allowed_users" matches the authenticated user's id
    | or email. "allowed_roles" matches a role name via a hasRole()/getRoleNames()
    | method or a "roles" relation (e.g. spatie/laravel-permission).
    |
    */

    'access' => [
        'allowed_users' => [],
        'allowed_roles' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature toggles
    |--------------------------------------------------------------------------
    */

    'features' => [
        'analytics' => true,
        'timeline' => true,
        'saved_views' => true,
        'live_counts' => false,
        'api' => false,
    ],

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
    | Analytics dashboard
    |--------------------------------------------------------------------------
    |
    | "cache_ttl" is how long (in seconds) the computed analytics payload is
    | cached for a given filter set. Set to 0 to disable caching.
    | "range_days" is the default window of the "activity over time" chart.
    |
    */

    'analytics' => [
        'cache_ttl' => 3600,
        'range_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Live counts
    |--------------------------------------------------------------------------
    |
    | When features.live_counts is enabled, the statistics total refreshes in
    | the background every "poll_interval" seconds without a full page reload.
    |
    */

    'live_counts' => [
        'poll_interval' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Saved views
    |--------------------------------------------------------------------------
    |
    | Lets a user save the current filter set and recall it later. Requires the
    | published migration to be run. Views are scoped to the authenticated user.
    |
    */

    'saved_views' => [
        'table' => 'activitylog_ui_saved_views',
        'max_per_user' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    |
    | "formats" lists the export formats offered in the UI. CSV and JSON work
    | out of the box. XLSX needs maatwebsite/excel and falls back to CSV when
    | absent; PDF needs barryvdh/laravel-dompdf and falls back to JSON.
    | "limit" caps the number of rows written to protect against huge exports.
    |
    */

    'export' => [
        'enabled' => true,
        'limit' => 10000,
        'formats' => ['csv', 'json', 'xlsx', 'pdf'],
    ],

];
