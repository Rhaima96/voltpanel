<?php

namespace Rhaima\VoltPanel\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CsvExporter
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

        $file = fopen($filepath, 'w');

        // Write headers
        fputcsv($file, array_values($headers));

        // Write data
        foreach ($records as $record) {
            $row = [];

            foreach ($columns as $column) {
                $value = $resourceClass::formatExportValue($record, $column);

                // Format value for CSV
                $row[] = $this->formatValue($value);
            }

            fputcsv($file, $row);
        }

        fclose($file);

        return $filename;
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

        return "{$slug}_export_{$timestamp}.csv";
    }
}
