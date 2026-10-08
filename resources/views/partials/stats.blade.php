@php
    $cards = [
        ['label' => __('activitylog-ui::messages.stats.total'), 'key' => 'total', 'value' => $stats['total'], 'event' => null, 'accent' => 'text-indigo-600 dark:text-indigo-400'],
        ['label' => __('activitylog-ui::messages.stats.created'), 'key' => 'created', 'value' => $stats['created'], 'event' => 'created', 'accent' => 'text-emerald-600 dark:text-emerald-400'],
        ['label' => __('activitylog-ui::messages.stats.updated'), 'key' => 'updated', 'value' => $stats['updated'], 'event' => 'updated', 'accent' => 'text-amber-600 dark:text-amber-400'],
        ['label' => __('activitylog-ui::messages.stats.deleted'), 'key' => 'deleted', 'value' => $stats['deleted'], 'event' => 'deleted', 'accent' => 'text-rose-600 dark:text-rose-400'],
    ];
    $liveCounts = config('activitylog-ui.features.live_counts', false);
    $pollInterval = max(5, (int) config('activitylog-ui.live_counts.poll_interval', 15)) * 1000;
@endphp

<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4"
    @if ($liveCounts)
        x-data="{
            total: {{ (int) $stats['total'] }},
            poll() {
                fetch('{{ route('activitylog-ui.count', $filters->all()) }}', { headers: { 'Accept': 'application/json' } })
                    .then(r => r.ok ? r.json() : null)
                    .then(d => { if (d && typeof d.total !== 'undefined') this.total = d.total; })
                    .catch(() => {});
            }
        }"
        x-init="setInterval(() => poll(), {{ $pollInterval }})"
    @endif
>
    @foreach ($cards as $card)
        <a href="{{ route('activitylog-ui.index', array_filter(array_merge($filters->all(), ['event' => $card['event']]))) }}"
           class="group rounded-xl border bg-white p-4 shadow-sm transition hover:border-indigo-300 dark:bg-slate-900 dark:hover:border-indigo-500/50 {{ $filters->get('event') === $card['event'] && $card['event'] ? 'border-indigo-400 ring-1 ring-indigo-400 dark:border-indigo-500' : 'border-slate-200 dark:border-slate-800' }}">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums {{ $card['accent'] }}" data-stat="{{ $card['key'] }}"
               @if ($liveCounts && $card['key'] === 'total') x-text="new Intl.NumberFormat().format(total)" @endif>{{ number_format($card['value']) }}</p>
        </a>
    @endforeach
</div>
