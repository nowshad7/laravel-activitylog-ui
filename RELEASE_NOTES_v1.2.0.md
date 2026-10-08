# v1.2.0 — Analytics, timeline, saved views, multi-format export & JSON API

A major feature release. The clean, well-tested core is unchanged; everything new is **opt-in or degrades gracefully**, so upgrading is safe and changes nothing until you turn features on. Tested across **Laravel 9, 10, 11 and 12** on PHP 8.1–8.4.

```bash
composer require nsd7/laravel-activitylog-ui
```

## ✨ Highlights

- **📊 Analytics dashboard** — activity over time, breakdown by event and log name, and top causers/subjects, rendered with Chart.js. Filter-aware, cached per filter set, and portable across MySQL, PostgreSQL, SQLite and SQL Server.
- **🕒 Timeline view** — a day-grouped vertical timeline, toggled from a new tab bar.
- **💾 Saved views** — save the current filter set a name and recall it in one click, scoped per user.
- **⬇️ More export formats** — **JSON, Excel (XLSX) and PDF** join CSV. Excel falls back to CSV and PDF to JSON when the optional `maatwebsite/excel` / `barryvdh/laravel-dompdf` packages aren't installed. Still streamed, row-capped and formula-injection safe.
- **🗓️ Quick date presets** — Today, Yesterday, Last 7/30 days, This month.
- **🔐 Access allow-lists** — `access.allowed_users` and `access.allowed_roles`, layered on top of the existing gate.
- **🔌 Read-only JSON API** (opt-in) — same filters as the UI, for SPAs, dashboards and automated agents.
- **📈 Live counts** (opt-in) — background-refresh the statistics total.
- **🌍 Localization** — publishable language files and a translatable UI.
- **⚡ Performance-index migration** (publishable) — indexes on `event`, `created_at` and `(log_name, created_at)`.

## 🔧 Config added

`access.*`, `features.{analytics,timeline,saved_views,live_counts,api}`, `analytics.*`, `live_counts.*`, `saved_views.*`, and `export.formats`. All existing keys are unchanged; re-publish the config to pick up the new options:

```bash
php artisan vendor:publish --tag=activitylog-ui-config
php artisan vendor:publish --tag=activitylog-ui-migrations   # saved views + performance indexes
php artisan migrate
```

## 🧱 Internals

- Shared activity lookups extracted into a `ResolvesActivities` trait; export logic into an `ActivityExporter`.
- Optional-feature routes are always registered and gated inside their controllers, so feature flags take effect at runtime.

## ✅ Quality

- **86 tests, 260 assertions**, green on the full Laravel 9→12 / PHP 8.1→8.4 CI matrix.

## ⬆️ Upgrade notes

Drop-in from 1.1.x — no breaking changes. To use saved views and the performance indexes, publish and run the new migrations. To enable the API or live counts, flip the matching `features.*` flag.

**Full changelog:** see [CHANGELOG.md](CHANGELOG.md).
