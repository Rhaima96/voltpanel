<?php

namespace Rhaima\VoltPanel\Tests\Unit\Widgets;

use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Tests\TestCase;
use Rhaima\VoltPanel\Widgets\TimeSeriesChartWidget;

class TimeSeriesChartWidgetTest extends TestCase
{
    #[Test]
    public function it_can_create_a_time_series_widget(): void
    {
        $widget = TimeSeriesChartWidget::make();

        $this->assertInstanceOf(TimeSeriesChartWidget::class, $widget);
    }

    #[Test]
    public function it_has_line_chart_type_by_default(): void
    {
        $widget = TimeSeriesChartWidget::make();
        $data = $widget->getData();

        $this->assertEquals('line', $data['type']);
    }

    #[Test]
    public function it_has_time_scale_configured(): void
    {
        $widget = TimeSeriesChartWidget::make();
        $data = $widget->getData();

        $this->assertEquals('time', $data['options']['scales']['x']['type'] ?? '');
    }

    #[Test]
    public function it_has_smooth_lines_by_default(): void
    {
        $widget = TimeSeriesChartWidget::make();
        $data = $widget->getData();

        $this->assertEquals(0.4, $data['options']['elements']['line']['tension'] ?? 0);
    }
}
