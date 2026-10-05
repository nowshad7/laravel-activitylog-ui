<?php

namespace Nsd7\LaravelActivitylogUi\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Nsd7\LaravelActivitylogUi\Filters\ActivityLogFilters;
use Nsd7\LaravelActivitylogUi\Support\ActivityPresenter;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
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
            'stats' => config('activitylog-ui.show_stats', true) ? $this->stats($filters) : null,
            'models' => $this->subjectTypes(),
            'events' => $this->distinctValues('event'),
            'logNames' => $this->distinctValues('log_name'),
            'perPage' => $perPage,
            'perPageOptions' => $this->perPageOptions(),
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

    public function export(Request $request): StreamedResponse
    {
        abort_unless(config('activitylog-ui.export.enabled', true), 404);

        $filters = ActivityLogFilters::fromRequest($request);
        $limit = max(1, (int) config('activitylog-ui.export.limit', 10000));
        $dateFormat = config('activitylog-ui.date_format', 'Y-m-d H:i:s');

        $rows = $filters->apply($this->query())
            ->with('causer')
            ->lazyByIdDesc(500)
            ->take($limit);

        return response()->streamDownload(function () use ($rows, $dateFormat) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'id', 'log_name', 'description', 'event', 'subject_type', 'subject_id',
                'causer_type', 'causer_id', 'causer', 'properties', 'batch_uuid', 'created_at',
            ]);

            foreach ($rows as $activity) {
                fputcsv($handle, array_map([$this, 'csvSafe'], [
                    $activity->id,
                    $activity->log_name,
                    $activity->description,
                    $activity->event,
                    $activity->subject_type,
                    $activity->subject_id,
                    $activity->causer_type,
                    $activity->causer_id,
                    ActivityPresenter::causerName($activity),
                    json_encode($activity->properties, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    $activity->batch_uuid,
                    optional($activity->created_at)->format($dateFormat),
                ]));
            }

            fclose($handle);
        }, 'activity-log-' . now()->format('Y-m-d-His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function query(): Builder
    {
        $model = ActivitylogServiceProvider::determineActivityModel();

        return $model::query();
    }

    /**
     * Event counts for the currently filtered result set.
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

    /**
     * Distinct subject types as value (FQCN) => label (basename) pairs.
     * The full class name is shown when two models share a basename.
     */
    protected function subjectTypes(): array
    {
        $types = $this->distinctValues('subject_type');
        $basenames = array_count_values(array_map('class_basename', $types));

        return collect($types)
            ->mapWithKeys(function ($type) use ($basenames) {
                $basename = class_basename($type);

                return [$type => $basenames[$basename] > 1 ? $type : $basename];
            })
            ->sort()
            ->all();
    }

    protected function distinctValues(string $column): array
    {
        return $this->query()
            ->toBase()
            ->whereNotNull($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->map(fn ($value) => (string) $value)
            ->all();
    }

    protected function perPageOptions(): array
    {
        return array_values(array_filter(
            array_map('intval', (array) config('activitylog-ui.per_page_options', [10, 15, 25, 50, 100])),
            fn ($option) => $option > 0
        ));
    }

    protected function perPage(Request $request): int
    {
        $default = (int) config('activitylog-ui.per_page', 15);
        $requested = (int) $request->query('per_page', $default);

        return in_array($requested, $this->perPageOptions(), true) ? $requested : $default;
    }

    /**
     * Prevent CSV/formula injection when the export is opened in a spreadsheet.
     */
    public function csvSafe($value)
    {
        if (is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $value;
        }

        return $value;
    }
}
