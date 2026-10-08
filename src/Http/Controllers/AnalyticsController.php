<?php

namespace Nsd7\LaravelActivitylogUi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Nsd7\LaravelActivitylogUi\Filters\ActivityLogFilters;
use Nsd7\LaravelActivitylogUi\Support\Analytics;
use Nsd7\LaravelActivitylogUi\Support\DatePresets;
use Nsd7\LaravelActivitylogUi\Support\ResolvesActivities;

class AnalyticsController extends Controller
{
    use ResolvesActivities;

    public function index(Request $request)
    {
        abort_unless(config('activitylog-ui.features.analytics', true), 404);

        $filters = ActivityLogFilters::fromRequest($request);

        return view('activitylog-ui::analytics', [
            'filters' => $filters,
            'viewMode' => 'analytics',
            'analytics' => $this->compute($filters),
            'models' => $this->subjectTypes(),
            'events' => $this->distinctValues('event'),
            'logNames' => $this->distinctValues('log_name'),
            'perPage' => $this->perPage($request),
            'perPageOptions' => $this->perPageOptions(),
            'datePresets' => DatePresets::forFilters($filters),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(config('activitylog-ui.features.analytics', true), 404);

        return response()->json($this->compute(ActivityLogFilters::fromRequest($request)));
    }

    protected function compute(ActivityLogFilters $filters): array
    {
        $analytics = new Analytics($filters, fn () => $this->query());
        $ttl = (int) config('activitylog-ui.analytics.cache_ttl', 3600);

        if ($ttl <= 0) {
            return $analytics->payload();
        }

        $key = 'activitylog-ui.analytics.' . md5(json_encode($filters->all()));

        return Cache::remember($key, $ttl, fn () => $analytics->payload());
    }
}
