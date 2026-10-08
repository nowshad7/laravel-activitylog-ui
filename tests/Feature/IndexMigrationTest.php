<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class IndexMigrationTest extends TestCase
{
    public function test_the_performance_index_migration_runs_and_rolls_back()
    {
        $migration = require __DIR__ . '/../../database/migrations/add_indexes_to_activity_log_table.php.stub';

        $migration->up();

        $indexes = array_map(
            fn ($index) => $index['name'],
            Schema::getConnection()->getSchemaBuilder()->getIndexes('activity_log')
        );

        $this->assertContains('alu_event_index', $indexes);
        $this->assertContains('alu_created_at_index', $indexes);
        $this->assertContains('alu_log_name_created_at_index', $indexes);

        // Idempotent: running up() again does not throw.
        $migration->up();

        $migration->down();

        $after = array_map(
            fn ($index) => $index['name'],
            Schema::getConnection()->getSchemaBuilder()->getIndexes('activity_log')
        );

        $this->assertNotContains('alu_event_index', $after);
    }
}
