@php($present = \Nsd7\LaravelActivitylogUi\Support\ActivityPresenter::class)
@php($dateFormat = config('activitylog-ui.date_format', 'Y-m-d H:i:s'))
@php($groups = $logs->getCollection()->groupBy(fn ($log) => optional($log->created_at)->format('Y-m-d') ?? '—'))

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    @forelse ($groups as $day => $items)
        <div class="border-b border-slate-100 last:border-0 dark:border-slate-800">
            <div class="sticky top-16 z-10 bg-slate-50/90 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 backdrop-blur dark:bg-slate-900/90 dark:text-slate-400">
                {{ $day === '—' ? '—' : \Illuminate\Support\Carbon::parse($day)->isoFormat('dddd, MMMM D, YYYY') }}
            </div>

            <ol class="relative ml-4 border-l border-slate-200 py-2 dark:border-slate-700">
                @foreach ($items as $log)
                    <li class="relative py-3 pl-6 pr-4">
                        <span class="absolute -left-[5px] top-4 h-2.5 w-2.5 rounded-full ring-4 ring-white dark:ring-slate-900
                            {{ $log->event === 'created' ? 'bg-emerald-500' : ($log->event === 'updated' ? 'bg-amber-500' : ($log->event === 'deleted' ? 'bg-rose-500' : 'bg-slate-400')) }}"></span>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $present::eventClasses($log->event) }}">
                                {{ $log->event ?? '—' }}
                            </span>
                            <span class="text-sm text-slate-900 dark:text-slate-100">{{ $log->description }}</span>
                            @if ($log->subject_type)
                                <a href="{{ route('activitylog-ui.index', ['model' => $log->subject_type, 'subject_id' => $log->subject_id]) }}"
                                   class="text-xs font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                                    {{ $present::subjectLabel($log) }} #{{ $log->subject_id }}
                                </a>
                            @endif
                        </div>

                        <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                            @if ($log->created_at)
                                <time datetime="{{ $log->created_at->toIso8601String() }}" title="{{ $log->created_at->format($dateFormat) }}">
                                    {{ $log->created_at->format('H:i:s') }}
                                </time>
                            @endif
                            <span>·</span>
                            <span>{{ $log->causer_type && $log->causer_id !== null ? $present::causerName($log) : __('activitylog-ui::messages.table.system') }}</span>
                            <a href="{{ route('activitylog-ui.show', $log->getKey()) }}" class="text-indigo-600 hover:underline dark:text-indigo-400">
                                {{ __('activitylog-ui::messages.actions.details') }}
                            </a>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    @empty
        <div class="px-4 py-16 text-center">
            <p class="font-medium text-slate-700 dark:text-slate-300">{{ __('activitylog-ui::messages.table.empty') }}</p>
        </div>
    @endforelse

    @include('activitylog-ui::partials.pagination', ['paginator' => $logs])
</div>
