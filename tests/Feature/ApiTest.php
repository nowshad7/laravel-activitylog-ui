<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Nsd7\LaravelActivitylogUi\Tests\Fixtures\Post;
use Nsd7\LaravelActivitylogUi\Tests\Fixtures\User;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class ApiTest extends TestCase
{
    public function test_the_api_is_disabled_by_default()
    {
        $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.api.index'))
            ->assertNotFound();
    }

    public function test_it_lists_activities_as_json_when_enabled()
    {
        config(['activitylog-ui.features.api' => true]);

        $causer = User::create(['name' => 'Api Causer']);
        $this->logActivity(['description' => 'api row', 'event' => 'created', 'causer_type' => User::class, 'causer_id' => $causer->id]);

        $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.api.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'log_name', 'description', 'event', 'causer', 'created_at']],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                'links' => ['next', 'prev'],
            ])
            ->assertJsonPath('data.0.description', 'api row')
            ->assertJsonPath('data.0.causer', 'Api Causer')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_it_filters_via_the_api()
    {
        config(['activitylog-ui.features.api' => true]);

        $this->logActivity(['description' => 'keep', 'event' => 'created']);
        $this->logActivity(['description' => 'drop', 'event' => 'deleted']);

        $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.api.index', ['event' => 'created']))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.description', 'keep');
    }

    public function test_it_shows_a_single_activity_with_changes()
    {
        config(['activitylog-ui.features.api' => true]);

        $activity = $this->logActivity([
            'event' => 'updated',
            'subject_type' => Post::class,
            'subject_id' => 1,
            'properties' => ['attributes' => ['title' => 'New'], 'old' => ['title' => 'Old']],
        ]);

        $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.api.show', $activity->id))
            ->assertOk()
            ->assertJsonPath('data.id', $activity->id)
            ->assertJsonPath('data.changes.0.key', 'title')
            ->assertJsonPath('data.changes.0.old', 'Old')
            ->assertJsonPath('data.changes.0.new', 'New');
    }

    public function test_show_is_disabled_by_default()
    {
        $activity = $this->logActivity();

        $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.api.show', $activity->id))
            ->assertNotFound();
    }
}
