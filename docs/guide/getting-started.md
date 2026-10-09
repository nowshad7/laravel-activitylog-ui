# Introduction

**Laravel Activity Log UI** is a beautiful, drop-in [Tailwind CSS](https://tailwindcss.com)
dashboard for [Spatie Laravel Activitylog](https://spatie.be/docs/laravel-activitylog).
Browse, filter, diff, analyse and export the audit trail of your Laravel application —
with analytics charts, a timeline, saved views, dark mode and a read-only JSON API.

> Think *a polished admin UI for your `activity_log` table* — with **no build step, no Node,
> no npm**.

## Why this package

- **Zero build step.** Tailwind and Alpine.js are loaded at runtime — nothing to compile.
- **Drop-in.** `composer require`, visit a URL, done. It reads your existing `activity_log` table.
- **Broad Laravel support.** Laravel **9, 10, 11 and 12** on PHP **8.0+** — not just the latest release.
- **Multi-database.** MySQL, MariaDB, PostgreSQL, SQLite and SQL Server, all tested.
- **Safe by default.** Authorization gate + optional allow-lists, `noindex` headers, and exports hardened against formula injection.
- **Fully tested.** 80+ tests run against a Laravel 9→12 matrix on every push.

## Requirements

- **PHP** `^8.0`
- **Laravel** `^9.0 | ^10.0 | ^11.0 | ^12.0`
- **spatie/laravel-activitylog** `^4.0`, already logging activity
- *Optional:* `maatwebsite/excel` for Excel export, `barryvdh/laravel-dompdf` for PDF export

## Installation

```bash
composer require nsd7/laravel-activitylog-ui
```

The service provider is auto-discovered. That's it — visit **`/admin/activity-log`**
while logged in.

Make sure Spatie's `activity_log` table exists (see the
[Spatie installation guide](https://spatie.be/docs/laravel-activitylog/v4/installation)).

## Publish optional assets

```bash
# Config
php artisan vendor:publish --tag=activitylog-ui-config

# Views (to customise the UI)
php artisan vendor:publish --tag=activitylog-ui-views

# Language files
php artisan vendor:publish --tag=activitylog-ui-lang

# Migrations: saved views + performance indexes
php artisan vendor:publish --tag=activitylog-ui-migrations
php artisan migrate
```

By default the dashboard is protected by the `web` and `auth` middleware. Define a gate
(see [Authorization](/guide/authorization)) to restrict it further.

## Optional packages

| Package | Enables |
|---|---|
| `maatwebsite/excel` | Excel (XLSX) export (otherwise falls back to CSV) |
| `barryvdh/laravel-dompdf` | PDF export (otherwise falls back to JSON) |

## Next steps

- [Configuration](/guide/configuration) — the full `config/activitylog-ui.php` reference.
- [Dashboard](/guide/dashboard) — the list, detail view, filters and statistics.
- [Analytics](/guide/analytics) — charts computed from your filtered logs.
- [Exports](/guide/exports) — CSV / JSON / Excel / PDF.
- [Authorization](/guide/authorization) — gates and allow-lists.
- [JSON API](/guide/api) — consume the log from a SPA or agent.
