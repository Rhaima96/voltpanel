<?php

namespace Rhaima\VoltPanel\Tables\Concerns;

trait HasColumnVisibility
{
    protected bool $columnVisibilityEnabled = true;
    protected array $hiddenColumns = [];
    protected array $defaultHiddenColumns = [];
    protected bool $persistColumnVisibility = true;

    public function columnVisibility(bool $enabled = true): static
    {
        $this->columnVisibilityEnabled = $enabled;
        return $this;
    }

    public function hideColumns(array $columns): static
    {
        $this->hiddenColumns = $columns;
        return $this;
    }

    public function defaultHiddenColumns(array $columns): static
    {
        $this->defaultHiddenColumns = $columns;
        return $this;
    }

    public function persistColumnVisibility(bool $persist = true): static
    {
        $this->persistColumnVisibility = $persist;
        return $this;
    }

    public function isColumnVisibilityEnabled(): bool
    {
        return $this->columnVisibilityEnabled;
    }

    public function getHiddenColumns(): array
    {
        return $this->hiddenColumns;
    }

    public function getDefaultHiddenColumns(): array
    {
        return $this->defaultHiddenColumns;
    }

    public function shouldPersistColumnVisibility(): bool
    {
        return $this->persistColumnVisibility;
    }

    public function getVisibleColumns(): array
    {
        return array_filter(
            $this->getColumns(),
            fn ($column) => !in_array($column->getName(), $this->hiddenColumns)
        );
    }
}
