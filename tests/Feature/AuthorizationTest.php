<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Illuminate\Support\Facades\Gate;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class AuthorizationTest extends TestCase
{
    public function test_access_is_allowed_when_no_gate_is_defined()
    {
        $this->actingAs($this->makeUser())->get(route('activitylog-ui.index'))->assertOk();
    }

    public function test_the_gate_can_deny_access()
    {
        Gate::define('viewActivityLogUi', fn ($user) => $user->email === 'admin@example.com');

        $this->actingAs($this->makeUser(['email' => 'someone@example.com']))
            ->get(route('activitylog-ui.index'))
            ->assertForbidden();

        $activity = $this->logActivity();

        $this->get(route('activitylog-ui.show', $activity->id))->assertForbidden();
        $this->get(route('activitylog-ui.export'))->assertForbidden();
    }

    public function test_the_gate_can_allow_access()
    {
        Gate::define('viewActivityLogUi', fn ($user) => $user->email === 'admin@example.com');

        $this->actingAs($this->makeUser(['email' => 'admin@example.com']))
            ->get(route('activitylog-ui.index'))
            ->assertOk();
    }

    public function test_a_custom_gate_name_can_be_configured()
    {
        config(['activitylog-ui.gate' => 'audit']);
        Gate::define('audit', fn () => false);

        $this->actingAs($this->makeUser())->get(route('activitylog-ui.index'))->assertForbidden();
    }
}
