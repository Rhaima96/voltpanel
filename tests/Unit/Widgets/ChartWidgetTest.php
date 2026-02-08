<?php

namespace Rhaima\VoltPanel\Tests\Unit\Widgets;

use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Tests\TestCase;
use Rhaima\VoltPanel\Widgets\ChartWidget;

class ChartWidgetTest extends TestCase
{
    #[Test]
    public function it_can_create_a_chart_widget(): void
    {
        $widget = ChartWidget::make();

        $this->assertInstanceOf(ChartWidget::class, $widget);
    }

    #[Test]
    public function it_can_set_heading(): void
    {
        $widget = ChartWidget::make()->heading('Sales Chart');
        $data = $widget->getData();

        $this->assertEquals('Sales Chart', $data['heading']);
    }

    #[Test]
    public function it_can_set_chart_type(): void
    {
        $widget = ChartWidget::make()->type('bar');
        $data = $widget->getData();

        $this->assertEquals('bar', $data['type']);
    }

    #[Test]
    public function it_has_line_type_by_default(): void
    {
        $widget = ChartWidget::make();
        $data = $widget->getData();

        $this->assertEquals('line', $data['type']);
    }

    #[Test]
    public function it_can_set_datasets(): void
    {
        $datasets = [
            [
                'label' => 'Revenue',
                'data' => [100, 200, 300],
            ]
        ];

        $widget = ChartWidget::make()->datasets($datasets);
        $data = $widget->getData();

        $this->assertEquals($datasets, $data['chartData']['datasets']);
    }

    #[Test]
    public function it_can_set_labels(): void
    {
        $labels = ['January', 'February', 'March'];

        $widget = ChartWidget::make()->labels($labels);
        $data = $widget->getData();

        $this->assertEquals($labels, $data['chartData']['labels']);
    }

    #[Test]
    public function it_can_set_custom_options(): void
    {
        $options = [
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];

        $widget = ChartWidget::make()->options($options);
        $data = $widget->getData();

        $this->assertEquals($options, $data['options']);
    }

    #[Test]
    public function it_returns_correct_data_structure(): void
    {
        $widget = ChartWidget::make()
            ->heading('Test Chart')
            ->type('bar')
            ->labels(['A', 'B', 'C'])
            ->datasets([['label' => 'Test', 'data' => [1, 2, 3]]]);

        $data = $widget->getData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('heading', $data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('chartData', $data);
        $this->assertArrayHasKey('options', $data);
        $this->assertArrayHasKey('labels', $data['chartData']);
        $this->assertArrayHasKey('datasets', $data['chartData']);
    }

    #[Test]
    public function it_supports_method_chaining(): void
    {
        $widget = ChartWidget::make()
            ->heading('Chained')
            ->type('pie')
            ->labels(['X'])
            ->datasets([['data' => [1]]])
            ->options(['responsive' => true]);

        $this->assertInstanceOf(ChartWidget::class, $widget);

        $data = $widget->getData();
        $this->assertEquals('Chained', $data['heading']);
        $this->assertEquals('pie', $data['type']);
    }
}
