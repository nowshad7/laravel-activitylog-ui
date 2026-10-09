---
layout: home
hero:
  name: Laravel Activity Log UI
  text: A beautiful UI for your audit trail
  tagline: A drop-in Tailwind CSS dashboard for Spatie Laravel Activitylog — browse, filter, diff, analyse and export your activity log, with analytics charts, a timeline, saved views, dark mode and a JSON API. No build step, no Node, no npm.
  image:
    src: /logo.svg
    alt: Activity Log UI
  actions:
    - theme: brand
      text: Get started
      link: /guide/getting-started
    - theme: alt
      text: GitHub
      link: https://github.com/nowshad7/laravel-activitylog-ui
features:
  - icon: 📋
    title: Browse & diff
    details: Activity list with event badges, subject & causer links and relative timestamps. Expand any row for an inline old/new diff, or open the detail page for custom properties, raw JSON and the full history of the same record.
  - icon: 🔍
    title: Rich filters
    details: Free-text search, model, subject ID, event, log name, causer, batch and date range, with quick presets (Today, Last 7/30 days, This month). Active filters show as removable chips and persist across pages.
  - icon: 📊
    title: Analytics dashboard
    details: Activity over time, breakdowns by event and log name, and top causers & subjects — rendered with Chart.js, honouring your current filters and cached per filter set.
  - icon: 🕒
    title: Timeline & saved views
    details: A day-grouped vertical timeline as an alternative to the table, plus saved views — store a filter set and recall it in one click, scoped per user.
  - icon: ⬇️
    title: Export anywhere
    details: Export the currently filtered result as CSV, JSON, Excel (XLSX) or PDF. Streamed, row-capped, and hardened against spreadsheet formula injection.
  - icon: 🔐
    title: Safe & self-hosted
    details: Authorization gate, configurable prefix/domain/middleware and optional user/role allow-lists. Reads your existing activity_log table across MySQL, PostgreSQL, SQLite and SQL Server. Opt-in read-only JSON API.
---
