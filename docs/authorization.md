# Authorization & access control

Access is checked in three layers. All configured layers must pass.

## 1. Route middleware

`route.middleware` (default `['web', 'auth']`) runs first. Guests hitting the dashboard are redirected to `login`.

## 2. Gate

If a gate named by `gate` (default `viewActivityLogUi`) is defined, it must pass:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewActivityLogUi', fn ($user) => $user->is_admin);
```

If no such gate is defined, this layer is a no-op.

## 3. Allow-lists

Optional lists, each enforced only when non-empty:

```php
'access' => [
    'allowed_users' => [1, 'admin@example.com'], // matches id or email
    'allowed_roles' => ['admin', 'auditor'],      // matches a role name
],
```

Roles are resolved via `hasRole()`, `getRoleNames()` (e.g. spatie/laravel-permission), or a `roles` relation/attribute exposing role names.

The defaults (`[]`) change nothing, so existing setups keep working.
