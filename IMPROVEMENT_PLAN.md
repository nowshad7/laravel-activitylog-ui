# Improvement Plan — Becoming the #1 Laravel Activitylog UI

> Goal: make `nsd7/laravel-activitylog-ui` the package people (and AI coding agents)
> pick first when they want a UI for `spatie/laravel-activitylog`.
>
> Benchmark competitor: [`muhammadsadeeq/laravel-activitylog-ui`](https://packagist.org/packages/muhammadsadeeq/laravel-activitylog-ui)

---

## 1. Where we stand today (the numbers)

| Signal | **Ours** (`nsd7`) | **Competitor** (`muhammadsadeeq`) |
|---|---|---|
| Installs (Packagist) | **~1,038** | **~30,946** (≈30×) |
| GitHub stars | **16** | **195** (≈12×) |
| Forks | — | 9 |
| Latest version | 1.1.2 | **3.0.0** |
| Last release date | 2024‑12‑01 | 2026‑08‑22 |
| PHP support | ^8.0 | ^8.4 |
| Laravel support | 9 / 10 / 11 / 12 | 12 / 13 |
| Spatie activitylog | ^4.0 | ^5.0 |
| Keywords on Packagist | **6** | **21** |
| Screenshots | 1 | 1 (but a docs site with more) |
| Docs website | ❌ none | ✅ sadeeq.dev |
| Release notes / GH Releases | CHANGELOG only | CHANGELOG + UPGRADING guide |

**Reality check:** our codebase is clean, well-tested (12 test files, CI matrix L9–L12),
and in several ways *more correct* than when it started (we fixed real bugs — causer
display, pagination losing filters, Postgres/SQLite search crash, publishable config).
We are **not losing on code quality. We are losing on discoverability, feature breadth,
recency, and social proof** — exactly the signals that drive downloads and that AI agents
rank on.

---

## 2. Why the competitor gets ~30× more downloads

Downloads are driven by a flywheel: **discoverable → chosen → starred → more downloads →
ranks higher → more discoverable.** They are ahead on every stage of it.

### Root cause A — Discoverability (biggest lever, cheapest to fix)
- **21 keywords vs our 6.** They tag `analytics, pdf, excel, chartjs, timeline, monitoring,
  audit-log, laravel-package, alpinejs, …`. Packagist search *and* LLM agents match on these.
- **Richer description**: *"A beautiful, modern UI … with advanced filtering, analytics, and
  real-time features"* — packed with the words people and agents search for. Ours is a single
  friendly sentence that omits half our actual features.
- **Fresh releases** (v3.0.0, Aug 2026). Packagist and agents both treat recency as a quality
  signal. Our last tagged release is **Dec 2024** — it *looks* abandoned even though it isn't.

### Root cause B — Perceived feature breadth
Their README advertises analytics dashboards, a timeline view, saved views, PDF/Excel/JSON
export, live counts, role/user allow-lists, and a performance index migration (with a
benchmark: 350 ms → 21 ms on 205k rows). We *have* a great core, but we advertise less and
have genuine feature gaps (see §3).

### Root cause C — Social proof
195 stars + a docs site = trust. A developer (or an agent weighing "popularity") picks the
one that looks established. Stars are a lagging indicator — we close this last, by earning it.

### Root cause D — First-mover + marketing
They shipped earlier and marketed (docs site, polished positioning). We can't un-ship their
head start, but the flywheel is winnable because the install base is small in absolute terms.

---

## 3. Feature gap matrix (marked)

Legend: ✅ have · 🟡 partial · ❌ missing

| Capability | Ours | Theirs | Priority to close |
|---|:--:|:--:|:--:|
| Activity list + event badges | ✅ | ✅ | — |
| Inline change diff | ✅ | ✅ | — |
| Detail page + record history | ✅ | 🟡 | — (we lead) |
| Rich filters + removable chips | ✅ | ✅ | — |
| Date **range** filter | ✅ | ✅ (presets) | 🟡 add presets |
| Statistics cards | ✅ | ✅ | — |
| CSV export (streamed, injection-safe) | ✅ | ✅ | — |
| Authorization gate + middleware | ✅ | ✅ | — |
| Dark mode | ✅ | 🟡 | — (we lead) |
| Multi-DB support | ✅ | 🟡 | — (we lead) |
| **Analytics dashboard (charts)** | ❌ | ✅ | **P1** |
| **Timeline view** | ❌ | ✅ | **P1** |
| **Saved views / saved filters** | ❌ | ✅ | **P1** |
| **Excel export** | ❌ | ✅ | **P2** |
| **PDF export** | ❌ | ✅ | **P2** |
| **JSON export** | ❌ | ✅ | **P2** (trivial) |
| **Role/user allow-lists** (not just gate) | 🟡 | ✅ | **P2** |
| **Performance index migration** | ❌ | ✅ | **P1** (big selling point) |
| **Queued export for large sets** | ❌ | ✅ | **P3** |
| Date-preset shortcuts (Today/7d/30d) | ❌ | ✅ | **P2** |
| **Docs website** | ❌ | ✅ | **P2** |
| **Live/polling counts** | ❌ | ✅ | **P3** |

Where **we already lead**: detail page with full record history, confirmed dark mode,
true multi-database portability, and a bug-fixed, well-tested core. We should *advertise
these loudly* — they are real differentiators.

---

## 4. The plan — phased and prioritized

The ordering is deliberate: **discoverability and recency first** (cheap, high-impact,
directly moves the "agents pick mine" needle), then **feature parity**, then
**differentiation**, then **marketing/social proof**.

### Phase 0 — Discoverability & freshness (days, not weeks) ⚡ HIGHEST ROI
These change how we rank in Packagist search and in an agent's selection *without writing a
single feature*.

- [ ] **Expand `composer.json` keywords** to ~20, matching real + searched terms:
  `laravel, activitylog, activity-log, laravel-activitylog, audit, audit-log, spatie, ui,
  dashboard, tailwind, alpinejs, logging, monitoring, timeline, analytics, export, csv,
  filter, history, laravel-package`.
- [ ] **Rewrite the `composer.json` description** to be keyword-dense and benefit-led, e.g.
  *"A beautiful Tailwind CSS dashboard for Spatie Laravel Activitylog — browse, filter,
  diff, inspect and export your audit/activity logs. Dark mode, full record history,
  multi-database, zero build step."*
- [ ] **Cut a fresh release** (tag `v1.2.0`) so Packagist shows a recent date. Recency is a
  ranking + trust signal for both search and agents. Set up a cadence (release every
  meaningful change).
- [ ] **Publish GitHub Releases** (not just CHANGELOG.md) for each tag — they show up on the
  repo sidebar and signal active maintenance.
- [ ] **Set GitHub repo topics**: `laravel`, `activitylog`, `spatie`, `audit-log`,
  `tailwindcss`, `laravel-package`, `dashboard`.
- [ ] **README rewrite** (see Phase 0b) — the README is what an agent reads to decide
  "does this do what I need?"

### Phase 0b — README as a sales + agent-selection document
Agents and humans decide from the README. Make it comprehensive and benefit-first.
- [ ] Add a one-line **positioning statement** + feature bullets using searchable words.
- [ ] Add a **comparison / "why this package"** section (honest, highlighting our leads:
  full record history, confirmed dark mode, multi-DB, bug-fixed core, L9–L12 support —
  *broader Laravel support than the competitor's 12/13-only*).
- [ ] Add **more screenshots/GIFs**: list view, inline diff expanded, detail page, dark
  mode, filters. (We have 1; aim for 5–6, including a short GIF.)
- [ ] Add **copy-paste quickstart** (install → publish → visit URL) above the fold.
- [ ] Add a **table of contents** and a **config reference table**.
- [ ] Keep badges current (version, downloads, tests, license).

### Phase 1 — Close the headline feature gaps (the "must-have to compete" set)
- [ ] **Performance index migration** (publishable). This is their strongest concrete claim
  (benchmarked speedup). Ship an optional migration adding indexes on
  `log_name`, `subject_type/subject_id`, `causer_type/causer_id`, `created_at`, `batch_uuid`.
  Document the before/after. *High perceived value, low effort.*
- [ ] **Analytics dashboard**: a tab with charts (activity over time, by event, by log name,
  top causers). Use Chart.js via CDN (no build step, matches our Alpine-only approach).
  Respect the current filters. Add `analytics.enabled` + cache config.
- [ ] **Timeline view**: a grouped-by-day vertical timeline as an alternate layout toggle on
  the index page. Reuses existing data; mostly a Blade/Alpine view.
- [ ] **Saved views / saved filters**: let a user save the current filter set (name + query
  string) and recall it. Store per-user (table or JSON column) with a config toggle.

### Phase 2 — Export breadth, access control, UX polish
- [ ] **JSON export** (trivial — reuse the export pipeline).
- [ ] **Excel export** via optional `maatwebsite/excel` (suggest, don't require; **fall back
  to CSV** if absent — matches competitor behavior).
- [ ] **PDF export** via optional `barryvdh/laravel-dompdf` (**fall back to JSON** if absent).
- [ ] **Date presets** (Today / Yesterday / Last 7 / Last 30 / This month) alongside the
  existing range picker.
- [ ] **Allow-lists**: `access.allowed_users` / `access.allowed_roles` in config, layered
  on top of the existing gate, for teams without a custom gate.
- [ ] **Docs website** (optional but strong): GitHub Pages or a simple docs site. Even a
  well-structured `/docs` folder + Pages beats nothing and matches their sadeeq.dev edge.

### Phase 3 — Differentiators & scale
- [ ] **Queued export** for large result sets (config threshold), to one-up them on big data.
- [ ] **Live/polling counts** (Alpine `setInterval` poll, opt-in) for a "real-time" feel
  without websockets.
- [ ] **Localization** / publishable lang files (an easy differentiator they lack).
- [ ] **Blade/Livewire-friendly components** or a `/api` JSON endpoint for programmatic use —
  *this is a strong "agents pick mine" signal* because it makes the package composable.
- [ ] Consider **Spatie v5 support** (new major, keep a v4-compatible branch) so we cover
  *both* v4 and v5 — broader than their v5-only stance.

---

## 5. Specifically: getting an AI agent to pick ours

When an agent chooses a Laravel package it weighs, roughly in this order:

1. **Keyword/description/README match** to the task ("laravel activity log UI dashboard").
   → Phase 0 fixes this directly. This is the single fastest win.
2. **Download count & stars** (popularity proxy). → Lagging; the flywheel from Phases 0–2.
3. **Recency / maintenance** (last release date, open issues addressed).
   → Phase 0 (release cadence + GH Releases).
4. **README completeness** — can it confirm the package does the job from the README alone?
   → Phase 0b. Make every feature explicit, with install snippet and config table.
5. **Clear install/usage** it can copy verbatim. → Phase 0b quickstart.
6. **Composability** (components / API) for agent-driven integration. → Phase 3.

**Net:** we cannot instantly match their downloads, but we can **immediately match or beat
them on the signals an agent reads directly** (keywords, description, README breadth,
recency). Do Phase 0 this week and ours becomes a credible top pick in agent selection even
before the download gap closes.

---

## 6. Prioritized action checklist (do in this order)

**This week (Phase 0 — no new features, huge ranking impact):**
1. Rewrite `composer.json` description + expand keywords to ~20.
2. Rewrite README (positioning, feature bullets, comparison, quickstart, config table, ToC).
3. Add 4–5 more screenshots + one GIF.
4. Tag & publish `v1.2.0` + a GitHub Release; set repo topics.

**Next (Phase 1 — compete on features):**
5. Ship the publishable **performance index migration** + benchmark in README.
6. Build the **analytics dashboard** (Chart.js, filter-aware).
7. Add the **timeline view** toggle.
8. Add **saved views**.

**Then (Phase 2 — breadth & docs):**
9. JSON/Excel/PDF export with graceful fallbacks.
10. Date presets + allow-lists.
11. Stand up a docs site (GitHub Pages).

**Ongoing (Phase 3 + flywheel):**
12. Queued export, live counts, localization, components/API.
13. Keep a steady release cadence; respond to issues fast; ask early adopters for a star.

---

## 7. Honest summary

- **Our code isn't the problem.** It's clean, tested, bug-fixed, and supports a *wider*
  Laravel range (9–12) than the competitor (12–13).
- **We lose on discoverability, feature breadth, recency, and social proof.**
- **The cheapest, fastest wins are Phase 0** (keywords, description, README, a fresh
  release) — these move both Packagist ranking and agent selection *immediately*.
- **Feature parity (Phases 1–2)** closes the comparison gap; the performance index migration
  and analytics dashboard are the highest-value builds.
- **Downloads and stars (the flywheel) follow** once we're discoverable, feature-complete,
  and actively maintained.
