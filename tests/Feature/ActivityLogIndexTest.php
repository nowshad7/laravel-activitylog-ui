<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Illuminate\Support\Carbon;
use Nsd7\LaravelActivitylogUi\Tests\Fixtures\Post;
use Nsd7\LaravelActivitylogUi\Tests\Fixtures\PostComment;
use Nsd7\LaravelActivitylogUi\Tests\Fixtures\User;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class ActivityLogIndexTest extends TestCase
{
    protected function index(array $query = [])
    {
        return $this->actingAs($this->makeUser())->get(route('activitylog-ui.index', $query));
    }

    public function test_guests_are_redirected_to_login()
    {
        $this->get('/admin/activity-log')->assertRedirect('/login');
    }

    public function test_it_lists_activities_for_authenticated_users()
    {
        $this->logActivity(['description' => 'First thing happened']);
        $this->logActivity(['description' => 'Second thing happened']);

        $this->index()
            ->assertOk()
            ->assertViewIs('activitylog-ui::index')
            ->assertSee('First thing happened')
            ->assertSee('Second thing happened')
            ->assertSeeInOrder(['Second thing happened', 'First thing happened']);
    }

    public function test_it_shows_an_empty_state()
    {
        $this->index()->assertOk()->assertSee('No activities found');
    }

    public function test_it_displays_the_causer_name()
    {
        $causer = User::create(['name' => 'Alice Causer']);
        $this->logActivity(['causer_type' => User::class, 'causer_id' => $causer->id]);

        $this->index()->assertOk()->assertSee('Alice Causer');
    }

    public function test_it_falls_back_when_the_causer_was_deleted()
    {
        $this->logActivity(['causer_type' => User::class, 'causer_id' => 999]);

        $this->index()->assertOk()->assertSee('User #999');
    }

    public function test_activities_without_a_causer_are_attributed_to_system()
    {
        $this->logActivity(['description' => 'cron ran']);

        $this->index()->assertOk()->assertSee('System');
    }

    public function test_it_filters_by_event()
    {
        $this->logActivity(['description' => 'was created', 'event' => 'created']);
        $this->logActivity(['description' => 'was deleted', 'event' => 'deleted']);

        $this->index(['event' => 'deleted'])
            ->assertSee('was deleted')
            ->assertDontSee('was created');
    }

    public function test_it_filters_by_fully_qualified_model_without_matching_similar_names()
    {
        $this->logActivity(['description' => 'post activity', 'subject_type' => Post::class, 'subject_id' => 1]);
        $this->logActivity(['description' => 'comment activity', 'subject_type' => PostComment::class, 'subject_id' => 1]);

        $this->index(['model' => Post::class])
            ->assertSee('post activity')
            ->assertDontSee('comment activity');
    }

    public function test_it_still_accepts_a_model_basename_for_backwards_compatibility()
    {
        $this->logActivity(['description' => 'post activity', 'subject_type' => Post::class, 'subject_id' => 1]);
        $this->logActivity(['description' => 'comment activity', 'subject_type' => PostComment::class, 'subject_id' => 1]);

        $this->index(['model' => 'Post'])
            ->assertSee('post activity')
            ->assertDontSee('comment activity');
    }

    public function test_it_filters_by_subject_id()
    {
        $this->logActivity(['description' => 'subject one', 'subject_type' => Post::class, 'subject_id' => 1]);
        $this->logActivity(['description' => 'subject two', 'subject_type' => Post::class, 'subject_id' => 2]);

        $this->index(['subject_id' => 2])
            ->assertSee('subject two')
            ->assertDontSee('subject one');
    }

    public function test_it_filters_by_causer()
    {
        $alice = User::create(['name' => 'Alice']);
        $bob = User::create(['name' => 'Bob']);
        $this->logActivity(['description' => 'by alice', 'causer_type' => User::class, 'causer_id' => $alice->id]);
        $this->logActivity(['description' => 'by bob', 'causer_type' => User::class, 'causer_id' => $bob->id]);

        $this->index(['causer_type' => User::class, 'causer_id' => $bob->id])
            ->assertSee('by bob')
            ->assertDontSee('by alice');
    }

    public function test_it_filters_by_log_name()
    {
        $this->logActivity(['description' => 'billing entry', 'log_name' => 'billing']);
        $this->logActivity(['description' => 'default entry', 'log_name' => 'default']);

        $this->index(['log_name' => 'billing'])
            ->assertSee('billing entry')
            ->assertDontSee('default entry');
    }

    public function test_it_filters_by_batch()
    {
        $this->logActivity(['description' => 'in batch', 'batch_uuid' => '7b5f3c1e-0000-4000-8000-000000000001']);
        $this->logActivity(['description' => 'outside batch']);

        $this->index(['batch_uuid' => '7b5f3c1e-0000-4000-8000-000000000001'])
            ->assertSee('in batch')
            ->assertDontSee('outside batch');
    }

    public function test_search_works_on_databases_without_fulltext_support()
    {
        $this->logActivity(['description' => 'Invoice paid']);
        $this->logActivity(['description' => 'Password reset']);

        $this->index(['search' => 'invoice'])
            ->assertOk()
            ->assertSee('Invoice paid')
            ->assertDontSee('Password reset');
    }

    public function test_it_filters_by_date_range()
    {
        $this->logActivity(['description' => 'too old', 'created_at' => Carbon::parse('2024-01-01 12:00:00')]);
        $this->logActivity(['description' => 'in range start', 'created_at' => Carbon::parse('2024-02-01 00:00:00')]);
        $this->logActivity(['description' => 'in range end', 'created_at' => Carbon::parse('2024-02-10 23:59:59')]);
        $this->logActivity(['description' => 'too new', 'created_at' => Carbon::parse('2024-02-11 00:00:01')]);

        $this->index(['date_from' => '2024-02-01', 'date_to' => '2024-02-10'])
            ->assertSee('in range start')
            ->assertSee('in range end')
            ->assertDontSee('too old')
            ->assertDontSee('too new');
    }

    public function test_invalid_dates_are_ignored_instead_of_erroring()
    {
        $this->logActivity(['description' => 'still visible']);

        $this->index(['date_from' => 'not-a-date', 'date_to' => '2024-13-45'])
            ->assertOk()
            ->assertSee('still visible')
            ->assertSee('data-active-filters="0"', false);
    }

    public function test_pagination_links_keep_the_active_filters()
    {
        for ($i = 0; $i < 20; $i++) {
            $this->logActivity(['event' => 'created']);
        }

        $response = $this->index(['event' => 'created', 'per_page' => 10])->assertOk();

        $this->assertStringContainsString('event=created', $response->viewData('logs')->nextPageUrl());
        $this->assertStringContainsString('per_page=10', $response->viewData('logs')->nextPageUrl());
    }

    public function test_per_page_is_restricted_to_the_configured_options()
    {
        for ($i = 0; $i < 30; $i++) {
            $this->logActivity();
        }

        $this->assertSame(25, $this->index(['per_page' => 25])->viewData('logs')->perPage());
        $this->assertSame(15, $this->index(['per_page' => 100000])->viewData('logs')->perPage());
    }

    public function test_it_shows_event_statistics_for_the_filtered_result()
    {
        $this->logActivity(['event' => 'created', 'log_name' => 'a']);
        $this->logActivity(['event' => 'created', 'log_name' => 'a']);
        $this->logActivity(['event' => 'updated', 'log_name' => 'a']);
        $this->logActivity(['event' => 'deleted', 'log_name' => 'b']);

        $stats = $this->index(['log_name' => 'a'])->viewData('stats');

        $this->assertSame(['total' => 3, 'created' => 2, 'updated' => 1, 'deleted' => 0], $stats);
    }

    public function test_statistics_can_be_disabled()
    {
        config(['activitylog-ui.show_stats' => false]);

        $this->index()->assertOk()->assertViewHas('stats', null);
    }

    public function test_filter_dropdowns_are_populated_without_null_entries()
    {
        $this->logActivity(['subject_type' => Post::class, 'subject_id' => 1, 'event' => 'updated', 'log_name' => 'blog']);
        $this->logActivity(['event' => null, 'log_name' => null]);

        $response = $this->index();

        $this->assertSame([Post::class => 'Post'], $response->viewData('models'));
        $this->assertSame(['updated'], $response->viewData('events'));
        $this->assertSame(['blog'], $response->viewData('logNames'));
    }

    public function test_attribute_changes_are_rendered_as_a_diff()
    {
        $this->logActivity([
            'event' => 'updated',
            'properties' => [
                'attributes' => ['title' => 'New title'],
                'old' => ['title' => 'Old title'],
            ],
        ]);

        $this->index()
            ->assertSee('1 field')
            ->assertSee('Old title')
            ->assertSee('New title');
    }

    public function test_output_is_escaped()
    {
        $this->logActivity(['description' => '<script>alert(1)</script>']);

        $this->index()
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }
}
