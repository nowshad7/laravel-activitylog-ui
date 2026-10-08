# Features

## Activity list
Event badges, subject & causer links, relative timestamps, pagination, and an inline expandable change diff showing the old/new value of every changed attribute.

## Detail page
Per-activity view with the field diff, custom properties, raw JSON, batch link, and the full history of the same record.

## Filters
Free-text search, model, subject ID, event, log name, causer, batch and date range, plus **quick date presets** (Today, Yesterday, Last 7/30 days, This month). Active filters render as removable chips and persist across pages and tabs.

## Saved views
Save the current filter set a name and recall it in one click. Scoped per user. Requires the published migration.

## Timeline
A day-grouped vertical timeline of the filtered activities. Toggle via `features.timeline`.

## Statistics & live counts
Total / created / updated / deleted cards for the current filter. Enable `features.live_counts` to refresh the total in the background every `live_counts.poll_interval` seconds.

## Dark mode & responsive
Class-based dark mode with a persisted toggle, responsive layout, accessible markup. Tailwind + Alpine via CDN — no build step.
