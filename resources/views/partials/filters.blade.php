@php
    $input = 'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:[color-scheme:dark]';
    $label = 'mb-1 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400';
    $filterLabels = [
        'search' => 'Search', 'log_name' => 'Log', 'model' => 'Model', 'subject_id' => 'Subject ID',
        'event' => 'Event', 'causer_type' => 'Causer type', 'causer_id' => 'Causer ID',
        'date_from' => 'From', 'date_to' => 'To', 'batch_uuid' => 'Batch',
    ];
@endphp

<div x-data="{ open: {{ $filters->count() ? 'true' : 'false' }} || window.innerWidth >= 1024 }"
     class="mb-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3 dark:border-slate-800">
        <div class="flex flex-wrap items-center gap-2">
            <h2 class="text-sm font-semibold">Filters</h2>
            <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300" data-active-filters="{{ $filters->count() }}">
                {{ $filters->count() }} active
            </span>

            @foreach ($filters->all() as $key => $value)
                <a href="{{ route('activitylog-ui.index', array_merge(\Illuminate\Support\Arr::except($filters->all(), [$key]), ['per_page' => $perPage])) }}"
                   class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-xs text-slate-700 hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-rose-500/50 dark:hover:bg-rose-500/10"
                   title="Remove filter">
                    <span class="font-medium">{{ $filterLabels[$key] ?? $key }}:</span>
                    <span class="max-w-[12rem] truncate">{{ $key === 'model' || $key === 'causer_type' ? class_basename($value) : $value }}</span>
                    <span aria-hidden="true">&times;</span>
                </a>
            @endforeach
        </div>

        <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:hover:bg-slate-800 dark:hover:text-white"
                aria-label="Toggle filters">
            <svg class="h-5 w-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    <form x-show="open" x-cloak method="GET" action="{{ route('activitylog-ui.index') }}" class="p-4">
        @foreach (['causer_type', 'batch_uuid'] as $hidden)
            @if ($filters->get($hidden))
                <input type="hidden" name="{{ $hidden }}" value="{{ $filters->get($hidden) }}">
            @endif
        @endforeach

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <label for="alu-search" class="{{ $label }}">Search</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search" name="search" id="alu-search" value="{{ $filters->get('search') }}"
                           placeholder="Search description…" class="{{ $input }} pl-9">
                </div>
            </div>

            <div>
                <label for="alu-model" class="{{ $label }}">Model</label>
                <select name="model" id="alu-model" class="{{ $input }}">
                    <option value="">All models</option>
                    @foreach ($models as $value => $name)
                        <option value="{{ $value }}" @selected($filters->get('model') === $value || $filters->get('model') === $name)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="alu-subject" class="{{ $label }}">Subject ID</label>
                <input type="text" name="subject_id" id="alu-subject" value="{{ $filters->get('subject_id') }}" placeholder="e.g. 42" class="{{ $input }}">
            </div>

            <div>
                <label for="alu-event" class="{{ $label }}">Event</label>
                <select name="event" id="alu-event" class="{{ $input }}">
                    <option value="">All events</option>
                    @foreach ($events as $event)
                        <option value="{{ $event }}" @selected($filters->get('event') === $event)>{{ ucfirst($event) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="alu-log" class="{{ $label }}">Log name</label>
                <select name="log_name" id="alu-log" class="{{ $input }}">
                    <option value="">All logs</option>
                    @foreach ($logNames as $logName)
                        <option value="{{ $logName }}" @selected($filters->get('log_name') === $logName)>{{ $logName }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="alu-causer" class="{{ $label }}">Causer ID</label>
                <input type="text" name="causer_id" id="alu-causer" value="{{ $filters->get('causer_id') }}" placeholder="User ID" class="{{ $input }}">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label for="alu-from" class="{{ $label }}">From</label>
                    <input type="date" name="date_from" id="alu-from" value="{{ $filters->get('date_from') }}" class="{{ $input }}">
                </div>
                <div>
                    <label for="alu-to" class="{{ $label }}">To</label>
                    <input type="date" name="date_to" id="alu-to" value="{{ $filters->get('date_to') }}" class="{{ $input }}">
                </div>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4 dark:border-slate-800">
            <div class="flex items-center gap-2 text-sm">
                <label for="alu-per-page" class="whitespace-nowrap text-slate-500 dark:text-slate-400">Per page</label>
                <select name="per_page" id="alu-per-page" class="{{ $input }} w-auto py-1.5" onchange="this.form.submit()">
                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                @if ($filters->count())
                    <a href="{{ route('activitylog-ui.index') }}"
                       class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        Reset
                    </a>
                @endif
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Apply filters
                </button>
            </div>
        </div>
    </form>
</div>
