<?php

namespace Nsd7\LaravelActivitylogUi\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ActivityLogFilters
{
    public const KEYS = [
        'search',
        'log_name',
        'model',
        'subject_id',
        'event',
        'causer_type',
        'causer_id',
        'date_from',
        'date_to',
        'batch_uuid',
    ];

    /** @var array<string, string> */
    protected $values = [];

    public function __construct(array $input = [])
    {
        foreach (self::KEYS as $key) {
            $value = $input[$key] ?? null;

            if (! is_scalar($value)) {
                continue;
            }

            $value = trim((string) $value);

            if ($value === '') {
                continue;
            }

            if (in_array($key, ['date_from', 'date_to'], true) && ! $this->isValidDate($value)) {
                continue;
            }

            $this->values[$key] = $value;
        }
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->query());
    }

    public function get(string $key, $default = null)
    {
        return $this->values[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->values;
    }

    public function count(): int
    {
        return count($this->values);
    }

    public function apply(Builder $query): Builder
    {
        return $query
            ->when($this->get('search'), function (Builder $query, $search) {
                if (config('activitylog-ui.search_driver') === 'fulltext') {
                    $query->whereFullText('description', $search);
                } else {
                    $query->where('description', 'like', '%' . $search . '%');
                }
            })
            ->when($this->get('log_name'), fn (Builder $query, $logName) => $query->where('log_name', $logName))
            ->when($this->get('model'), fn (Builder $query, $model) => $this->applyModel($query, $model))
            ->when($this->get('subject_id'), fn (Builder $query, $id) => $query->where('subject_id', $id))
            ->when($this->get('event'), fn (Builder $query, $event) => $query->where('event', $event))
            ->when($this->get('causer_type'), fn (Builder $query, $type) => $query->where('causer_type', $type))
            ->when($this->get('causer_id'), fn (Builder $query, $id) => $query->where('causer_id', $id))
            ->when($this->get('batch_uuid'), fn (Builder $query, $uuid) => $query->where('batch_uuid', $uuid))
            ->when($this->get('date_from'), function (Builder $query, $date) {
                $query->where('created_at', '>=', Carbon::createFromFormat('Y-m-d', $date)->startOfDay());
            })
            ->when($this->get('date_to'), function (Builder $query, $date) {
                $query->where('created_at', '<=', Carbon::createFromFormat('Y-m-d', $date)->endOfDay());
            });
    }

    /**
     * Accepts a fully qualified class name (or morph alias) for an exact match,
     * or a class basename (e.g. "User") for backwards compatible links.
     */
    protected function applyModel(Builder $query, string $model): void
    {
        if (str_contains($model, '\\')) {
            $query->where('subject_type', $model);

            return;
        }

        $types = $query->getModel()->newQuery()
            ->whereNotNull('subject_type')
            ->distinct()
            ->pluck('subject_type')
            ->filter(fn ($type) => $type === $model || class_basename($type) === $model)
            ->values()
            ->all();

        $query->whereIn('subject_type', $types);
    }

    protected function isValidDate(string $value): bool
    {
        if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)) {
            return false;
        }

        return checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
    }
}
