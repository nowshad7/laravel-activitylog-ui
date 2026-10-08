<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class IndexMigrationTest extends TestCase
{
    public function test_the_performance_index_migration_runs_and_rolls_back()
    {
        $migration = require __DIR__ . '/../../database/migrations/add_indexes_to_activity_log_table.php.stub';

        // Should not throw, and should be idempotent.
        $migration->up();
        $migration->up();

        $builder = Schema::getConnection()->getSchemaBuilder();

        // Schema::getIndexes() only exists on Laravel 11+. On older versions we
        // can still assert the migration ran and rolls back cleanly.
        if (method_exists($builder, 'getIndexes')) {
            $indexes = array_map(fn ($index) => $index['name'], $builder->getIndexes('activity_log'));

            $this->assertContains('alu_event_index', $indexes);
            $this->assertContains('alu_created_at_index', $indexes);
            $this->assertContains('alu_log_name_created_at_index', $indexes);
        }

        $migration->down();

        if (method_exists($builder, 'getIndexes')) {
            $after = array_map(fn ($index) => $index['name'], $builder->getIndexes('activity_log'));
            $this->assertNotContains('alu_event_index', $after);
        } else {
            $this->assertTrue(true);
        }
    }
}
