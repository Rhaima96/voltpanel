<?php

namespace Rhaima\VoltPanel\Tests\Unit\Widgets;

use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Tests\TestCase;
use Rhaima\VoltPanel\Widgets\AdvancedChartWidget;

class AdvancedChartWidgetTest extends TestCase
{
    #[Test]
    public function it_can_create_a_chart_widget(): void
    {
        $widget = AdvancedChartWidget::make();

        $this->assertInstanceOf(AdvancedChartWidget::class, $widget);
    }

    #[Test]
    public function it_can_set_chart_type(): void
    {
        $widget = AdvancedChartWidget::make()->line();
        $data = $widget->getData();

        $this->assertEquals('line', $data['type']);
    }

    #[Test]
    public function it_can_set_datasets(): void
    {
        $datasets = [
            [
                'label' => 'Test Dataset',
                'data' => [10, 20, 30],
            ]
        ];

        $widget = AdvancedChartWidget::make()->datasets($datasets);
        $data = $widget->getData();

        $this->assertEquals($datasets, $data['chartData']['datasets']);
    }

    #[Test]
    public function it_can_set_labels(): void
    {
        $labels = ['Jan', 'Feb', 'Mar'];

        $widget = AdvancedChartWidget::make()->labels($labels);
        $data = $widget->getData();

        $this->assertEquals($labels, $data['chartData']['labels']);
    }

    #[Test]
    public function it_can_set_heading(): void
    {
        $widget = AdvancedChartWidget::make()->heading('Test Chart');
        $data = $widget->getData();

        $this->assertEquals('Test Chart', $data['heading']);
    }

    #[Test]
    public function it_can_enable_export(): void
    {
        $widget = AdvancedChartWidget::make()->showExport();
        $data = $widget->getData();

        $this->assertTrue($data['showExport']);
    }

    #[Test]
    public function it_can_set_color_scheme(): void
    {
        $widget = AdvancedChartWidget::make()->oceanColors();
        $data = $widget->getData();

        $this->assertEquals('ocean', $data['colorScheme']);
    }

    #[Test]
    public function it_returns_correct_data_structure(): void
    {
        $widget = AdvancedChartWidget::make()
            ->heading('Test Chart')
            ->line()
            ->labels(['A', 'B', 'C'])
            ->datasets([[
                'label' => 'Dataset',
                'data' => [1, 2, 3]
            ]]);

        $data = $widget->getData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('heading', $data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('chartData', $data);
        $this->assertArrayHasKey('options', $data);
        $this->assertArrayHasKey('showExport', $data);
        $this->assertArrayHasKey('colorScheme', $data);
    }
}
