<?php

namespace Nsd7\LaravelActivitylogUi\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Nsd7\LaravelActivitylogUi\Models\SavedView;

/**
 * Persists and scopes per-user saved filter views, degrading gracefully when
 * the feature is disabled or the migration has not been run.
 */
class SavedViewRepository
{
    public static function enabled(): bool
    {
        return config('activitylog-ui.features.saved_views', true)
            && Schema::hasTable(config('activitylog-ui.saved_views.table', 'activitylog_ui_saved_views'));
    }

    /**
     * @return Collection<int, SavedView>
     */
    public static function forUser(?Authenticatable $user): Collection
    {
        if (! $user || ! self::enabled()) {
            return new Collection();
        }

        return self::scopeToUser($user)->orderBy('name')->get();
    }

    public static function create(Authenticatable $user, string $name, array $filters): ?SavedView
    {
        if (! self::enabled()) {
            return null;
        }

        $max = (int) config('activitylog-ui.saved_views.max_per_user', 50);

        if ($max > 0 && self::scopeToUser($user)->count() >= $max) {
            return null;
        }

        return SavedView::create([
            'user_type' => self::userType($user),
            'user_id' => $user->getAuthIdentifier(),
            'name' => $name,
            'filters' => $filters,
        ]);
    }

    public static function deleteForUser(Authenticatable $user, int $id): bool
    {
        if (! self::enabled()) {
            return false;
        }

        return (bool) self::scopeToUser($user)->whereKey($id)->delete();
    }

    protected static function scopeToUser(Authenticatable $user)
    {
        return SavedView::query()
            ->where('user_type', self::userType($user))
            ->where('user_id', $user->getAuthIdentifier());
    }

    protected static function userType(Authenticatable $user): string
    {
        return method_exists($user, 'getMorphClass') ? $user->getMorphClass() : get_class($user);
    }
}
