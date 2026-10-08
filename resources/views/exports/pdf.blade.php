<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 9px; color: #1e293b; }
        h1 { font-size: 14px; margin: 0 0 2px; }
        .meta { color: #64748b; font-size: 9px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; text-align: left; padding: 4px 6px; border-bottom: 1px solid #cbd5e1; font-size: 8px; text-transform: uppercase; }
        td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; vertical-align: top; word-break: break-word; }
        tr:nth-child(even) td { background: #f8fafc; }
    </style>
</head>
<body>
    <h1>Activity Log</h1>
    <p class="meta">Generated {{ $generatedAt }} — {{ $rows->count() }} rows</p>

    <table>
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ \Illuminate\Support\Str::limit((string) $cell, 120) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
