<?php

namespace Rhaima\VoltPanel\Forms;

use Closure;
use Illuminate\Support\Collection;

class Form
{
    protected Collection $components;
    protected ?string $model = null;
    protected ?Closure $fillUsing = null;
    protected ?Closure $saveUsing = null;
    protected mixed $record = null;

    public function __construct(mixed $record = null, ?string $modelClass = null)
    {
        $this->components = new Collection();
        $this->record = $record;
        $this->model = $modelClass;
    }

    public static function make(mixed $record = null, ?string $modelClass = null): static
    {
        return new static($record, $modelClass);
    }

    public function record(mixed $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function isCreating(): bool
    {
        return $this->record === null;
    }

    public function isEditing(): bool
    {
        return $this->record !== null;
    }

    public function schema(array $components): static
    {
        $this->components = collect($components)->map(function ($component) {
            if (method_exists($component, 'form')) {
                $component->form($this);
            }
            return $component;
        });

        return $this;
    }

    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function fill(Closure $callback): static
    {
        $this->fillUsing = $callback;

        return $this;
    }

    public function save(Closure $callback): static
    {
        $this->saveUsing = $callback;

        return $this;
    }

    public function getComponents(): Collection
    {
        return $this->components;
    }

    public function getModel(): mixed
    {
        return $this->record;
    }

    public function getModelClass(): ?string
    {
        return $this->model;
    }

    public function toArray(): array
    {
        return [
            'components' => $this->components->map(fn ($component) => $component->toArray())->all(),
            'model' => $this->model ?? '',
        ];
    }
}
