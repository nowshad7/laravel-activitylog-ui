<?php

namespace Nsd7\LaravelActivitylogUi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class Authorize
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        abort_unless($this->passesGate($user), 403);
        abort_unless($this->passesAllowLists($user), 403);

        return $next($request);
    }

    protected function passesGate($user): bool
    {
        $gate = config('activitylog-ui.gate');

        if ($gate && Gate::has($gate)) {
            return Gate::forUser($user)->check($gate);
        }

        return true;
    }

    /**
     * Optional allow-lists layered on top of the gate. Each list is only
     * enforced when it is configured (non-empty).
     */
    protected function passesAllowLists($user): bool
    {
        if (! $user) {
            return true;
        }

        $users = array_filter((array) config('activitylog-ui.access.allowed_users', []));
        $roles = array_filter((array) config('activitylog-ui.access.allowed_roles', []));

        if ($users && ! $this->matchesUser($user, $users)) {
            return false;
        }

        if ($roles && ! $this->matchesRole($user, $roles)) {
            return false;
        }

        return true;
    }

    protected function matchesUser($user, array $allowed): bool
    {
        $identifiers = array_filter([
            $user->getAuthIdentifier(),
            method_exists($user, 'getAttribute') ? $user->getAttribute('email') : null,
        ], fn ($value) => $value !== null);

        foreach ($identifiers as $identifier) {
            if (in_array($identifier, $allowed, false)) {
                return true;
            }
        }

        return false;
    }

    protected function matchesRole($user, array $allowed): bool
    {
        // spatie/laravel-permission and similar.
        if (method_exists($user, 'hasRole')) {
            return (bool) $user->hasRole($allowed);
        }

        if (method_exists($user, 'getRoleNames')) {
            return $user->getRoleNames()->intersect($allowed)->isNotEmpty();
        }

        // A "roles" relation or attribute exposing role names/objects.
        $roles = $user->roles ?? null;

        if ($roles !== null) {
            return collect($roles)
                ->map(fn ($role) => is_object($role) ? ($role->name ?? null) : $role)
                ->filter()
                ->intersect($allowed)
                ->isNotEmpty();
        }

        return false;
    }
}
