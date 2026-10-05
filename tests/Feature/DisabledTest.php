<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class DisabledTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('activitylog-ui.enabled', false);
    }

    public function test_no_routes_are_registered_when_disabled()
    {
        $this->assertFalse(Route::has('activitylog-ui.index'));
        $this->actingAs($this->makeUser())->get('/admin/activity-log')->assertNotFound();
    }
}
