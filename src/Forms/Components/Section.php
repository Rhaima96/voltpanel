<?php

namespace Rhaima\VoltPanel\Forms\Components;

use Closure;
use Illuminate\Support\Collection;

class Section
{
    protected ?string $heading = null;
    protected ?string $description = null;
    protected Collection $schema;
    protected int $columns = 1;
    protected bool $collapsible = false;
    protected bool $collapsed = false;
    protected ?Closure $visible = null;
    protected ?\Rhaima\VoltPanel\Forms\Form $form = null;

    public function __construct(?string $heading = null)
    {
        $this->heading = $heading;
        $this->schema = new Collection();
    }

    public static function make(?string $heading = null): static
    {
        return new static($heading);
    }

    public function form(\Rhaima\VoltPanel\Forms\Form $form): static
    {
        $this->form = $form;

        // Pass form to all child components
        $this->schema = $this->schema->map(function ($component) use ($form) {
            if (method_exists($component, 'form')) {
                $component->form($form);
            }
            return $component;
        });

        return $this;
    }

    public function heading(string $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function schema(array $components): static
    {
        $this->schema = collect($components)->map(function ($component) {
            if (method_exists($component, 'form') && $this->form) {
                $component->form($this->form);
            }
            return $component;
        });

        return $this;
    }

    public function columns(int $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function collapsible(bool $collapsible = true): static
    {
        $this->collapsible = $collapsible;

        return $this;
    }

    public function collapsed(bool $collapsed = true): static
    {
        $this->collapsed = $collapsed;

        // If setting collapsed, also make it collapsible
        if ($collapsed) {
            $this->collapsible = true;
        }

        return $this;
    }

    public function visible(Closure $callback): static
    {
        $this->visible = $callback;

        return $this;
    }

    public function getType(): string
    {
        return 'section';
    }

    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'heading' => $this->heading,
            'description' => $this->description,
            'columns' => $this->columns,
            'collapsible' => $this->collapsible,
            'collapsed' => $this->collapsed,
            'components' => $this->schema->map(fn ($component) => $component->toArray())->all(),
        ];
    }
}
