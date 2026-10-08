<?php

namespace Nsd7\LaravelActivitylogUi\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Turns a filtered activity query into a downloadable export.
 *
 * CSV and JSON stream out of the box. XLSX uses maatwebsite/excel when it is
 * installed and otherwise falls back to CSV; PDF uses barryvdh/laravel-dompdf
 * when installed and otherwise falls back to JSON.
 */
class ActivityExporter
{
    public const COLUMNS = [
        'id', 'log_name', 'description', 'event', 'subject_type', 'subject_id',
        'causer_type', 'causer_id', 'causer', 'properties', 'batch_uuid', 'created_at',
    ];

    protected Builder $query;

    protected int $limit;

    protected string $dateFormat;

    public function __construct(Builder $query, int $limit, string $dateFormat)
    {
        $this->query = $query;
        $this->limit = max(1, $limit);
        $this->dateFormat = $dateFormat;
    }

    /**
     * Resolve the requested format to one that can actually be produced here,
     * applying the documented fallbacks.
     */
    public function resolveFormat(string $format): string
    {
        $format = strtolower($format);

        if ($format === 'xlsx' && ! class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            $format = 'csv';
        }

        if ($format === 'pdf' && ! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $format = 'json';
        }

        return in_array($format, ['csv', 'json', 'xlsx', 'pdf'], true) ? $format : 'csv';
    }

    public function download(string $format): Response
    {
        $format = $this->resolveFormat($format);
        $filename = 'activity-log-' . now()->format('Y-m-d-His');

        return match ($format) {
            'json' => $this->json($filename),
            'xlsx' => $this->xlsx($filename),
            'pdf' => $this->pdf($filename),
            default => $this->csv($filename),
        };
    }

    protected function rows(): \Illuminate\Support\LazyCollection
    {
        return $this->query->with('causer')->lazyByIdDesc(500)->take($this->limit);
    }

    /**
     * @return array<int, mixed>
     */
    public function toRow(Model $activity): array
    {
        return [
            $activity->id,
            $activity->log_name,
            $activity->description,
            $activity->event,
            $activity->subject_type,
            $activity->subject_id,
            $activity->causer_type,
            $activity->causer_id,
            ActivityPresenter::causerName($activity),
            json_encode($activity->properties, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            $activity->batch_uuid,
            optional($activity->created_at)->format($this->dateFormat),
        ];
    }

    protected function csv(string $filename): StreamedResponse
    {
        $rows = $this->rows();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, self::COLUMNS);

            foreach ($rows as $activity) {
                fputcsv($handle, array_map([self::class, 'csvSafe'], $this->toRow($activity)));
            }

            fclose($handle);
        }, $filename . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function json(string $filename): StreamedResponse
    {
        $rows = $this->rows();
        $columns = self::COLUMNS;

        return response()->streamDownload(function () use ($rows, $columns) {
            echo '[';
            $first = true;

            foreach ($rows as $activity) {
                echo $first ? '' : ',';
                $first = false;
                echo json_encode(
                    array_combine($columns, $this->toRow($activity)),
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                );
            }

            echo ']';
        }, $filename . '.json', ['Content-Type' => 'application/json; charset=UTF-8']);
    }

    protected function xlsx(string $filename): Response
    {
        $columns = self::COLUMNS;
        $rows = collect();

        foreach ($this->rows() as $activity) {
            $rows->push(array_map([self::class, 'csvSafe'], $this->toRow($activity)));
        }

        $export = new class($columns, $rows) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings {
            public function __construct(protected array $headings, protected \Illuminate\Support\Collection $rows)
            {
            }

            public function collection()
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return $this->headings;
            }
        };

        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename . '.xlsx');
    }

    protected function pdf(string $filename): Response
    {
        $columns = self::COLUMNS;
        $rows = collect();

        foreach ($this->rows() as $activity) {
            $rows->push($this->toRow($activity));
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('activitylog-ui::exports.pdf', [
            'columns' => $columns,
            'rows' => $rows,
            'generatedAt' => now()->format($this->dateFormat),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Prevent CSV/formula injection when the export is opened in a spreadsheet.
     */
    public static function csvSafe($value)
    {
        if (is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $value;
        }

        return $value;
    }
}
