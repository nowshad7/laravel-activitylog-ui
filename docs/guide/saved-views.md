# Saved views

Save the current filter set a name and recall it with one click later. Views are **scoped
to the authenticated user**, so everyone keeps their own.

## Enabling

Run the published migration to create the `activitylog_ui_saved_views` table:

```bash
php artisan vendor:publish --tag=activitylog-ui-migrations
php artisan migrate
```

Until the table exists the feature **degrades gracefully** and simply stays hidden — nothing
breaks.

## Limits

- Each user can store up to `saved_views.max_per_user` views (default `50`).
- Disable the feature entirely with `features.saved_views => false`.
