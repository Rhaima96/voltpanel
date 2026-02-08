<?php

namespace Rhaima\VoltPanel\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell;

class XlsxExporter
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

        // Create writer
        $writer = new Writer();
        $writer->openToFile($filepath);

        // Write headers
        $headerCells = array_map(fn($header) => Cell::fromValue($header), $headers);
        $headerRow = new Row($headerCells);
        $writer->addRow($headerRow);

        // Write data rows
        foreach ($records as $record) {
            $rowData = [];
            foreach ($columns as $column) {
                $value = $resourceClass::formatExportValue($record, $column);
                $rowData[] = Cell::fromValue($this->formatValue($value));
            }
            $dataRow = new Row($rowData);
            $writer->addRow($dataRow);
        }

        $writer->close();

        return $filename;
    }

    protected function formatValue(mixed $value): mixed
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

        return $value;
    }

    protected function generateFilename(string $resourceClass): string
    {
        $className = class_basename($resourceClass);
        $slug = Str::slug($className);
        $timestamp = now()->format('Y-m-d_His');

        return "{$slug}_export_{$timestamp}.xlsx";
    }
}
