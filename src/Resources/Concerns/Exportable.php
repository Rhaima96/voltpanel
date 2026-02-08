<?php

namespace Rhaima\VoltPanel\Resources\Concerns;

use Illuminate\Support\Collection;

trait Exportable
{
    protected static bool $canExport = true;

    protected static array $exportFormats = ['csv', 'xlsx', 'pdf'];

    protected static int $exportChunkSize = 1000;

    public static function canExport(): bool
    {
        return static::$canExport;
    }

    public static function getExportFormats(): array
    {
        return static::$exportFormats;
    }

    public static function getExportColumns(): array
    {
        // By default, export all table columns
        $table = static::table(new \Rhaima\VoltPanel\Tables\Table());

        return collect($table->toArray()['columns'])
            ->pluck('name')
            ->toArray();
    }

    public static function getExportHeaders(): array
    {
        // By default, use column labels as headers
        $table = static::table(new \Rhaima\VoltPanel\Tables\Table());

        return collect($table->toArray()['columns'])
            ->mapWithKeys(fn($column) => [$column['name'] => $column['label']])
            ->toArray();
    }

    public static function formatExportValue($record, string $column): mixed
    {
        // Handle nested properties (e.g., 'user.name')
        if (str_contains($column, '.')) {
            $keys = explode('.', $column);
            $value = $record;

            foreach ($keys as $key) {
                $value = $value?->$key ?? null;
                if ($value === null) break;
            }

            return $value;
        }

        return $record->$column ?? null;
    }

    public static function getExportQuery(?array $filters = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = static::getModel()::query();

        // Apply filters if provided (same logic as index)
        if ($filters && isset($filters['search'])) {
            $table = static::table(new \Rhaima\VoltPanel\Tables\Table());
            $searchableColumns = collect($table->toArray()['columns'])
                ->filter(fn($column) => $column['searchable'] ?? false)
                ->pluck('name')
                ->toArray();

            if (!empty($searchableColumns)) {
                $query->where(function ($q) use ($searchableColumns, $filters) {
                    foreach ($searchableColumns as $column) {
                        $q->orWhere($column, 'LIKE', "%{$filters['search']}%");
                    }
                });
            }
        }

        return $query;
    }
}
