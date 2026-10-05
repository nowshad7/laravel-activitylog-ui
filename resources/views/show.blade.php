@extends('activitylog-ui::layouts.app')

@php($present = \Nsd7\LaravelActivitylogUi\Support\ActivityPresenter::class)
@php($dateFormat = config('activitylog-ui.date_format', 'Y-m-d H:i:s'))

@section('title', 'Activity #' . $activity->getKey())

@section('content')
    <div class="mb-6">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('activitylog-ui.index') }}"
           class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
            &larr; Back to activity log
        </a>
        <div class="mt-2 flex flex-wrap items-center gap-3">
            <h1 class="text-2xl font-bold tracking-tight">Activity #{{ $activity->getKey() }}</h1>
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $present::eventClasses($activity->event) }}">
                {{ $activity->event ?? 'no event' }}
            </span>
        </div>
        <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $activity->description }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="border-b border-slate-200 px-4 py-3 text-sm font-semibold dark:border-slate-800">Changes</h2>
                <div class="p-4">
                    @if (count($changes))
                        @include('activitylog-ui::partials.changes', ['changes' => $changes])
                    @else
                        <p class="text-sm text-slate-500">No attribute changes were recorded for this activity.</p>
                    @endif
                </div>
            </section>

            @if (count($customProperties))
                <section class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="border-b border-slate-200 px-4 py-3 text-sm font-semibold dark:border-slate-800">Custom properties</h2>
                    <pre class="max-h-96 overflow-auto p-4 text-xs"><code>{{ $present::prettyJson($customProperties) }}</code></pre>
                </section>
            @endif

            <section x-data="{ open: false }" class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <button type="button" @click="open = !open" class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-semibold">
                    Raw properties
                    <span class="text-xs font-normal text-slate-500" x-text="open ? 'Hide' : 'Show'">Show</span>
                </button>
                <pre x-show="open" x-cloak class="max-h-96 overflow-auto border-t border-slate-200 p-4 text-xs dark:border-slate-800"><code>{{ $present::prettyJson($activity->properties) }}</code></pre>
            </section>
        </div>

        <div class="space-y-6">
            <section class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="border-b border-slate-200 px-4 py-3 text-sm font-semibold dark:border-slate-800">Details</h2>
                <dl class="divide-y divide-slate-100 text-sm dark:divide-slate-800">
                    @foreach ([
                        'Log' => $activity->log_name,
                        'Subject' => $activity->subject_type ? $activity->subject_type . ' #' . $activity->subject_id : null,
                        'Causer' => $present::causerName($activity),
                        'Batch' => $activity->batch_uuid,
                        'Logged at' => optional($activity->created_at)->format($dateFormat),
                    ] as $term => $value)
                        <div class="grid grid-cols-3 gap-2 px-4 py-2.5">
                            <dt class="text-slate-500 dark:text-slate-400">{{ $term }}</dt>
                            <dd class="col-span-2 break-all">{{ $value ?? '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
                @if ($activity->batch_uuid)
                    <div class="border-t border-slate-200 px-4 py-2.5 text-sm dark:border-slate-800">
                        <a href="{{ route('activitylog-ui.index', ['batch_uuid' => $activity->batch_uuid]) }}" class="text-indigo-600 hover:underline dark:text-indigo-400">View all activity in this batch &rarr;</a>
                    </div>
                @endif
            </section>

            @if ($history->count() > 1)
                <section class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="border-b border-slate-200 px-4 py-3 text-sm font-semibold dark:border-slate-800">History of this record</h2>
                    <ol class="divide-y divide-slate-100 text-sm dark:divide-slate-800">
                        @foreach ($history as $item)
                            <li class="{{ $item->is($activity) ? 'bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                                <a href="{{ route('activitylog-ui.show', $item->getKey()) }}" class="flex items-start justify-between gap-3 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <span>
                                        <span class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-medium ring-1 ring-inset {{ $present::eventClasses($item->event) }}">{{ $item->event ?? '—' }}</span>
                                        <span class="ml-1 text-slate-600 dark:text-slate-300">{{ $present::causerName($item) }}</span>
                                    </span>
                                    <time class="shrink-0 text-xs text-slate-400" title="{{ optional($item->created_at)->format($dateFormat) }}">{{ optional($item->created_at)->diffForHumans() }}</time>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                    <div class="border-t border-slate-200 px-4 py-2.5 text-sm dark:border-slate-800">
                        <a href="{{ route('activitylog-ui.index', ['model' => $activity->subject_type, 'subject_id' => $activity->subject_id]) }}" class="text-indigo-600 hover:underline dark:text-indigo-400">View full history &rarr;</a>
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
