@php
    $tabs = [];
    $tabs[] = ['key' => 'table', 'label' => __('activitylog-ui::messages.nav.list'), 'url' => route('activitylog-ui.index', $filters->all())];

    if (config('activitylog-ui.features.timeline', true)) {
        $tabs[] = ['key' => 'timeline', 'label' => __('activitylog-ui::messages.nav.timeline'), 'url' => route('activitylog-ui.index', array_merge($filters->all(), ['view' => 'timeline']))];
    }

    if (config('activitylog-ui.features.analytics', true)) {
        $tabs[] = ['key' => 'analytics', 'label' => __('activitylog-ui::messages.nav.analytics'), 'url' => route('activitylog-ui.analytics', $filters->all())];
    }

    $active = $viewMode ?? 'table';
@endphp

@if (count($tabs) > 1)
    <div class="mb-6 inline-flex rounded-lg border border-slate-200 bg-white p-1 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @foreach ($tabs as $tab)
            <a href="{{ $tab['url'] }}"
               @class([
                   'rounded-md px-4 py-1.5 text-sm font-medium transition',
                   'bg-indigo-600 text-white shadow-sm' => $active === $tab['key'],
                   'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' => $active !== $tab['key'],
               ])>
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
@endif
