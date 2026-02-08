<?php

namespace Rhaima\VoltPanel\Tests\Unit\Widgets;

use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Tests\TestCase;
use Rhaima\VoltPanel\Widgets\StatsChartWidget;

class StatsChartWidgetTest extends TestCase
{
    #[Test]
    public function it_can_create_a_stats_chart_widget(): void
    {
        $widget = StatsChartWidget::make();

        $this->assertInstanceOf(StatsChartWidget::class, $widget);
    }

    #[Test]
    public function it_inherits_from_advanced_chart_widget(): void
    {
        $widget = StatsChartWidget::make();

        $this->assertInstanceOf(\Rhaima\VoltPanel\Widgets\AdvancedChartWidget::class, $widget);
    }

    #[Test]
    public function it_can_set_bar_chart_type(): void
    {
        $widget = StatsChartWidget::make()->bar();
        $data = $widget->getData();

        $this->assertEquals('bar', $data['type']);
    }

    #[Test]
    public function it_can_set_pie_chart_type(): void
    {
        $widget = StatsChartWidget::make()->pie();
        $data = $widget->getData();

        $this->assertEquals('pie', $data['type']);
    }

    #[Test]
    public function it_can_set_doughnut_chart_type(): void
    {
        $widget = StatsChartWidget::make()->doughnut();
        $data = $widget->getData();

        $this->assertEquals('doughnut', $data['type']);
    }

    #[Test]
    public function it_supports_horizontal_bar_charts(): void
    {
        $widget = StatsChartWidget::make()->bar()->horizontal();
        $data = $widget->getData();

        $this->assertEquals('bar', $data['type']);
        $this->assertTrue($data['options']['indexAxis'] === 'y');
    }

    #[Test]
    public function it_can_configure_datasets_and_labels(): void
    {
        $widget = StatsChartWidget::make()
            ->bar()
            ->labels(['Category A', 'Category B'])
            ->datasets([
                [
                    'label' => 'Count',
                    'data' => [10, 20],
                ]
            ]);

        $data = $widget->getData();

        $this->assertEquals(['Category A', 'Category B'], $data['chartData']['labels']);
        $this->assertEquals('Count', $data['chartData']['datasets'][0]['label']);
        $this->assertEquals([10, 20], $data['chartData']['datasets'][0]['data']);
    }

    #[Test]
    public function it_supports_color_schemes(): void
    {
        $widget = StatsChartWidget::make()->oceanColors();
        $data = $widget->getData();

        $this->assertEquals('ocean', $data['colorScheme']);
    }

    #[Test]
    public function it_supports_export_functionality(): void
    {
        $widget = StatsChartWidget::make()->showExport();
        $data = $widget->getData();

        $this->assertTrue($data['showExport']);
    }

    #[Test]
    public function it_can_set_heading(): void
    {
        $widget = StatsChartWidget::make()->heading('Sales by Category');
        $data = $widget->getData();

        $this->assertEquals('Sales by Category', $data['heading']);
    }
}
