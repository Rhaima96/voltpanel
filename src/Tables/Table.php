<?php

namespace Rhaima\VoltPanel\Tables;

use Closure;
use Illuminate\Support\Collection;
use Rhaima\VoltPanel\Tables\Concerns\HasColumnVisibility;

class Table
{
    use HasColumnVisibility;
    protected Collection $columns;
    protected Collection $filters;
    protected Collection $actions;
    protected Collection $bulkActions;
    protected bool $searchable = true;
    protected bool $sortable = true;
    protected int $defaultPerPage = 10;
    protected array $perPageOptions = [10, 25, 50, 100];

    public function __construct()
    {
        $this->columns = new Collection();
        $this->filters = new Collection();
        $this->actions = new Collection();
        $this->bulkActions = new Collection();
    }

    public static function make(): static
    {
        return new static();
    }

    public function columns(array $columns): static
    {
        $this->columns = collect($columns);

        return $this;
    }

    public function filters(array $filters): static
    {
        $this->filters = collect($filters);

        return $this;
    }

    public function actions(array $actions): static
    {
        $this->actions = collect($actions);

        return $this;
    }

    public function bulkActions(array $actions): static
    {
        $this->bulkActions = collect($actions);

        return $this;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function defaultPerPage(int $perPage): static
    {
        $this->defaultPerPage = $perPage;

        return $this;
    }

    public function perPageOptions(array $options): static
    {
        $this->perPageOptions = $options;

        return $this;
    }

    public function getColumns(): Collection
    {
        return $this->columns;
    }

    public function getFilters(): Collection
    {
        return $this->filters;
    }

    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function getBulkActions(): Collection
    {
        return $this->bulkActions;
    }

    public function toArray(): array
    {
        return [
            'columns' => $this->columns->map(fn ($column) => $column->toArray())->all(),
            'filters' => $this->filters->map(fn ($filter) => $filter->toArray())->all(),
            'actions' => $this->actions->map(fn ($action) => $action->toArray())->all(),
            'bulkActions' => $this->bulkActions->map(fn ($action) => $action->toArray())->all(),
            'searchable' => $this->searchable,
            'sortable' => $this->sortable,
            'defaultPerPage' => $this->defaultPerPage,
            'perPageOptions' => $this->perPageOptions,
            'columnVisibilityEnabled' => $this->columnVisibilityEnabled,
            'defaultHiddenColumns' => $this->defaultHiddenColumns,
            'persistColumnVisibility' => $this->persistColumnVisibility,
        ];
    }
}
