<?php

namespace Nsd7\LaravelActivitylogUi\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Nsd7\LaravelActivitylogUi\Filters\ActivityLogFilters;

/**
 * Computes the aggregate datasets rendered on the analytics dashboard.
 *
 * Every metric runs against a freshly filtered query so a single instance can
 * produce several aggregates. Grouping by calendar day uses a per-driver date
 * expression so it stays portable across MySQL, PostgreSQL, SQLite and SQL Server.
 */
class Analytics
{
    protected ActivityLogFilters $filters;

    /** @var callable(): Builder */
    protected $queryFactory;

    public function __construct(ActivityLogFilters $filters, callable $queryFactory)
    {
        $this->filters = $filters;
        $this->queryFactory = $queryFactory;
    }

    /**
     * @return array{
     *     totals: array<string, int>,
     *     per_day: array<string, int>,
     *     per_event: array<string, int>,
     *     per_log_name: array<string, int>,
     *     top_causers: array<int, array{label: string, count: int}>,
     *     top_subjects: array<int, array{label: string, count: int}>
     * }
     */
    public function payload(): array
    {
        return [
            'totals' => $this->totals(),
            'per_day' => $this->perDay(),
            'per_event' => $this->perEvent(),
            'per_log_name' => $this->perLogName(),
            'top_causers' => $this->topCausers(),
            'top_subjects' => $this->topSubjects(),
        ];
    }

    protected function query(): Builder
    {
        return $this->filters->apply(($this->queryFactory)());
    }

    /**
     * @return array<string, int>
     */
    protected function totals(): array
    {
        $counts = $this->query()->toBase()
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
     * Daily activity counts over the filter's range (or the configured window),
     * with gaps filled so the chart has a continuous x-axis.
     *
     * @return array<string, int>
     */
    protected function perDay(): array
    {
        [$start, $end] = $this->range();
        $expression = $this->dateExpression();

        $rows = $this->query()->toBase()
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->selectRaw("{$expression} as day, count(*) as aggregate")
            ->groupByRaw($expression)
            ->pluck('aggregate', 'day');

        $series = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $series[$key] = (int) ($rows[$key] ?? 0);
        }

        return $series;
    }

    /**
     * @return array<string, int>
     */
    protected function perEvent(): array
    {
        return $this->query()->toBase()
            ->whereNotNull('event')
            ->selectRaw('event, count(*) as aggregate')
            ->groupBy('event')
            ->orderByDesc('aggregate')
            ->pluck('aggregate', 'event')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    /**
     * @return array<string, int>
     */
    protected function perLogName(): array
    {
        return $this->query()->toBase()
            ->whereNotNull('log_name')
            ->selectRaw('log_name, count(*) as aggregate')
            ->groupBy('log_name')
            ->orderByDesc('aggregate')
            ->limit(8)
            ->pluck('aggregate', 'log_name')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    /**
     * @return array<int, array{label: string, count: int}>
     */
    protected function topCausers(): array
    {
        $rows = $this->query()->toBase()
            ->whereNotNull('causer_type')
            ->whereNotNull('causer_id')
            ->selectRaw('causer_type, causer_id, count(*) as aggregate')
            ->groupBy('causer_type', 'causer_id')
            ->orderByDesc('aggregate')
            ->limit(8)
            ->get();

        $names = $this->resolveCauserNames($rows);

        return $rows->map(fn ($row) => [
            'label' => $names["{$row->causer_type}#{$row->causer_id}"]
                ?? class_basename($row->causer_type) . ' #' . $row->causer_id,
            'count' => (int) $row->aggregate,
        ])->all();
    }

    /**
     * @return array<int, array{label: string, count: int}>
     */
    protected function topSubjects(): array
    {
        return $this->query()->toBase()
            ->whereNotNull('subject_type')
            ->selectRaw('subject_type, count(*) as aggregate')
            ->groupBy('subject_type')
            ->orderByDesc('aggregate')
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'label' => class_basename($row->subject_type),
                'count' => (int) $row->aggregate,
            ])
            ->all();
    }

    /**
     * Resolve display names for the top causers, one query per causer type.
     *
     * @param  \Illuminate\Support\Collection  $rows
     * @return array<string, string>
     */
    protected function resolveCauserNames($rows): array
    {
        $attributes = (array) config('activitylog-ui.causer_display_attributes', ['name']);
        $names = [];

        foreach ($rows->groupBy('causer_type') as $type => $group) {
            if (! is_string($type) || ! class_exists($type) || ! is_subclass_of($type, Model::class)) {
                continue;
            }

            $ids = $group->pluck('causer_id')->all();

            /** @var Model $instance */
            $instance = new $type();

            $instance->newQuery()
                ->whereIn($instance->getKeyName(), $ids)
                ->get()
                ->each(function (Model $causer) use (&$names, $type, $attributes) {
                    foreach ($attributes as $attribute) {
                        $value = $causer->getAttribute($attribute);

                        if (is_scalar($value) && trim((string) $value) !== '') {
                            $names["{$type}#{$causer->getKey()}"] = (string) $value;

                            return;
                        }
                    }
                });
        }

        return $names;
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function range(): array
    {
        $end = $this->filters->get('date_to')
            ? Carbon::createFromFormat('Y-m-d', $this->filters->get('date_to'))->startOfDay()
            : Carbon::today();

        if ($this->filters->get('date_from')) {
            $start = Carbon::createFromFormat('Y-m-d', $this->filters->get('date_from'))->startOfDay();
        } else {
            $days = max(1, (int) config('activitylog-ui.analytics.range_days', 30));
            $start = $end->copy()->subDays($days - 1);
        }

        // Guard against an unbounded or inverted range blowing up the x-axis.
        if ($start->gt($end)) {
            $start = $end->copy();
        }

        if ($start->diffInDays($end) > 366) {
            $start = $end->copy()->subDays(366);
        }

        return [$start, $end];
    }

    protected function dateExpression(): string
    {
        $column = 'created_at';

        return match ($this->driver()) {
            'pgsql', 'sqlsrv' => "CAST({$column} AS date)",
            'sqlite' => "date({$column})",
            default => "DATE({$column})",
        };
    }

    protected function driver(): string
    {
        return (string) (($this->queryFactory)()->getConnection()->getDriverName());
    }
}
