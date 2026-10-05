@php($present = \Nsd7\LaravelActivitylogUi\Support\ActivityPresenter::class)
@php($dateFormat = config('activitylog-ui.date_format', 'Y-m-d H:i:s'))

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="relative overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
            <thead class="bg-slate-50 dark:bg-slate-900/60">
                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    <th scope="col" class="px-4 py-3">Event</th>
                    <th scope="col" class="px-4 py-3">Description</th>
                    <th scope="col" class="px-4 py-3">Subject</th>
                    <th scope="col" class="px-4 py-3">Causer</th>
                    <th scope="col" class="px-4 py-3">Changes</th>
                    <th scope="col" class="px-4 py-3">When</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>

            @forelse ($logs as $log)
                @php($changes = $present::changes($log))
                <tbody x-data="{ open: false }" class="border-b border-slate-100 last:border-0 dark:border-slate-800">
                    <tr class="align-top transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $present::eventClasses($log->event) }}">
                                {{ $log->event ?? '—' }}
                            </span>
                            @if ($log->log_name)
                                <div class="mt-1 text-xs text-slate-400">{{ $log->log_name }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-900 dark:text-slate-100">
                            <div class="max-w-md break-words">{{ $log->description }}</div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            @if ($log->subject_type)
                                <a href="{{ route('activitylog-ui.index', ['model' => $log->subject_type, 'subject_id' => $log->subject_id]) }}"
                                   class="font-medium text-indigo-600 hover:underline dark:text-indigo-400"
                                   title="{{ $log->subject_type }} — show all activity for this record">
                                    {{ $present::subjectLabel($log) }} <span class="text-slate-400">#{{ $log->subject_id }}</span>
                                </a>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            @if ($log->causer_type && $log->causer_id !== null)
                                <a href="{{ route('activitylog-ui.index', ['causer_type' => $log->causer_type, 'causer_id' => $log->causer_id]) }}"
                                   class="text-slate-700 hover:text-indigo-600 hover:underline dark:text-slate-300 dark:hover:text-indigo-400"
                                   title="Show all activity by this causer">
                                    {{ $present::causerName($log) }}
                                </a>
                            @else
                                <span class="text-slate-400">System</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            @if (count($changes))
                                <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                                        class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                                    <svg class="h-3.5 w-3.5 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    {{ count($changes) }} {{ \Illuminate\Support\Str::plural('field', count($changes)) }}
                                </button>
                            @elseif ($log->properties && $log->properties->isNotEmpty())
                                <button type="button" @click="open = !open" class="rounded-md px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                                    Properties
                                </button>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-500 dark:text-slate-400">
                            @if ($log->created_at)
                                <time datetime="{{ $log->created_at->toIso8601String() }}" title="{{ $log->created_at->format($dateFormat) }}">
                                    {{ $log->created_at->diffForHumans() }}
                                </time>
                                <div class="text-xs text-slate-400">{{ $log->created_at->format($dateFormat) }}</div>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('activitylog-ui.show', $log->getKey()) }}"
                               class="rounded-md px-2 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/10">
                                Details
                            </a>
                        </td>
                    </tr>
                    <tr x-show="open" x-cloak>
                        <td colspan="7" class="bg-slate-50 px-4 py-3 dark:bg-slate-950/50">
                            @if (count($changes))
                                @include('activitylog-ui::partials.changes', ['changes' => $changes])
                            @else
                                <pre class="max-h-72 overflow-auto rounded-lg border border-slate-200 bg-white p-3 text-xs dark:border-slate-800 dark:bg-slate-900"><code>{{ $present::prettyJson($log->properties) }}</code></pre>
                            @endif
                        </td>
                    </tr>
                </tbody>
            @empty
                <tbody>
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <svg class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-2 font-medium text-slate-700 dark:text-slate-300">No activities found</p>
                            @if ($filters->count())
                                <p class="mt-1 text-sm text-slate-500">Try adjusting or <a href="{{ route('activitylog-ui.index') }}" class="text-indigo-600 hover:underline dark:text-indigo-400">resetting your filters</a>.</p>
                            @endif
                        </td>
                    </tr>
                </tbody>
            @endforelse
        </table>
    </div>

    @include('activitylog-ui::partials.pagination', ['paginator' => $logs])
</div>
