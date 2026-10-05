<?php

namespace Nsd7\LaravelActivitylogUi\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ActivityPresenter
{
    /**
     * Human readable name of whoever caused the activity.
     */
    public static function causerName(Model $activity): string
    {
        $causer = $activity->causer;

        if ($causer instanceof Model) {
            foreach ((array) config('activitylog-ui.causer_display_attributes', ['name']) as $attribute) {
                $value = $causer->getAttribute($attribute);

                if (is_scalar($value) && trim((string) $value) !== '') {
                    return (string) $value;
                }
            }

            return class_basename($causer) . ' #' . $causer->getKey();
        }

        if ($activity->causer_type && $activity->causer_id) {
            // The causer was deleted or cannot be resolved anymore.
            return class_basename($activity->causer_type) . ' #' . $activity->causer_id;
        }

        return 'System';
    }

    public static function subjectLabel(Model $activity): ?string
    {
        if (! $activity->subject_type) {
            return null;
        }

        return class_basename($activity->subject_type);
    }

    /**
     * Tailwind classes used for the event badge.
     */
    public static function eventClasses(?string $event): string
    {
        switch ($event) {
            case 'created':
                return 'bg-emerald-100 text-emerald-800 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30';
            case 'updated':
                return 'bg-amber-100 text-amber-800 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/30';
            case 'deleted':
                return 'bg-rose-100 text-rose-800 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-500/30';
            case 'restored':
                return 'bg-sky-100 text-sky-800 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-300 dark:ring-sky-500/30';
            default:
                return 'bg-slate-100 text-slate-700 ring-slate-500/20 dark:bg-slate-500/10 dark:text-slate-300 dark:ring-slate-500/30';
        }
    }

    /**
     * Builds a field level diff from the "attributes" and "old" properties
     * that spatie/laravel-activitylog stores for model events.
     *
     * @return array<int, array{key: string, old: mixed, new: mixed, status: string}>
     */
    public static function changes(Model $activity): array
    {
        $properties = $activity->properties instanceof Collection
            ? $activity->properties->toArray()
            : (array) $activity->properties;

        $new = isset($properties['attributes']) && is_array($properties['attributes']) ? $properties['attributes'] : [];
        $old = isset($properties['old']) && is_array($properties['old']) ? $properties['old'] : [];

        $rows = [];

        foreach (array_unique(array_merge(array_keys($old), array_keys($new))) as $key) {
            $hasOld = array_key_exists($key, $old);
            $hasNew = array_key_exists($key, $new);

            if ($hasOld && $hasNew) {
                $status = $old[$key] === $new[$key] ? 'unchanged' : 'changed';
            } else {
                $status = $hasNew ? 'added' : 'removed';
            }

            $rows[] = [
                'key' => (string) $key,
                'old' => $hasOld ? $old[$key] : null,
                'new' => $hasNew ? $new[$key] : null,
                'status' => $status,
            ];
        }

        return $rows;
    }

    /**
     * Properties other than the attribute diff (custom properties).
     */
    public static function customProperties(Model $activity): array
    {
        $properties = $activity->properties instanceof Collection
            ? $activity->properties->toArray()
            : (array) $activity->properties;

        return array_diff_key($properties, array_flip(['attributes', 'old']));
    }

    public static function formatValue($value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value) || is_object($value)) {
            return (string) json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    }

    public static function prettyJson($value): string
    {
        if ($value instanceof Collection) {
            $value = $value->toArray();
        }

        return (string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
