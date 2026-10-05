# Changelog

## Unreleased

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
