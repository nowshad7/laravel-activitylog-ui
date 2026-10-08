# Exports

Export the **currently filtered** result set from the Export menu. The format is chosen with the `format` query parameter on the export route.

| Format | Requires | Fallback |
|---|---|---|
| CSV | — | — |
| JSON | — | — |
| Excel (XLSX) | `maatwebsite/excel` | CSV |
| PDF | `barryvdh/laravel-dompdf` | JSON |

## Safety

- Every export is capped at `export.limit` rows (default 10,000).
- CSV/XLSX string cells beginning with `= + - @` (or tab/CR) are prefixed with `'` to neutralise spreadsheet **formula injection**.
- CSV and JSON are **streamed**, so large exports do not exhaust memory.

## Restricting formats

Limit what the UI offers with `export.formats`, e.g. `['csv', 'json']`. Disable exports entirely with `export.enabled => false`.
