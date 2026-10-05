@extends('activitylog-ui::layouts.app')

@section('title', 'Activity Log')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Activity Log</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Audit trail of everything that happened in your application.</p>
        </div>

        @if (config('activitylog-ui.export.enabled', true))
            <a href="{{ route('activitylog-ui.export', $filters->all()) }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </a>
        @endif
    </div>

    @if ($stats)
        @include('activitylog-ui::partials.stats')
    @endif

    @include('activitylog-ui::partials.filters')

    @include('activitylog-ui::partials.table')
@endsection
