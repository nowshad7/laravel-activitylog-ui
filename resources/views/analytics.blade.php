@extends('activitylog-ui::layouts.app')

@section('title', __('activitylog-ui::messages.analytics.heading'))

@php($filterRoute = 'activitylog-ui.analytics')
@php($hasData = ($analytics['totals']['total'] ?? 0) > 0)

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight">{{ __('activitylog-ui::messages.analytics.heading') }}</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('activitylog-ui::messages.analytics.subtitle') }}</p>
    </div>

    @include('activitylog-ui::partials.nav')

    @include('activitylog-ui::partials.filters')

    @if (! $hasData)
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-16 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="font-medium text-slate-700 dark:text-slate-300">{{ __('activitylog-ui::messages.analytics.no_data') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-3 dark:border-slate-800 dark:bg-slate-900">
                <h2 class="mb-4 text-sm font-semibold">{{ __('activitylog-ui::messages.analytics.over_time') }}</h2>
                <div class="h-72"><canvas id="alu-chart-per-day"></canvas></div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="mb-4 text-sm font-semibold">{{ __('activitylog-ui::messages.analytics.by_event') }}</h2>
                <div class="h-64"><canvas id="alu-chart-per-event"></canvas></div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="mb-4 text-sm font-semibold">{{ __('activitylog-ui::messages.analytics.by_log') }}</h2>
                <div class="h-64"><canvas id="alu-chart-per-log"></canvas></div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="mb-4 text-sm font-semibold">{{ __('activitylog-ui::messages.analytics.top_causers') }}</h2>
                <div class="h-64"><canvas id="alu-chart-causers"></canvas></div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-3 dark:border-slate-800 dark:bg-slate-900">
                <h2 class="mb-4 text-sm font-semibold">{{ __('activitylog-ui::messages.analytics.top_subjects') }}</h2>
                <div class="h-64"><canvas id="alu-chart-subjects"></canvas></div>
            </div>
        </div>

        <script id="alu-analytics-data" type="application/json">@json($analytics)</script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                var data = JSON.parse(document.getElementById('alu-analytics-data').textContent);
                var dark = document.documentElement.classList.contains('dark');
                var grid = dark ? 'rgba(148,163,184,0.15)' : 'rgba(100,116,139,0.15)';
                var text = dark ? '#cbd5e1' : '#475569';

                // Accessible, colour-blind-friendly categorical palette.
                var palette = ['#6366f1', '#10b981', '#f59e0b', '#f43f5e', '#0ea5e9', '#a855f7', '#14b8a6', '#eab308'];
                var eventColors = { created: '#10b981', updated: '#f59e0b', deleted: '#f43f5e', restored: '#0ea5e9' };

                if (window.Chart) {
                    Chart.defaults.color = text;
                    Chart.defaults.font.family = 'ui-sans-serif, system-ui, sans-serif';
                }

                function labelsOf(obj) { return Object.keys(obj); }
                function valuesOf(obj) { return Object.keys(obj).map(function (k) { return obj[k]; }); }

                function make(id, config) {
                    var el = document.getElementById(id);
                    if (el && window.Chart) { new Chart(el, config); }
                }

                var perDay = data.per_day || {};
                make('alu-chart-per-day', {
                    type: 'line',
                    data: {
                        labels: labelsOf(perDay),
                        datasets: [{
                            label: 'Activities',
                            data: valuesOf(perDay),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99,102,241,0.15)',
                            fill: true, tension: 0.3, pointRadius: 2, borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { color: grid }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } },
                            y: { beginAtZero: true, grid: { color: grid }, ticks: { precision: 0 } },
                        },
                    },
                });

                var perEvent = data.per_event || {};
                make('alu-chart-per-event', {
                    type: 'doughnut',
                    data: {
                        labels: labelsOf(perEvent),
                        datasets: [{
                            data: valuesOf(perEvent),
                            backgroundColor: labelsOf(perEvent).map(function (k, i) { return eventColors[k] || palette[i % palette.length]; }),
                            borderWidth: 0,
                        }],
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
                });

                var perLog = data.per_log_name || {};
                make('alu-chart-per-log', {
                    type: 'bar',
                    data: {
                        labels: labelsOf(perLog),
                        datasets: [{ label: 'Activities', data: valuesOf(perLog), backgroundColor: '#6366f1', borderRadius: 4 }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: grid }, ticks: { precision: 0 } } },
                    },
                });

                function horizontalBar(id, rows) {
                    make(id, {
                        type: 'bar',
                        data: {
                            labels: rows.map(function (r) { return r.label; }),
                            datasets: [{ label: 'Activities', data: rows.map(function (r) { return r.count; }), backgroundColor: '#0ea5e9', borderRadius: 4 }],
                        },
                        options: {
                            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { x: { beginAtZero: true, grid: { color: grid }, ticks: { precision: 0 } }, y: { grid: { display: false } } },
                        },
                    });
                }

                horizontalBar('alu-chart-causers', data.top_causers || []);
                horizontalBar('alu-chart-subjects', data.top_subjects || []);
            })();
        </script>
    @endif
@endsection
