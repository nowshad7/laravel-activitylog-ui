# Changelog

## 1.2.0

### Added
- **Analytics dashboard** (`features.analytics`): activity over time, breakdown by event and log name, and top causers/subjects, rendered with Chart.js. Filter-aware and cached per filter set (`analytics.cache_ttl`); date grouping is portable across MySQL, PostgreSQL, SQLite and SQL Server.
- **Timeline view** (`features.timeline`): a day-grouped vertical timeline toggled from the tab bar.
- **Saved views** (`features.saved_views`): save the current filter set and recall it later, scoped per user. Ships a publishable migration and degrades gracefully until it is run.
- **Export formats**: JSON, Excel (XLSX) and PDF in addition to CSV. Excel falls back to CSV and PDF falls back to JSON when the optional `maatwebsite/excel` / `barryvdh/laravel-dompdf` packages are absent.
- **Quick date presets** (Today, Yesterday, Last 7/30 days, This month) above the filters.
- **Access allow-lists** (`access.allowed_users`, `access.allowed_roles`) layered on top of the gate.
- **Read-only JSON API** (`features.api`) over the same filters, for SPAs, dashboards and automated agents.
- **Live counts** (`features.live_counts`): background-refresh the statistics total.
- **Localization**: publishable language files (`activitylog-ui-lang`) and a translatable UI.
- **Performance index migration** (publishable): indexes on `event`, `created_at` and `(log_name, created_at)`.
- Expanded test suite (80+ tests) covering every new feature.

### Changed
- Routes for optional features are always registered and gated inside their controllers, so feature flags take effect at runtime.
- Shared activity lookups extracted into a `ResolvesActivities` trait; export logic extracted into an `ActivityExporter`.

## 1.1.x

### Fixed
- The causer was always shown as "System": `causer_type` was not selected, so the relation never loaded.
- Pagination links dropped the active filters.
- Search crashed (HTTP 500) on PostgreSQL, SQLite and MySQL tables without a FULLTEXT index. It now uses a portable `LIKE` search by default.
- The model filter matched by substring (`User` also matched `UserProfile`). It now matches the exact class, and still accepts a basename for old links.
- The "active filters" counter included the `page` parameter.
- `vendor:publish` published nothing, although the README documented it. Config and views are now publishable.
- `null` subject types and events no longer produce empty dropdown options.

### Added
- Config file (`activitylog-ui.php`): route prefix/domain/middleware, enable toggle, pagination, search driver, causer display attributes, date format, statistics and export options.
- Authorization through the `viewActivityLogUi` gate.
- Activity detail page with field diff, custom properties, raw JSON, batch link and record history.
- Inline, expandable change diff in the list.
- Filters for log name, causer, batch and date range; removable filter chips; per-page selector.
- Event statistics cards for the current filter (promised in the README but missing).
- Streaming CSV export with a row cap and protection against formula injection.
- Dark mode and improved mobile layout.
- Support for Laravel 12 and custom activity models (`activitylog.activity_model`).
- PHPUnit test suite (Orchestra Testbench) and a GitHub Actions matrix for Laravel 9–12.

### Changed
- Views moved to `resources/views` and split into a layout, pages and partials.
- Removed the Lumen base-controller shim and the highlight.js dependency; Alpine.js is pinned to a fixed version.
