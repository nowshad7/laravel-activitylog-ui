@php($currentUrl = request()->fullUrl())

<div class="mb-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
     x-data="{ saving: false }">
    <div class="flex flex-wrap items-center gap-2">
        <span class="text-sm font-semibold">{{ __('activitylog-ui::messages.saved_views.heading') }}</span>

        @forelse ($savedViews as $view)
            <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 pl-3 pr-1 text-xs text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                <a href="{{ route('activitylog-ui.index', $view->queryString()) }}" class="py-1 font-medium hover:text-indigo-600 dark:hover:text-indigo-400">
                    {{ $view->name }}
                </a>
                <form method="POST" action="{{ route('activitylog-ui.saved-views.destroy', $view->getKey()) }}"
                      onsubmit="return confirm('{{ __('activitylog-ui::messages.saved_views.confirm_delete') }}')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="redirect" value="{{ route('activitylog-ui.index') }}">
                    <button type="submit" class="rounded-full px-1.5 py-1 text-slate-400 hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-500/10" aria-label="{{ __('activitylog-ui::messages.actions.delete') }}">
                        &times;
                    </button>
                </form>
            </span>
        @empty
            <span class="text-xs text-slate-400">{{ __('activitylog-ui::messages.saved_views.none') }}</span>
        @endforelse

        <button type="button" @click="saving = !saving"
                class="ml-auto inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h8l4 4v10a2 2 0 01-2 2H7a2 2 0 01-2-2V5z"/>
            </svg>
            {{ __('activitylog-ui::messages.actions.save_view') }}
        </button>
    </div>

    <form x-show="saving" x-cloak method="POST" action="{{ route('activitylog-ui.saved-views.store') }}" class="mt-3 flex flex-wrap items-center gap-2">
        @csrf
        @foreach ($filters->all() as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <input type="hidden" name="redirect" value="{{ $currentUrl }}">
        <input type="text" name="name" required maxlength="100"
               placeholder="{{ __('activitylog-ui::messages.saved_views.name_placeholder') }}"
               class="block w-56 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            {{ __('activitylog-ui::messages.saved_views.save_current') }}
        </button>
    </form>
</div>
