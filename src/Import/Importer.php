<?php

namespace Rhaima\VoltPanel\Import;

use Closure;
use Illuminate\Support\Collection;

abstract class Importer
{
    protected static ?string $model = null;
    protected array $columns = [];
    protected ?Closure $beforeImport = null;
    protected ?Closure $afterImport = null;
    protected ?Closure $beforeSave = null;
    protected bool $updateExisting = false;
    protected ?string $uniqueBy = null;

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

    public function updateExisting(bool $update = true, ?string $uniqueBy = null): static
    {
        $this->updateExisting = $update;
        $this->uniqueBy = $uniqueBy;

        return $this;
    }

    public function beforeImport(Closure $callback): static
    {
        $this->beforeImport = $callback;

        return $this;
    }

    public function afterImport(Closure $callback): static
    {
        $this->afterImport = $callback;

        return $this;
    }

    public function beforeSave(Closure $callback): static
    {
        $this->beforeSave = $callback;

        return $this;
    }

    public function import(array $data): Collection
    {
        if ($this->beforeImport) {
            call_user_func($this->beforeImport, $data);
        }

        $imported = collect();
        $model = static::getModel();

        foreach ($data as $row) {
            $attributes = $this->mapRow($row);

            if ($this->beforeSave) {
                $attributes = call_user_func($this->beforeSave, $attributes, $row);
            }

            if ($this->updateExisting && $this->uniqueBy) {
                $record = $model::updateOrCreate(
                    [$this->uniqueBy => $attributes[$this->uniqueBy]],
                    $attributes
                );
            } else {
                $record = $model::create($attributes);
            }

            $imported->push($record);
        }

        if ($this->afterImport) {
            call_user_func($this->afterImport, $imported);
        }

        return $imported;
    }

    protected function mapRow(array $row): array
    {
        $mapped = [];

        foreach ($this->columns as $column => $key) {
            if (is_callable($key)) {
                $mapped[$column] = call_user_func($key, $row);
            } else {
                $mapped[$column] = $row[$key] ?? null;
            }
        }

        return $mapped;
    }

    abstract public static function getColumns(): array;
}
