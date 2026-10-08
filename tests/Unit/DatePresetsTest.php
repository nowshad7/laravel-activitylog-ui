<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Unit;

use Illuminate\Support\Carbon;
use Nsd7\LaravelActivitylogUi\Filters\ActivityLogFilters;
use Nsd7\LaravelActivitylogUi\Support\DatePresets;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class DatePresetsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2024-06-15 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_builds_the_expected_presets()
    {
        $presets = DatePresets::forFilters(new ActivityLogFilters());

        $keys = array_column($presets, 'key');
        $this->assertSame(['today', 'yesterday', '7d', '30d', 'this_month'], $keys);

        $today = collect($presets)->firstWhere('key', 'today');
        $this->assertSame('2024-06-15', $today['from']);
        $this->assertSame('2024-06-15', $today['to']);

        $last7 = collect($presets)->firstWhere('key', '7d');
        $this->assertSame('2024-06-09', $last7['from']);
        $this->assertSame('2024-06-15', $last7['to']);

        $month = collect($presets)->firstWhere('key', 'this_month');
        $this->assertSame('2024-06-01', $month['from']);
        $this->assertSame('2024-06-30', $month['to']);
    }

    public function test_it_marks_the_matching_preset_as_active()
    {
        $filters = new ActivityLogFilters(['date_from' => '2024-06-15', 'date_to' => '2024-06-15']);

        $active = collect(DatePresets::forFilters($filters))->firstWhere('active', true);

        $this->assertSame('today', $active['key']);
    }
}
