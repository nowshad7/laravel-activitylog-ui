<?php

return [
    'title' => 'Activity Log',
    'subtitle' => 'Audit trail of everything that happened in your application.',

    'nav' => [
        'list' => 'List',
        'timeline' => 'Timeline',
        'analytics' => 'Analytics',
    ],

    'actions' => [
        'export' => 'Export',
        'apply_filters' => 'Apply filters',
        'reset' => 'Reset',
        'details' => 'Details',
        'save_view' => 'Save view',
        'delete' => 'Delete',
    ],

    'filters' => [
        'heading' => 'Filters',
        'active' => ':count active',
        'search' => 'Search',
        'search_placeholder' => 'Search description…',
        'model' => 'Model',
        'all_models' => 'All models',
        'subject_id' => 'Subject ID',
        'event' => 'Event',
        'all_events' => 'All events',
        'log_name' => 'Log name',
        'all_logs' => 'All logs',
        'causer_id' => 'Causer ID',
        'from' => 'From',
        'to' => 'To',
        'per_page' => 'Per page',
        'presets' => 'Quick ranges',
    ],

    'stats' => [
        'total' => 'Total',
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
    ],

    'table' => [
        'event' => 'Event',
        'description' => 'Description',
        'subject' => 'Subject',
        'causer' => 'Causer',
        'changes' => 'Changes',
        'when' => 'When',
        'system' => 'System',
        'empty' => 'No activities found',
        'empty_hint' => 'Try adjusting or resetting your filters.',
        'properties' => 'Properties',
    ],

    'saved_views' => [
        'heading' => 'Saved views',
        'none' => 'No saved views yet.',
        'name_placeholder' => 'Name this view…',
        'save_current' => 'Save current filters',
        'confirm_delete' => 'Delete this saved view?',
    ],

    'analytics' => [
        'heading' => 'Analytics',
        'subtitle' => 'Trends and breakdowns for the current filters.',
        'over_time' => 'Activity over time',
        'by_event' => 'By event',
        'by_log' => 'By log name',
        'top_causers' => 'Top causers',
        'top_subjects' => 'Top subjects',
        'no_data' => 'No data for the current filters.',
    ],

    'view_saved' => 'View saved.',
    'view_deleted' => 'View deleted.',
];
