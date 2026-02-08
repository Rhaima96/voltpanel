<?php

namespace Rhaima\VoltPanel\Widgets;

class StatsChartWidget extends AdvancedChartWidget
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Create a bar chart showing counts by category
     */
    public static function countByColumn(
        string $model,
        string $column,
        string $label = null,
        int $limit = 10
    ): static {
        $widget = static::make()->bar();

        $data = $model::query()
            ->selectRaw("{$column}, COUNT(*) as count")
            ->groupBy($column)
            ->orderByDesc('count')
            ->limit($limit)
            ->get();

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $labels[] = ucfirst($item->$column);
            $values[] = $item->count;
        }

        return $widget
            ->labels($labels)
            ->datasets([
                [
                    'label' => $label ?? ucfirst($column),
                    'data' => $values,
                ]
            ]);
    }

    /**
     * Create a doughnut chart showing distribution by status
     */
    public static function statusDistribution(
        string $model,
        string $statusColumn = 'status'
    ): static {
        $widget = static::make()->doughnut();
        $data = static::aggregateByStatus($model, $statusColumn);

        return $widget
            ->labels($data['labels'])
            ->datasets($data['datasets']);
    }

    /**
     * Create a pie chart showing percentage distribution
     */
    public static function percentageDistribution(
        string $model,
        string $column,
        string $label = 'Distribution'
    ): static {
        $widget = static::make()->pie();

        $data = $model::query()
            ->selectRaw("{$column}, COUNT(*) as count")
            ->groupBy($column)
            ->get();

        $total = $data->sum('count');
        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $percentage = $total > 0 ? round(($item->count / $total) * 100, 1) : 0;
            $labels[] = ucfirst($item->$column) . " ({$percentage}%)";
            $values[] = $item->count;
        }

        return $widget
            ->labels($labels)
            ->datasets([
                [
                    'label' => $label,
                    'data' => $values,
                ]
            ]);
    }

    /**
     * Create a horizontal bar chart for rankings
     */
    public static function topItems(
        string $model,
        string $nameColumn,
        string $valueColumn,
        int $limit = 10,
        string $label = 'Value'
    ): static {
        $widget = static::make()->bar()->horizontal();

        $data = $model::query()
            ->orderByDesc($valueColumn)
            ->limit($limit)
            ->get();

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $labels[] = $item->$nameColumn;
            $values[] = $item->$valueColumn;
        }

        return $widget
            ->labels($labels)
            ->datasets([
                [
                    'label' => $label,
                    'data' => $values,
                ]
            ]);
    }
}
