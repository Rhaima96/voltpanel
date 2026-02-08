<?php

namespace Rhaima\VoltPanel\Tables\Columns;

use Closure;

abstract class Column
{
    protected string $name;
    protected ?string $label = null;
    protected bool $sortable = false;
    protected bool $searchable = false;
    protected ?Closure $formatStateUsing = null;
    protected ?string $alignment = null;
    protected bool $toggleable = true;
    protected bool $hidden = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function formatStateUsing(Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function alignment(string $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function alignLeft(): static
    {
        return $this->alignment('left');
    }

    public function alignCenter(): static
    {
        return $this->alignment('center');
    }

    public function alignRight(): static
    {
        return $this->alignment('right');
    }

    public function toggleable(bool $toggleable = true): static
    {
        $this->toggleable = $toggleable;

        return $this;
    }

    public function hidden(bool $hidden = true): static
    {
        $this->hidden = $hidden;

        return $this;
    }

    abstract public function getType(): string;

    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->name,
            'label' => $this->label ?? ucfirst(str_replace('_', ' ', $this->name)),
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'alignment' => $this->alignment ?? 'left',
            'toggleable' => $this->toggleable,
            'hidden' => $this->hidden,
        ];
    }
}
