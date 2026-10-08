<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class LiveCountTest extends TestCase
{
    public function test_the_count_endpoint_is_disabled_by_default()
    {
        $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.count'))
            ->assertNotFound();
    }

    public function test_the_index_renders_with_live_counts_enabled()
    {
        config(['activitylog-ui.features.live_counts' => true]);
        $this->logActivity(['event' => 'created']);

        $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.index'))
            ->assertOk()
            ->assertSee('x-text="new Intl.NumberFormat().format(total)"', false);
    }

    public function test_it_returns_the_filtered_total_when_enabled()
    {
        config(['activitylog-ui.features.live_counts' => true]);

        $this->logActivity(['event' => 'created']);
        $this->logActivity(['event' => 'created']);
        $this->logActivity(['event' => 'deleted']);

        $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.count'))
            ->assertOk()
            ->assertExactJson(['total' => 3]);

        $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.count', ['event' => 'created']))
            ->assertOk()
            ->assertExactJson(['total' => 2]);
    }
}
