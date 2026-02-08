<?php

namespace Rhaima\VoltPanel\Widgets;

class ChartWidget extends Widget
{
    protected ?string $heading = null;
    protected string $type = 'line';
    protected array $datasets = [];
    protected array $labels = [];
    protected array $options = [];

    public static function make(): static
    {
        return new static();
    }

    public function heading(string $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function datasets(array $datasets): static
    {
        $this->datasets = $datasets;

        return $this;
    }

    public function labels(array $labels): static
    {
        $this->labels = $labels;

        return $this;
    }

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getData(): array
    {
        return [
            'heading' => $this->heading,
            'type' => $this->type,
            'chartData' => [
                'labels' => $this->labels,
                'datasets' => $this->datasets,
            ],
            'options' => $this->options,
        ];
    }
}
