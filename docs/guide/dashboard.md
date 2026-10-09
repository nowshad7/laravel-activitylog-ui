# Dashboard

The dashboard is the heart of the package — a filterable, paginated view of your
`activity_log` table at **`/admin/activity-log`** (or your configured prefix).

## Activity list

Each row shows the **event** as a coloured badge, links to the **subject** and **causer**
models, a relative timestamp and the log name. Expand any row for an **inline change diff**
showing the old and new value of every changed attribute.

## Detail page

Open an activity for the full picture:

- the field-by-field **change diff**,
- **custom properties**,
- the **raw JSON** payload,
- a link to the **batch** (when the activity was logged as part of one),
- and the **full history of the same record** — every activity for that subject.

## Filters

Narrow the list with:

- free-text **search**,
- **model** (subject type) and **subject ID**,
- **event**, **log name**, **causer** and **batch**,
- a **date range** with quick presets — Today, Yesterday, Last 7 days, Last 30 days, This month.

Active filters render as **removable chips** and persist across pages and tabs. Set the
search strategy with `search_driver` — `like` (portable, the default) or `fulltext`
(MySQL/MariaDB).

## Statistics & live counts

Statistics cards (**total / created / updated / deleted**) summarise the current filter.
Toggle them with `show_stats`. Enable `features.live_counts` to refresh the total in the
background every `live_counts.poll_interval` seconds.

## Dark mode & responsive

Class-based dark mode with a persisted toggle, a responsive layout and accessible markup.
Tailwind and Alpine.js are loaded via CDN, so there is **no build step**.
