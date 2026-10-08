<?php

namespace Nsd7\LaravelActivitylogUi\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A named, per-user set of saved filters.
 *
 * @property int $id
 * @property string $name
 * @property array $filters
 */
class SavedView extends Model
{
    protected $guarded = [];

    protected $casts = [
        'filters' => 'array',
    ];

    public function getTable()
    {
        return config('activitylog-ui.saved_views.table', 'activitylog_ui_saved_views');
    }

    /**
     * Query string (minus empty values) that re-applies this view's filters.
     */
    public function queryString(): array
    {
        return array_filter((array) $this->filters, fn ($value) => $value !== null && $value !== '');
    }
}
