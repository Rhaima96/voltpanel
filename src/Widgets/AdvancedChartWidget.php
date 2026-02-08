<?php

namespace Rhaima\VoltPanel\Widgets;

class AdvancedChartWidget extends Widget
{
    protected ?string $heading = null;
    protected string $type = 'line';
    protected array $datasets = [];
    protected array $labels = [];
    protected array $options = [];
    protected bool $showExport = false;
    protected string $exportFilename = 'chart';
    protected bool $realtime = false;
    protected int $realtimeInterval = 3000;
    protected ?string $realtimeCallback = null;
    protected string $colorScheme = 'default';
    protected ?string $description = null;

    public static function make(): static
    {
        return new static();
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

    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function line(): static
    {
        return $this->type('line');
    }

    public function bar(): static
    {
        return $this->type('bar');
    }

    public function pie(): static
    {
        return $this->type('pie');
    }

    public function doughnut(): static
    {
        return $this->type('doughnut');
    }

    public function scatter(): static
    {
        return $this->type('scatter');
    }

    public function bubble(): static
    {
        return $this->type('bubble');
    }

    public function radar(): static
    {
        return $this->type('radar');
    }

    public function polarArea(): static
    {
        return $this->type('polarArea');
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
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function showExport(bool $show = true): static
    {
        $this->showExport = $show;
        return $this;
    }

    public function exportFilename(string $filename): static
    {
        $this->exportFilename = $filename;
        return $this;
    }

    public function realtime(bool $enabled = true, int $interval = 3000): static
    {
        $this->realtime = $enabled;
        $this->realtimeInterval = $interval;
        return $this;
    }

    public function realtimeCallback(string $callback): static
    {
        $this->realtimeCallback = $callback;
        return $this;
    }

    public function colorScheme(string $scheme): static
    {
        $this->colorScheme = $scheme;
        return $this;
    }

    // Predefined color schemes
    public function pastelColors(): static
    {
        return $this->colorScheme('pastel');
    }

    public function vibrantColors(): static
    {
        return $this->colorScheme('vibrant');
    }

    public function earthColors(): static
    {
        return $this->colorScheme('earth');
    }

    public function oceanColors(): static
    {
        return $this->colorScheme('ocean');
    }

    public function sunsetColors(): static
    {
        return $this->colorScheme('sunset');
    }

    // Chart configuration helpers
    public function stacked(): static
    {
        $this->options['scales']['x']['stacked'] = true;
        $this->options['scales']['y']['stacked'] = true;
        return $this;
    }

    public function horizontal(): static
    {
        $this->options['indexAxis'] = 'y';
        return $this;
    }

    public function smooth(): static
    {
        $this->options['elements']['line']['tension'] = 0.4;
        return $this;
    }

    public function hideLegend(): static
    {
        $this->options['plugins']['legend']['display'] = false;
        return $this;
    }

    public function legendPosition(string $position): static
    {
        $this->options['plugins']['legend']['position'] = $position;
        return $this;
    }

    // Time series helper
    public function timeSeries(string $unit = 'day'): static
    {
        $this->options['scales']['x'] = [
            'type' => 'time',
            'time' => [
                'unit' => $unit,
                'displayFormats' => [
                    'hour' => 'HH:mm',
                    'day' => 'MMM dd',
                    'week' => 'MMM dd',
                    'month' => 'MMM yyyy',
                    'year' => 'yyyy'
                ]
            ]
        ];
        return $this;
    }

    // Aggregation helpers
    public static function fromModel(
        string $model,
        string $dateColumn = 'created_at',
        string $valueColumn = 'id',
        string $groupBy = 'day',
        int $days = 30
    ): array {
        $startDate = now()->subDays($days)->startOfDay();

        $query = $model::query()
            ->where($dateColumn, '>=', $startDate)
            ->selectRaw("DATE({$dateColumn}) as date, COUNT({$valueColumn}) as count")
            ->groupBy('date')
            ->orderBy('date');

        $data = $query->get();

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $labels[] = $item->date;
            $values[] = $item->count;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => class_basename($model),
                    'data' => $values,
                ]
            ]
        ];
    }

    public static function aggregateByStatus(string $model, string $statusColumn = 'status'): array
    {
        $data = $model::query()
            ->selectRaw("{$statusColumn}, COUNT(*) as count")
            ->groupBy($statusColumn)
            ->get();

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $labels[] = ucfirst($item->$statusColumn);
            $values[] = $item->count;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Count by Status',
                    'data' => $values,
                ]
            ]
        ];
    }

    public function getData(): array
    {
        return [
            'heading' => $this->heading,
            'description' => $this->description,
            'type' => $this->type,
            'chartData' => [
                'labels' => $this->labels,
                'datasets' => $this->datasets,
            ],
            'options' => $this->options,
            'showExport' => $this->showExport,
            'exportFilename' => $this->exportFilename,
            'realtime' => $this->realtime,
            'realtimeInterval' => $this->realtimeInterval,
            'realtimeCallback' => $this->realtimeCallback,
            'colorScheme' => $this->colorScheme,
        ];
    }
}
