<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Nsd7\LaravelActivitylogUi\Tests\Fixtures\User;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class AccessControlTest extends TestCase
{
    public function test_allowed_users_list_restricts_by_id()
    {
        $allowed = $this->makeUser(['email' => 'allowed@example.com']);
        $denied = $this->makeUser(['email' => 'denied@example.com']);

        config(['activitylog-ui.access.allowed_users' => [$allowed->id]]);

        $this->actingAs($allowed)->get(route('activitylog-ui.index'))->assertOk();
        $this->actingAs($denied)->get(route('activitylog-ui.index'))->assertForbidden();
    }

    public function test_allowed_users_list_restricts_by_email()
    {
        $allowed = $this->makeUser(['email' => 'admin@example.com']);
        $denied = $this->makeUser(['email' => 'nobody@example.com']);

        config(['activitylog-ui.access.allowed_users' => ['admin@example.com']]);

        $this->actingAs($allowed)->get(route('activitylog-ui.index'))->assertOk();
        $this->actingAs($denied)->get(route('activitylog-ui.index'))->assertForbidden();
    }

    public function test_an_empty_allow_list_allows_everyone()
    {
        config(['activitylog-ui.access.allowed_users' => [], 'activitylog-ui.access.allowed_roles' => []]);

        $this->actingAs($this->makeUser())->get(route('activitylog-ui.index'))->assertOk();
    }

    public function test_allowed_roles_use_a_has_role_method()
    {
        config(['activitylog-ui.access.allowed_roles' => ['admin']]);
        config(['auth.providers.users.model' => RoleAwareUser::class]);

        $admin = RoleAwareUser::create(['name' => 'Admin', 'email' => 'a@example.com']);
        $admin->rolesList = ['admin'];

        $editor = RoleAwareUser::create(['name' => 'Editor', 'email' => 'e@example.com']);
        $editor->rolesList = ['editor'];

        $this->actingAs($admin)->get(route('activitylog-ui.index'))->assertOk();
        $this->actingAs($editor)->get(route('activitylog-ui.index'))->assertForbidden();
    }
}

class RoleAwareUser extends User
{
    protected $table = 'users';

    public array $rolesList = [];

    public function hasRole($roles): bool
    {
        return (bool) array_intersect((array) $roles, $this->rolesList);
    }
}
