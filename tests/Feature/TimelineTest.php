<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class TimelineTest extends TestCase
{
    public function test_it_renders_the_timeline_view()
    {
        $this->logActivity(['description' => 'timeline entry', 'event' => 'created']);

        $response = $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.index', ['view' => 'timeline']))
            ->assertOk()
            ->assertSee('timeline entry');

        $this->assertSame('timeline', $response->viewData('viewMode'));
    }

    public function test_an_unknown_view_mode_falls_back_to_table()
    {
        $response = $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.index', ['view' => 'bogus']))
            ->assertOk();

        $this->assertSame('table', $response->viewData('viewMode'));
    }

    public function test_timeline_falls_back_to_table_when_disabled()
    {
        config(['activitylog-ui.features.timeline' => false]);

        $response = $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.index', ['view' => 'timeline']))
            ->assertOk();

        $this->assertSame('table', $response->viewData('viewMode'));
    }
}
