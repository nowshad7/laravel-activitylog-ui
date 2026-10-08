@php
    $formats = array_values(array_intersect(
        array_map('strtolower', (array) config('activitylog-ui.export.formats', ['csv'])),
        ['csv', 'json', 'xlsx', 'pdf']
    )) ?: ['csv'];
    $formatLabels = ['csv' => 'CSV', 'json' => 'JSON', 'xlsx' => 'Excel (XLSX)', 'pdf' => 'PDF'];
@endphp

<div class="relative" x-data="{ open: false }" @keydown.escape="open = false">
    <button type="button" @click="open = !open" :aria-expanded="open.toString()"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        {{ __('activitylog-ui::messages.actions.export') }}
        <svg class="h-3.5 w-3.5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-cloak @click.outside="open = false"
         class="absolute right-0 z-30 mt-2 w-44 overflow-hidden rounded-lg border border-slate-200 bg-white py-1 shadow-lg dark:border-slate-800 dark:bg-slate-900">
        @foreach ($formats as $format)
            <a href="{{ route('activitylog-ui.export', array_merge($filters->all(), ['format' => $format])) }}"
               class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                {{ $formatLabels[$format] ?? strtoupper($format) }}
            </a>
        @endforeach
    </div>
</div>
