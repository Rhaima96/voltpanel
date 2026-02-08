<?php

namespace Rhaima\VoltPanel\Export;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class Exporter
{
    protected static ?string $model = null;
    protected array $columns = [];
    protected ?Closure $modifyQuery = null;
    protected ?Closure $beforeExport = null;
    protected ?Closure $afterExport = null;
    protected string $format = 'csv';
    protected ?string $fileName = null;

    public static function getModel(): string
    {
        return static::$model;
    }

    public static function make(): static
    {
        return new static();
    }

    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function modifyQuery(Closure $callback): static
    {
        $this->modifyQuery = $callback;

        return $this;
    }

    public function beforeExport(Closure $callback): static
    {
        $this->beforeExport = $callback;

        return $this;
    }

    public function afterExport(Closure $callback): static
    {
        $this->afterExport = $callback;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function fileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function export(): string
    {
        $model = static::getModel();
        $query = $model::query();

        if ($this->modifyQuery) {
            call_user_func($this->modifyQuery, $query);
        }

        $records = $query->get();

        if ($this->beforeExport) {
            call_user_func($this->beforeExport, $records);
        }

        $data = $this->mapRecords($records);

        $filePath = $this->generateFile($data);

        if ($this->afterExport) {
            call_user_func($this->afterExport, $filePath);
        }

        return $filePath;
    }

    protected function mapRecords(Collection $records): array
    {
        $headers = array_keys($this->columns);
        $data = [$headers];

        foreach ($records as $record) {
            $row = [];
            foreach ($this->columns as $column => $accessor) {
                if (is_callable($accessor)) {
                    $row[] = call_user_func($accessor, $record);
                } else {
                    $row[] = data_get($record, $accessor);
                }
            }
            $data[] = $row;
        }

        return $data;
    }

    protected function generateFile(array $data): string
    {
        $fileName = $this->fileName ?? 'export_' . date('Y-m-d_H-i-s');

        switch ($this->format) {
            case 'csv':
                return $this->generateCsv($data, $fileName);
            case 'xlsx':
                return $this->generateXlsx($data, $fileName);
            default:
                return $this->generateCsv($data, $fileName);
        }
    }

    protected function generateCsv(array $data, string $fileName): string
    {
        $filePath = storage_path("app/exports/{$fileName}.csv");

        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $file = fopen($filePath, 'w');

        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        fclose($file);

        return $filePath;
    }

    protected function generateXlsx(array $data, string $fileName): string
    {
        $filePath = storage_path("app/exports/{$fileName}.xlsx");

        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Use OpenSpout for generation
        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $writer->openToFile($filePath);

        foreach ($data as $row) {
            $cells = array_map(
                fn($value) => \OpenSpout\Common\Entity\Cell::fromValue($value),
                $row
            );
            $rowEntity = new \OpenSpout\Common\Entity\Row($cells);
            $writer->addRow($rowEntity);
        }

        $writer->close();

        return $filePath;
    }

    abstract public static function getColumns(): array;
}
