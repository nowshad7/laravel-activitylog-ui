# Installation

## Requirements

- PHP `^8.0`
- Laravel `^9.0 || ^10.0 || ^11.0 || ^12.0`
- `spatie/laravel-activitylog` `^4.0`, already logging activity

## Install

```bash
composer require nsd7/laravel-activitylog-ui
```

The service provider is auto-discovered. Visit `/admin/activity-log` while authenticated.

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

## Optional packages

| Package | Enables |
|---|---|
| `maatwebsite/excel` | Excel (XLSX) export (otherwise falls back to CSV) |
| `barryvdh/laravel-dompdf` | PDF export (otherwise falls back to JSON) |
