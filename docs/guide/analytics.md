# Analytics

The **Analytics** tab renders charts computed from the **currently filtered** logs, with
Chart.js:

- **Activity over time** — a line chart over the filter's date range (or the last
  `analytics.range_days` days), with gaps filled so the x-axis is continuous.
- **By event** — a doughnut of created / updated / deleted / restored / other.
- **By log name** — the busiest log channels.
- **Top causers** — the users (or other causers) with the most activity, with display
  names resolved via `causer_display_attributes`.
- **Top subjects** — the most-touched model types.

## Caching

The computed payload is cached per filter set for `analytics.cache_ttl` seconds
(default `3600`; set `0` to disable).

## Portability

Daily grouping uses a per-driver date expression, so it works on MySQL/MariaDB,
PostgreSQL, SQLite and SQL Server.

## Data endpoint

The same payload is available as JSON at `{prefix}/analytics/data` (same authorization as
the UI), handy for embedding the charts elsewhere.

Disable the whole feature with `features.analytics => false`.
