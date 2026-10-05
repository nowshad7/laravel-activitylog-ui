<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Unit;

use Nsd7\LaravelActivitylogUi\Support\ActivityPresenter;
use Nsd7\LaravelActivitylogUi\Tests\Fixtures\User;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class ActivityPresenterTest extends TestCase
{
    public function test_changes_are_classified()
    {
        $activity = $this->logActivity([
            'properties' => [
                'attributes' => ['title' => 'New', 'status' => 'draft', 'slug' => 'added'],
                'old' => ['title' => 'Old', 'status' => 'draft', 'legacy' => 'gone'],
            ],
        ]);

        $changes = collect(ActivityPresenter::changes($activity))->keyBy('key');

        $this->assertSame('changed', $changes['title']['status']);
        $this->assertSame('Old', $changes['title']['old']);
        $this->assertSame('New', $changes['title']['new']);
        $this->assertSame('unchanged', $changes['status']['status']);
        $this->assertSame('added', $changes['slug']['status']);
        $this->assertSame('removed', $changes['legacy']['status']);
    }

    public function test_changes_are_empty_without_attribute_properties()
    {
        $activity = $this->logActivity(['properties' => ['ip' => '127.0.0.1']]);

        $this->assertSame([], ActivityPresenter::changes($activity));
        $this->assertSame(['ip' => '127.0.0.1'], ActivityPresenter::customProperties($activity));
    }

    public function test_causer_name_uses_the_configured_attributes()
    {
        $user = User::create(['name' => '', 'email' => 'no-name@example.com']);
        $activity = $this->logActivity(['causer_type' => User::class, 'causer_id' => $user->id]);

        $this->assertSame('no-name@example.com', ActivityPresenter::causerName($activity));

        config(['activitylog-ui.causer_display_attributes' => ['nickname']]);
        $activity->unsetRelation('causer');

        $this->assertSame('User #' . $user->id, ActivityPresenter::causerName($activity));
    }

    public function test_causer_name_defaults_to_system()
    {
        $this->assertSame('System', ActivityPresenter::causerName($this->logActivity()));
    }

    public function test_values_are_formatted_for_display()
    {
        $this->assertSame('null', ActivityPresenter::formatValue(null));
        $this->assertSame('true', ActivityPresenter::formatValue(true));
        $this->assertSame('false', ActivityPresenter::formatValue(false));
        $this->assertSame('12', ActivityPresenter::formatValue(12));
        $this->assertSame('{"a":"b/c"}', ActivityPresenter::formatValue(['a' => 'b/c']));
    }

    public function test_event_classes_have_a_fallback()
    {
        $this->assertStringContainsString('emerald', ActivityPresenter::eventClasses('created'));
        $this->assertStringContainsString('slate', ActivityPresenter::eventClasses('custom'));
        $this->assertStringContainsString('slate', ActivityPresenter::eventClasses(null));
    }
}
