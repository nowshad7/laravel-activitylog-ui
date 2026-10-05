<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Nsd7\LaravelActivitylogUi\Tests\Fixtures\User;
use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class ActivityLogExportTest extends TestCase
{
    protected function export(array $query = []): string
    {
        $response = $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.export', $query))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        return $response->streamedContent();
    }

    public function test_it_exports_the_filtered_activities_as_csv()
    {
        $causer = User::create(['name' => 'Exporter']);
        $this->logActivity(['description' => 'keep me', 'event' => 'created', 'causer_type' => User::class, 'causer_id' => $causer->id]);
        $this->logActivity(['description' => 'drop me', 'event' => 'deleted']);

        $csv = $this->export(['event' => 'created']);

        $this->assertStringStartsWith('id,log_name,description,event', $csv);
        $this->assertStringContainsString('keep me', $csv);
        $this->assertStringContainsString('Exporter', $csv);
        $this->assertStringNotContainsString('drop me', $csv);
    }

    public function test_it_respects_the_export_limit()
    {
        config(['activitylog-ui.export.limit' => 2]);

        foreach (range(1, 5) as $i) {
            $this->logActivity(['description' => "row {$i}"]);
        }

        $lines = array_filter(explode("\n", trim($this->export())));

        $this->assertCount(3, $lines); // header + 2 rows
        $this->assertStringContainsString('row 5', $this->export());
    }

    public function test_it_neutralises_spreadsheet_formulas()
    {
        $this->logActivity(['description' => '=HYPERLINK("http://evil.test")']);

        $this->assertStringContainsString("'=HYPERLINK", $this->export());
    }

    public function test_export_can_be_disabled()
    {
        config(['activitylog-ui.export.enabled' => false]);

        $this->actingAs($this->makeUser())
            ->get(route('activitylog-ui.export'))
            ->assertNotFound();
    }
}
