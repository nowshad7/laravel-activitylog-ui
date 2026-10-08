<?php

namespace Nsd7\LaravelActivitylogUi\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Nsd7\LaravelActivitylogUi\Filters\ActivityLogFilters;
use Nsd7\LaravelActivitylogUi\Support\ActivityPresenter;
use Nsd7\LaravelActivitylogUi\Support\ResolvesActivities;

/**
 * Read-only JSON API over the same filters as the UI, so the activity log is
 * consumable by SPAs, dashboards and automated agents. Opt-in via
 * config('activitylog-ui.features.api').
 */
class ActivityLogApiController extends Controller
{
    use ResolvesActivities;

    public function index(Request $request): JsonResponse
    {
        abort_unless(config('activitylog-ui.features.api', false), 404);

        $filters = ActivityLogFilters::fromRequest($request);

        $paginator = $filters->apply($this->query())
            ->with('causer')
            ->orderByDesc('id')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return response()->json([
            'data' => collect($paginator->items())->map(fn (Model $activity) => $this->transform($activity)),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'next' => $paginator->nextPageUrl(),
                'prev' => $paginator->previousPageUrl(),
            ],
        ]);
    }

    public function show($activity): JsonResponse
    {
        abort_unless(config('activitylog-ui.features.api', false), 404);

        $activity = $this->query()->with('causer')->findOrFail($activity);

        return response()->json([
            'data' => array_merge($this->transform($activity), [
                'changes' => ActivityPresenter::changes($activity),
                'custom_properties' => ActivityPresenter::customProperties($activity),
            ]),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function transform(Model $activity): array
    {
        return [
            'id' => $activity->getKey(),
            'log_name' => $activity->log_name,
            'description' => $activity->description,
            'event' => $activity->event,
            'subject_type' => $activity->subject_type,
            'subject_id' => $activity->subject_id,
            'causer_type' => $activity->causer_type,
            'causer_id' => $activity->causer_id,
            'causer' => ActivityPresenter::causerName($activity),
            'batch_uuid' => $activity->batch_uuid,
            'created_at' => optional($activity->created_at)->toIso8601String(),
        ];
    }
}
