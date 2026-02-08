<?php

namespace Rhaima\VoltPanel\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfExporter
{
    public function export(
        string $resourceClass,
        Collection $records,
        array $columns,
        array $headers
    ): string {
        $filename = $this->generateFilename($resourceClass);
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // Generate HTML table
        $html = $this->generateHtml($resourceClass, $records, $columns, $headers);

        // Configure Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Save PDF
        file_put_contents($filepath, $dompdf->output());

        return $filename;
    }

    protected function generateHtml(
        string $resourceClass,
        Collection $records,
        array $columns,
        array $headers
    ): string {
        $className = class_basename($resourceClass);
        $title = Str::title(Str::replace('Resource', '', $className));
        $date = now()->format('F j, Y \a\t g:i A');

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . $title . ' Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #4F46E5;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .meta {
            color: #6B7280;
            font-size: 12px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th {
            background-color: #4F46E5;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #E5E7EB;
        }
        tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #9CA3AF;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <h1>' . $title . ' Export</h1>
    <div class="meta">Generated on ' . $date . '</div>

    <table>
        <thead>
            <tr>';

        // Add headers
        foreach ($headers as $header) {
            $html .= '<th>' . htmlspecialchars($header) . '</th>';
        }

        $html .= '</tr>
        </thead>
        <tbody>';

        // Add data rows
        foreach ($records as $record) {
            $html .= '<tr>';

            foreach ($columns as $column) {
                $value = $resourceClass::formatExportValue($record, $column);
                $formattedValue = $this->formatValue($value);
                $html .= '<td>' . htmlspecialchars($formattedValue) . '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</tbody>
    </table>

    <div class="footer">
        Page {PAGE_NUM} of {PAGE_COUNT}
    </div>
</body>
</html>';

        return $html;
    }

    protected function formatValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }

    protected function generateFilename(string $resourceClass): string
    {
        $className = class_basename($resourceClass);
        $slug = Str::slug($className);
        $timestamp = now()->format('Y-m-d_His');

        return "{$slug}_export_{$timestamp}.pdf";
    }
}
