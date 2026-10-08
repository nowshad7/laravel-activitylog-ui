<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Illuminate\Support\Carbon;
use Nsd7\LaravelActivitylogUi\Tests\Fixtures\Post;
use Nsd7\LaravelActivitylogUi\Tests\Fixtures\User;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class AnalyticsTest extends TestCase
{
    public function test_the_analytics_page_renders()
    {
        $this->logActivity(['event' => 'created']);

        $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.analytics'))
            ->assertOk()
            ->assertViewIs('activitylog-ui::analytics');
    }

    public function test_the_data_endpoint_returns_aggregates()
    {
        $this->logActivity(['event' => 'created', 'log_name' => 'default']);
        $this->logActivity(['event' => 'created', 'log_name' => 'default']);
        $this->logActivity(['event' => 'updated', 'log_name' => 'billing']);

        $response = $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.analytics.data'))
            ->assertOk()
            ->assertJsonStructure([
                'totals' => ['total', 'created', 'updated', 'deleted'],
                'per_day', 'per_event', 'per_log_name', 'top_causers', 'top_subjects',
            ]);

        $response->assertJsonPath('totals.total', 3);
        $response->assertJsonPath('totals.created', 2);
        $response->assertJsonPath('per_event.created', 2);
        $response->assertJsonPath('per_log_name.default', 2);
    }

    public function test_the_data_endpoint_respects_filters()
    {
        $this->logActivity(['event' => 'created', 'log_name' => 'a']);
        $this->logActivity(['event' => 'deleted', 'log_name' => 'b']);

        $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.analytics.data', ['log_name' => 'a']))
            ->assertOk()
            ->assertJsonPath('totals.total', 1)
            ->assertJsonPath('totals.created', 1);
    }

    public function test_top_causers_resolve_display_names()
    {
        $alice = User::create(['name' => 'Alice Causer']);
        $this->logActivity(['causer_type' => User::class, 'causer_id' => $alice->id]);

        $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.analytics.data'))
            ->assertOk()
            ->assertJsonPath('top_causers.0.label', 'Alice Causer');
    }

    public function test_top_subjects_use_basenames()
    {
        $this->logActivity(['subject_type' => Post::class, 'subject_id' => 1]);

        $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.analytics.data'))
            ->assertOk()
            ->assertJsonPath('top_subjects.0.label', 'Post');
    }

    public function test_per_day_fills_gaps_within_the_range()
    {
        Carbon::setTestNow('2024-03-10 12:00:00');
        $this->logActivity(['created_at' => Carbon::parse('2024-03-08 09:00:00')]);

        $data = $this->actingAs($this->makeUser())
            ->getJson(route('activitylog-ui.analytics.data', ['date_from' => '2024-03-07', 'date_to' => '2024-03-09']))
            ->assertOk()
            ->json('per_day');

        $this->assertSame(['2024-03-07' => 0, '2024-03-08' => 1, '2024-03-09' => 0], $data);

        Carbon::setTestNow();
    }

    public function test_analytics_can_be_disabled()
    {
        config(['activitylog-ui.features.analytics' => false]);

        $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.analytics'))
            ->assertNotFound();

        $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.analytics.data'))
            ->assertNotFound();
    }
}
