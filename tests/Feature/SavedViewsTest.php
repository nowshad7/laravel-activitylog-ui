<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Nsd7\LaravelActivitylogUi\Models\SavedView;
use Nsd7\LaravelActivitylogUi\Support\SavedViewRepository;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class SavedViewsTest extends TestCase
{
    public function test_it_is_disabled_until_the_table_exists()
    {
        $this->assertFalse(SavedViewRepository::enabled());

        // The index page still renders without the saved-views panel.
        $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.index'))
            ->assertOk();
    }

    public function test_storing_is_not_found_when_the_table_is_missing()
    {
        $this->actingAs($this->makeUser())
            ->post(route('activitylog-ui.saved-views.store'), ['name' => 'Mine'])
            ->assertNotFound();
    }

    public function test_a_user_can_save_and_recall_a_view()
    {
        $this->createSavedViewsTable();
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('activitylog-ui.saved-views.store'), [
                'name' => 'Deletions',
                'event' => 'deleted',
                'log_name' => 'billing',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount(config('activitylog-ui.saved_views.table'), 1);

        $view = SavedView::first();
        $this->assertSame('Deletions', $view->name);
        $this->assertSame(['log_name' => 'billing', 'event' => 'deleted'], $view->filters);

        $views = SavedViewRepository::forUser($user);
        $this->assertCount(1, $views);
    }

    public function test_views_are_scoped_to_the_owning_user()
    {
        $this->createSavedViewsTable();
        $owner = $this->makeUser(['email' => 'owner@example.com']);
        $other = $this->makeUser(['email' => 'other@example.com']);

        SavedViewRepository::create($owner, 'Owner view', ['event' => 'created']);

        $this->assertCount(1, SavedViewRepository::forUser($owner));
        $this->assertCount(0, SavedViewRepository::forUser($other));
    }

    public function test_a_user_can_delete_their_view()
    {
        $this->createSavedViewsTable();
        $user = $this->makeUser();
        $view = SavedViewRepository::create($user, 'Temp', ['event' => 'updated']);

        $this->actingAs($user)
            ->delete(route('activitylog-ui.saved-views.destroy', $view->getKey()))
            ->assertRedirect();

        $this->assertDatabaseCount(config('activitylog-ui.saved_views.table'), 0);
    }

    public function test_a_user_cannot_delete_another_users_view()
    {
        $this->createSavedViewsTable();
        $owner = $this->makeUser(['email' => 'owner@example.com']);
        $attacker = $this->makeUser(['email' => 'attacker@example.com']);
        $view = SavedViewRepository::create($owner, 'Private', ['event' => 'created']);

        $this->actingAs($attacker)
            ->delete(route('activitylog-ui.saved-views.destroy', $view->getKey()))
            ->assertRedirect();

        $this->assertDatabaseCount(config('activitylog-ui.saved_views.table'), 1);
    }

    public function test_the_index_renders_the_saved_views_panel_when_enabled()
    {
        $this->createSavedViewsTable();
        $user = $this->makeUser();
        SavedViewRepository::create($user, 'My deletions', ['event' => 'deleted']);

        $this->actingAs($user)
            ->get(route('activitylog-ui.index'))
            ->assertOk()
            ->assertSee('My deletions')
            ->assertSee(__('activitylog-ui::messages.saved_views.heading'));
    }

    public function test_saved_views_feature_can_be_disabled()
    {
        $this->createSavedViewsTable();
        config(['activitylog-ui.features.saved_views' => false]);

        $this->assertFalse(SavedViewRepository::enabled());

        $this->actingAs($this->makeUser())
            ->post(route('activitylog-ui.saved-views.store'), ['name' => 'Nope'])
            ->assertNotFound();
    }
}
