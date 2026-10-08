@extends('activitylog-ui::layouts.app')

@section('title', __('activitylog-ui::messages.title'))

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('activitylog-ui::messages.title') }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('activitylog-ui::messages.subtitle') }}</p>
        </div>

        @if (config('activitylog-ui.export.enabled', true))
            @include('activitylog-ui::partials.export-menu')
        @endif
    </div>

    @include('activitylog-ui::partials.nav')

    @include('activitylog-ui::partials.flash')

    @if ($stats)
        @include('activitylog-ui::partials.stats')
    @endif

    @if (\Nsd7\LaravelActivitylogUi\Support\SavedViewRepository::enabled() && auth()->check())
        @include('activitylog-ui::partials.saved-views')
    @endif

    @include('activitylog-ui::partials.filters')

    @if (($viewMode ?? 'table') === 'timeline')
        @include('activitylog-ui::partials.timeline')
    @else
        @include('activitylog-ui::partials.table')
    @endif
@endsection
