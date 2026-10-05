<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Nsd7\LaravelActivitylogUi\Tests\Fixtures\Post;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class ActivityLogShowTest extends TestCase
{
    public function test_it_shows_a_single_activity_with_its_changes()
    {
        $activity = $this->logActivity([
            'description' => 'Post updated',
            'event' => 'updated',
            'subject_type' => Post::class,
            'subject_id' => 7,
            'properties' => [
                'attributes' => ['title' => 'After'],
                'old' => ['title' => 'Before'],
                'ip' => '127.0.0.1',
            ],
        ]);

        $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.show', $activity->id))
            ->assertOk()
            ->assertViewIs('activitylog-ui::show')
            ->assertSee('Activity #' . $activity->id)
            ->assertSee('Post updated')
            ->assertSee('Before')
            ->assertSee('After')
            ->assertSee('Custom properties')
            ->assertSee('127.0.0.1');
    }

    public function test_it_shows_the_history_of_the_same_subject()
    {
        $this->logActivity(['description' => 'created it', 'subject_type' => Post::class, 'subject_id' => 7]);
        $this->logActivity(['description' => 'other record', 'subject_type' => Post::class, 'subject_id' => 8]);
        $latest = $this->logActivity(['description' => 'updated it', 'event' => 'updated', 'subject_type' => Post::class, 'subject_id' => 7]);

        $response = $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.show', $latest->id))
            ->assertOk()
            ->assertSee('History of this record');

        $this->assertCount(2, $response->viewData('history'));
    }

    public function test_it_returns_404_for_unknown_activities()
    {
        $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.show', 12345))
            ->assertNotFound();
    }
}
