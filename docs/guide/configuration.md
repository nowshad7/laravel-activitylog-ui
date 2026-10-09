# Configuration

Publish the config with `php artisan vendor:publish --tag=activitylog-ui-config`. It lands
at `config/activitylog-ui.php`.

## Keys

| Key | Default | Description |
|---|---|---|
| `enabled` | `true` | Master switch (env `ACTIVITYLOG_UI_ENABLED`). When off, no routes are registered. |
| `route.prefix` | `admin/activity-log` | URL prefix (env `ACTIVITYLOG_UI_PATH`). |
| `route.domain` | `null` | Optional domain (env `ACTIVITYLOG_UI_DOMAIN`). |
| `route.middleware` | `['web', 'auth']` | Middleware applied to all routes. |
| `gate` | `viewActivityLogUi` | Gate checked when defined. |
| `access.allowed_users` | `[]` | User ids/emails allow-list (enforced when non-empty). |
| `access.allowed_roles` | `[]` | Role names allow-list (enforced when non-empty). |
| `features.analytics` | `true` | Analytics dashboard + data endpoint. |
| `features.timeline` | `true` | Timeline view. |
| `features.saved_views` | `true` | Saved views (needs the migration). |
| `features.live_counts` | `false` | Background-refresh the statistics total. |
| `features.api` | `false` | Read-only JSON API. |
| `per_page` / `per_page_options` | `15` / `[10,15,25,50,100]` | Pagination. |
| `search_driver` | `like` | `like` (portable) or `fulltext` (MySQL/MariaDB). |
| `causer_display_attributes` | `['name','full_name','username','email']` | First non-empty wins. |
| `date_format` | `Y-m-d H:i:s` | Timestamp display format. |
| `show_stats` | `true` | Statistics cards. |
| `analytics.cache_ttl` | `3600` | Seconds to cache the analytics payload (`0` = off). |
| `analytics.range_days` | `30` | Default window of the time chart. |
| `live_counts.poll_interval` | `15` | Seconds between live-count refreshes. |
| `saved_views.max_per_user` | `50` | Cap per user. |
| `export.enabled` | `true` | Allow exports. |
| `export.limit` | `10000` | Max rows per export. |
| `export.formats` | `['csv','json','xlsx','pdf']` | Formats offered in the UI. |

## Custom activity model / connection

The UI resolves the activity model through Spatie, so a custom model or database connection
configured in `config/activitylog.php` (`activity_model`) is picked up automatically —
including a custom table name.
