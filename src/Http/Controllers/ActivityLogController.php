<?php

namespace Nsd7\LaravelActivitylogUi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Nsd7\LaravelActivitylogUi\Filters\ActivityLogFilters;
use Nsd7\LaravelActivitylogUi\Support\ActivityExporter;
use Nsd7\LaravelActivitylogUi\Support\ActivityPresenter;
use Nsd7\LaravelActivitylogUi\Support\DatePresets;
use Nsd7\LaravelActivitylogUi\Support\ResolvesActivities;
use Nsd7\LaravelActivitylogUi\Support\SavedViewRepository;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogController extends Controller
{
    use ResolvesActivities;

    public function index(Request $request)
    {
        $filters = ActivityLogFilters::fromRequest($request);
        $perPage = $this->perPage($request);

        $logs = $filters->apply($this->query())
            ->with('causer')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('activitylog-ui::index', [
            'logs' => $logs,
            'filters' => $filters,
            'viewMode' => $this->viewMode($request),
            'stats' => config('activitylog-ui.show_stats', true) ? $this->stats($filters) : null,
            'models' => $this->subjectTypes(),
            'events' => $this->distinctValues('event'),
            'logNames' => $this->distinctValues('log_name'),
            'perPage' => $perPage,
            'perPageOptions' => $this->perPageOptions(),
            'datePresets' => DatePresets::forFilters($filters),
            'savedViews' => SavedViewRepository::forUser($request->user()),
        ]);
    }

    public function show($activity)
    {
        $activity = $this->query()->with('causer')->findOrFail($activity);

        $history = collect();

        if ($activity->subject_type && $activity->subject_id !== null) {
            $history = $this->query()
                ->with('causer')
                ->where('subject_type', $activity->subject_type)
                ->where('subject_id', $activity->subject_id)
                ->orderByDesc('id')
                ->limit(20)
                ->get();
        }

        return view('activitylog-ui::show', [
            'activity' => $activity,
            'changes' => ActivityPresenter::changes($activity),
            'customProperties' => ActivityPresenter::customProperties($activity),
            'history' => $history,
        ]);
    }

    public function export(Request $request): Response
    {
        abort_unless(config('activitylog-ui.export.enabled', true), 404);

        $filters = ActivityLogFilters::fromRequest($request);

        $exporter = new ActivityExporter(
            $filters->apply($this->query()),
            (int) config('activitylog-ui.export.limit', 10000),
            config('activitylog-ui.date_format', 'Y-m-d H:i:s')
        );

        return $exporter->download((string) $request->query('format', 'csv'));
    }

    /**
     * Lightweight JSON total for the live-count poller.
     */
    public function count(Request $request): JsonResponse
    {
        abort_unless(config('activitylog-ui.features.live_counts', false), 404);

        $filters = ActivityLogFilters::fromRequest($request);

        return response()->json([
            'total' => $filters->apply($this->query())->toBase()->count(),
        ]);
    }

    protected function viewMode(Request $request): string
    {
        $mode = (string) $request->query('view', 'table');

        $allowed = ['table'];

        if (config('activitylog-ui.features.timeline', true)) {
            $allowed[] = 'timeline';
        }

        return in_array($mode, $allowed, true) ? $mode : 'table';
    }

    /**
     * Event counts for the currently filtered result set.
     *
     * @return array{total: int, created: int, updated: int, deleted: int}
     */
    protected function stats(ActivityLogFilters $filters): array
    {
        $counts = $filters->apply($this->query())
            ->toBase()
            ->selectRaw('event, count(*) as aggregate')
            ->groupBy('event')
            ->pluck('aggregate', 'event')
            ->map(fn ($count) => (int) $count);

        return [
            'total' => $counts->sum(),
            'created' => $counts->get('created', 0),
            'updated' => $counts->get('updated', 0),
            'deleted' => $counts->get('deleted', 0),
        ];
    }
}
