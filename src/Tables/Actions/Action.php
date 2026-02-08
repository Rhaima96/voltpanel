<?php

namespace Rhaima\VoltPanel\Tables\Actions;

use Closure;

abstract class Action
{
    public string $name;
    public ?string $label = null;
    public ?string $icon = null;
    public ?string $color = null;
    public ?Closure $visible = null;
    public ?Closure $url = null;
    public bool $openInNewTab = false;

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

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function visible(Closure $callback): static
    {
        $this->visible = $callback;

        return $this;
    }

    public function url(Closure $callback): static
    {
        $this->url = $callback;

        return $this;
    }

    public function openInNewTab(bool $openInNewTab = true): static
    {
        $this->openInNewTab = $openInNewTab;

        return $this;
    }

    public function isVisible($record): bool
    {
        if ($this->visible === null) {
            return true;
        }

        return call_user_func($this->visible, $record);
    }

    public function getUrl($record): ?string
    {
        if ($this->url) {
            return call_user_func($this->url, $record);
        }

        return null;
    }

    abstract public function getType(): string;

    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->name,
            'label' => $this->label ?? ucfirst(str_replace('_', ' ', $this->name)),
            'icon' => $this->icon ?? '',
            'color' => $this->color ?? 'gray',
            'openInNewTab' => $this->openInNewTab,
        ];
    }
}
