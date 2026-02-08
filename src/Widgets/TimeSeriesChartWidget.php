<?php

namespace Rhaima\VoltPanel\Widgets;

class TimeSeriesChartWidget extends AdvancedChartWidget
{
    public static function make(): static
    {
        $instance = new static();
        $instance->type = 'line';
        $instance->timeSeries();
        $instance->smooth();
        return $instance;
    }

    /**
     * Create a time series chart showing daily counts
     */
    public static function dailyCounts(
        string $model,
        string $label = 'Daily Count',
        int $days = 30,
        string $dateColumn = 'created_at'
    ): static {
        $widget = static::make();
        $data = static::fromModel($model, $dateColumn, 'id', 'day', $days);

        return $widget
            ->labels($data['labels'])
            ->datasets([
                [
                    'label' => $label,
                    'data' => $data['datasets'][0]['data'],
                    'fill' => true,
                ]
            ]);
    }

    /**
     * Create a time series chart comparing multiple models
     */
    public static function compare(
        array $models, // ['label' => Model::class]
        int $days = 30,
        string $dateColumn = 'created_at'
    ): static {
        $widget = static::make();
        $datasets = [];
        $labels = [];

        foreach ($models as $label => $model) {
            $data = static::fromModel($model, $dateColumn, 'id', 'day', $days);
            if (empty($labels)) {
                $labels = $data['labels'];
            }

            $datasets[] = [
                'label' => $label,
                'data' => $data['datasets'][0]['data'],
                'fill' => false,
            ];
        }

        return $widget
            ->labels($labels)
            ->datasets($datasets);
    }
}
