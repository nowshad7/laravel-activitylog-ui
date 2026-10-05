<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class RouteConfigurationTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('activitylog-ui.route.prefix', 'audit/logs');
        $app['config']->set('activitylog-ui.route.middleware', ['web']);
    }

    public function test_the_route_prefix_and_middleware_are_configurable()
    {
        $this->assertSame(url('audit/logs'), route('activitylog-ui.index'));

        // Guest access works because "auth" was removed from the middleware.
        $this->get('/audit/logs')->assertOk();
        $this->get('/admin/activity-log')->assertNotFound();
    }

    public function test_config_and_views_are_publishable()
    {
        $this->artisan('vendor:publish', ['--tag' => 'activitylog-ui-config', '--force' => true])->assertExitCode(0);

        $this->assertFileExists(config_path('activitylog-ui.php'));

        @unlink(config_path('activitylog-ui.php'));
    }

    public function test_routes_are_named()
    {
        $this->assertTrue(Route::has('activitylog-ui.index'));
        $this->assertTrue(Route::has('activitylog-ui.show'));
        $this->assertTrue(Route::has('activitylog-ui.export'));
    }
}
