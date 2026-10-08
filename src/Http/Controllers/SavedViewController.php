<?php

namespace Nsd7\LaravelActivitylogUi\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Nsd7\LaravelActivitylogUi\Filters\ActivityLogFilters;
use Nsd7\LaravelActivitylogUi\Support\SavedViewRepository;

class SavedViewController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless(SavedViewRepository::enabled(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $filters = (new ActivityLogFilters($request->all()))->all();

        SavedViewRepository::create($request->user(), $validated['name'], $filters);

        return redirect()
            ->to($request->input('redirect', route('activitylog-ui.index', $filters)))
            ->with('activitylog-ui.status', __('activitylog-ui::messages.view_saved'));
    }

    public function destroy(Request $request, int $savedView): RedirectResponse
    {
        abort_unless(SavedViewRepository::enabled(), 404);

        SavedViewRepository::deleteForUser($request->user(), $savedView);

        return redirect()
            ->to($request->input('redirect', route('activitylog-ui.index')))
            ->with('activitylog-ui.status', __('activitylog-ui::messages.view_deleted'));
    }
}
