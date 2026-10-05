# Laravel Activity Log UI

**Laravel Activity Log UI** is a **Tailwind CSS-powered user interface** for the popular [Spatie Laravel Activitylog](https://spatie.be/docs/laravel-activitylog/v4/introduction) package. It gives you a drop-in dashboard to browse, filter, inspect and export the activity logs of your Laravel application.

[![Latest Version](https://img.shields.io/packagist/v/nsd7/laravel-activitylog-ui.svg)](https://packagist.org/packages/nsd7/laravel-activitylog-ui)
[![Tests](https://github.com/nowshad7/laravel-activitylog-ui/actions/workflows/tests.yml/badge.svg)](https://github.com/nowshad7/laravel-activitylog-ui/actions/workflows/tests.yml)
[![License](https://img.shields.io/github/license/nowshad7/laravel-activitylog-ui.svg)](LICENSE)
[![Downloads](https://img.shields.io/packagist/dt/nsd7/laravel-activitylog-ui.svg)](https://packagist.org/packages/nsd7/laravel-activitylog-ui)
[![Author](https://img.shields.io/badge/author-@RHNowshad-blue.svg)](https://www.linkedin.com/in/rh-nowshad)

---

## Features

- **Activity list** with event badges, subject and causer links, relative timestamps and pagination.
- **Inline change diff**: expand any row to see the old and new value of every changed attribute.
- **Detail page** for each activity, with its changes, custom properties, raw JSON, batch and the full **history of the same record**.
- **Filters**: free-text search, model, subject ID, event, log name, causer, batch and **date range**. Active filters show as removable chips and are kept across pages.
- **Statistics** (total / created / updated / deleted) for the current filter.
- **CSV export** of the filtered result. Exports are streamed, capped and protected against spreadsheet formula injection.
- **Authorization gate**, configurable route prefix, domain and middleware.
- **Dark mode**, responsive layout, works with any database (MySQL, PostgreSQL, SQLite, SQL Server).

---

## Activity Log View

![Activity Log View](screenshots/ui.png)

---

## Requirements

- **PHP:** ^8.0
- **Laravel:** ^9.0, ^10.0, ^11.0 or ^12.0
- **spatie/laravel-activitylog:** ^4.0

---

## Installation

1. **Install the package**

   ```bash
   composer require nsd7/laravel-activitylog-ui
   ```

2. **Set up spatie/laravel-activitylog**

   Make sure the `activity_log` table exists. See the [Spatie installation guide](https://spatie.be/docs/laravel-activitylog/v4/installation).

3. **Visit the dashboard**

   ```plaintext
   http://your-app.test/admin/activity-log
   ```

---

## Authorization

By default the dashboard is protected by the `web` and `auth` middleware, so **any logged-in user can access it**. Restrict it by defining the `viewActivityLogUi` gate, for example in `AppServiceProvider::boot()`:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewActivityLogUi', function ($user) {
    return $user->is_admin;
});
```

Once the gate is defined, users who fail it receive a `403`. You can use a different gate name with the `gate` config option.

---

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=activitylog-ui-config
```

The main options in `config/activitylog-ui.php`:

| Option | Default | Description |
| --- | --- | --- |
| `enabled` | `true` | Register the dashboard routes (`ACTIVITYLOG_UI_ENABLED`). |
| `route.prefix` | `admin/activity-log` | URL prefix (`ACTIVITYLOG_UI_PATH`). |
| `route.domain` | `null` | Optional domain (`ACTIVITYLOG_UI_DOMAIN`). |
| `route.middleware` | `['web', 'auth']` | Middleware applied to all routes. |
| `gate` | `viewActivityLogUi` | Gate checked when it is defined. |
| `per_page` / `per_page_options` | `15` / `[10, 15, 25, 50, 100]` | Pagination. |
| `search_driver` | `like` | `like` (any database) or `fulltext` (needs a FULLTEXT index on `description`). |
| `causer_display_attributes` | `['name', 'full_name', 'username', 'email']` | First non-empty attribute is shown as the causer name. |
| `date_format` | `Y-m-d H:i:s` | Format of absolute timestamps. |
| `show_stats` | `true` | Show the statistics cards. |
| `export.enabled` / `export.limit` | `true` / `10000` | CSV export toggle and row cap. |

A custom activity model configured through `activitylog.activity_model` is respected.

### Customizing the views

```bash
php artisan vendor:publish --tag=activitylog-ui-views
```

The views are copied to `resources/views/vendor/activitylog-ui/`.

### Tailwind CSS and Alpine.js

The views ship as a standalone page and load Tailwind CSS (Play CDN) and Alpine.js from a CDN. No build step is needed in your application. If you have a strict Content Security Policy, publish the views and swap the CDN scripts for your own compiled assets.

---

## Routes

| Name | URL | Description |
| --- | --- | --- |
| `activitylog-ui.index` | `GET /admin/activity-log` | List with filters |
| `activitylog-ui.show` | `GET /admin/activity-log/{id}` | Activity detail |
| `activitylog-ui.export` | `GET /admin/activity-log/export` | CSV export (accepts the same filters) |

Supported query parameters: `search`, `model`, `subject_id`, `event`, `log_name`, `causer_type`, `causer_id`, `batch_uuid`, `date_from`, `date_to` (`Y-m-d`), `per_page`.

---

## Testing

```bash
composer install
composer test
```

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
