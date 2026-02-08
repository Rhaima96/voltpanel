<?php

namespace Rhaima\VoltPanel\Widgets;

class StatsOverviewWidget extends Widget
{
    protected ?string $heading = null;
    protected ?string $value = null;
    protected ?string $description = null;
    protected ?string $icon = null;
    protected ?string $color = 'primary';
    protected ?string $url = null;

    public static function make(): static
    {
        return new static();
    }

    public function heading(string $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function value(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

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

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getData(): array
    {
        return [
            'heading' => $this->heading,
            'value' => $this->value,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'url' => $this->url,
        ];
    }
}
