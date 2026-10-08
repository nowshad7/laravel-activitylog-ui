<?php

namespace Nsd7\LaravelActivitylogUi\Support;

use Illuminate\Support\Carbon;
use Nsd7\LaravelActivitylogUi\Filters\ActivityLogFilters;

/**
 * Quick date-range shortcuts shown above the filter form.
 */
class DatePresets
{
    /**
     * @return array<int, array{key: string, label: string, from: string, to: string, active: bool}>
     */
    public static function forFilters(ActivityLogFilters $filters): array
    {
        $today = Carbon::today();

        $ranges = [
            'today' => ['Today', $today, $today],
            'yesterday' => ['Yesterday', $today->copy()->subDay(), $today->copy()->subDay()],
            '7d' => ['Last 7 days', $today->copy()->subDays(6), $today],
            '30d' => ['Last 30 days', $today->copy()->subDays(29), $today],
            'this_month' => ['This month', $today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
        ];

        $currentFrom = $filters->get('date_from');
        $currentTo = $filters->get('date_to');

        return collect($ranges)
            ->map(function ($range, $key) use ($currentFrom, $currentTo) {
                [$label, $from, $to] = $range;
                $fromStr = $from->format('Y-m-d');
                $toStr = $to->format('Y-m-d');

                return [
                    'key' => $key,
                    'label' => $label,
                    'from' => $fromStr,
                    'to' => $toStr,
                    'active' => $currentFrom === $fromStr && $currentTo === $toStr,
                ];
            })
            ->values()
            ->all();
    }
}
