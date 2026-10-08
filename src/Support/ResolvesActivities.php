<?php

namespace Nsd7\LaravelActivitylogUi\Support;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\ActivitylogServiceProvider;

/**
 * Shared activity-model lookups used by the UI, analytics and API controllers.
 */
trait ResolvesActivities
{
    protected function query(): Builder
    {
        $model = ActivitylogServiceProvider::determineActivityModel();

        return $model::query();
    }

    protected function activityTable(): string
    {
        $model = ActivitylogServiceProvider::determineActivityModel();

        return (new $model())->getTable();
    }

    /**
     * Distinct subject types as value (FQCN) => label (basename) pairs.
     * The full class name is shown when two models share a basename.
     *
     * @return array<string, string>
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

    /**
     * @return array<int, string>
     */
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

    /**
     * @return array<int, int>
     */
    protected function perPageOptions(): array
    {
        return array_values(array_filter(
            array_map('intval', (array) config('activitylog-ui.per_page_options', [10, 15, 25, 50, 100])),
            fn ($option) => $option > 0
        ));
    }

    protected function perPage($request): int
    {
        $default = (int) config('activitylog-ui.per_page', 15);
        $requested = (int) $request->query('per_page', $default);

        return in_array($requested, $this->perPageOptions(), true) ? $requested : $default;
    }
}
