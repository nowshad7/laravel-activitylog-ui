<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Unit;

use Nsd7\LaravelActivitylogUi\Filters\ActivityLogFilters;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class ActivityLogFiltersTest extends TestCase
{
    public function test_it_only_keeps_known_non_empty_scalar_keys()
    {
        $filters = new ActivityLogFilters([
            'event' => ' created ',
            'search' => '',
            'model' => ['array', 'is', 'ignored'],
            'page' => '3',
            'unknown' => 'x',
        ]);

        $this->assertSame(['event' => 'created'], $filters->all());
        $this->assertSame(1, $filters->count());
        $this->assertTrue($filters->has('event'));
        $this->assertFalse($filters->has('page'));
    }

    public function test_it_validates_dates()
    {
        $filters = new ActivityLogFilters([
            'date_from' => '2024-02-30',
            'date_to' => '2024-02-29',
        ]);

        $this->assertNull($filters->get('date_from'));
        $this->assertSame('2024-02-29', $filters->get('date_to'));
    }

    public function test_get_returns_a_default()
    {
        $this->assertSame('fallback', (new ActivityLogFilters())->get('event', 'fallback'));
    }
}
