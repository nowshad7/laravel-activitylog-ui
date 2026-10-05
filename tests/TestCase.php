<?php

namespace Nsd7\LaravelActivitylogUi\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Nsd7\LaravelActivitylogUi\LaravelActivitylogUiServiceProvider;
use Nsd7\LaravelActivitylogUi\Tests\Fixtures\User;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Activitylog\Models\Activity;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ActivitylogServiceProvider::class,
            LaravelActivitylogUiServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('auth.providers.users.model', User::class);
    }

    protected function defineDatabaseMigrations()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->string('event')->nullable();
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->timestamps();
            $table->index('log_name');
        });
    }

    protected function defineRoutes($router)
    {
        // The "auth" middleware redirects guests to a route named "login".
        Route::get('/login', fn () => 'login')->name('login');
    }

    protected function makeUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Jane Admin',
            'email' => 'jane@example.com',
        ], $attributes));
    }

    protected function logActivity(array $attributes = []): Activity
    {
        $activity = new Activity();
        $activity->forceFill(array_merge([
            'log_name' => 'default',
            'description' => 'created',
            'event' => 'created',
            'properties' => [],
        ], $attributes));
        $activity->save();

        return $activity;
    }
}
