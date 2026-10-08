<?php

namespace Nsd7\LaravelActivitylogUi\Tests\Feature;

use Nsd7\LaravelActivitylogUi\Tests\TestCase;

class ExportFormatsTest extends TestCase
{
    protected function download(array $query = [])
    {
        return $this->actingAs($this->makeUser())->get(route('activitylog-ui.export', $query));
    }

    public function test_it_defaults_to_csv()
    {
        $this->logActivity(['description' => 'csv row']);

        $this->download()
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_it_exports_json()
    {
        $this->logActivity(['description' => 'json row', 'event' => 'created']);

        $response = $this->download(['format' => 'json'])
            ->assertOk()
            ->assertHeader('content-type', 'application/json; charset=UTF-8');

        $payload = json_decode($response->streamedContent(), true);

        $this->assertIsArray($payload);
        $this->assertSame('json row', $payload[0]['description']);
        $this->assertSame('created', $payload[0]['event']);
    }

    public function test_xlsx_falls_back_to_csv_when_the_excel_package_is_missing()
    {
        // maatwebsite/excel is not installed in the test suite.
        $this->logActivity(['description' => 'xlsx row']);

        $this->download(['format' => 'xlsx'])
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_pdf_falls_back_to_json_when_the_dompdf_package_is_missing()
    {
        // barryvdh/laravel-dompdf is not installed in the test suite.
        $this->logActivity(['description' => 'pdf row']);

        $this->download(['format' => 'pdf'])
            ->assertOk()
            ->assertHeader('content-type', 'application/json; charset=UTF-8');
    }

    public function test_unknown_formats_fall_back_to_csv()
    {
        $this->logActivity(['description' => 'row']);

        $this->download(['format' => 'xml'])
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_json_export_respects_filters_and_limit()
    {
        config(['activitylog-ui.export.limit' => 2]);

        foreach (range(1, 5) as $i) {
            $this->logActivity(['description' => "row {$i}", 'event' => 'created']);
        }
        $this->logActivity(['description' => 'other', 'event' => 'deleted']);

        $response = $this->download(['format' => 'json', 'event' => 'created']);
        $payload = json_decode($response->streamedContent(), true);

        $this->assertCount(2, $payload);
        $this->assertSame('row 5', $payload[0]['description']);
    }
}
