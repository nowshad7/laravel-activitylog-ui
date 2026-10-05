@php
    $cards = [
        ['label' => 'Total', 'value' => $stats['total'], 'event' => null, 'accent' => 'text-indigo-600 dark:text-indigo-400'],
        ['label' => 'Created', 'value' => $stats['created'], 'event' => 'created', 'accent' => 'text-emerald-600 dark:text-emerald-400'],
        ['label' => 'Updated', 'value' => $stats['updated'], 'event' => 'updated', 'accent' => 'text-amber-600 dark:text-amber-400'],
        ['label' => 'Deleted', 'value' => $stats['deleted'], 'event' => 'deleted', 'accent' => 'text-rose-600 dark:text-rose-400'],
    ];
@endphp

<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    @foreach ($cards as $card)
        <a href="{{ route('activitylog-ui.index', array_filter(array_merge($filters->all(), ['event' => $card['event']]))) }}"
           class="group rounded-xl border bg-white p-4 shadow-sm transition hover:border-indigo-300 dark:bg-slate-900 dark:hover:border-indigo-500/50 {{ $filters->get('event') === $card['event'] && $card['event'] ? 'border-indigo-400 ring-1 ring-indigo-400 dark:border-indigo-500' : 'border-slate-200 dark:border-slate-800' }}">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums {{ $card['accent'] }}" data-stat="{{ strtolower($card['label']) }}">{{ number_format($card['value']) }}</p>
        </a>
    @endforeach
</div>
