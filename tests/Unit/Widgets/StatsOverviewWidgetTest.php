<?php

namespace Rhaima\VoltPanel\Tests\Unit\Widgets;

use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Tests\TestCase;
use Rhaima\VoltPanel\Widgets\StatsOverviewWidget;

class StatsOverviewWidgetTest extends TestCase
{
    #[Test]
    public function it_can_create_a_stats_widget(): void
    {
        $widget = StatsOverviewWidget::make();

        $this->assertInstanceOf(StatsOverviewWidget::class, $widget);
    }

    #[Test]
    public function it_can_set_heading(): void
    {
        $widget = StatsOverviewWidget::make()->heading('Total Users');
        $data = $widget->getData();

        $this->assertEquals('Total Users', $data['heading']);
    }

    #[Test]
    public function it_can_set_value(): void
    {
        $widget = StatsOverviewWidget::make()->value('1,234');
        $data = $widget->getData();

        $this->assertEquals('1,234', $data['value']);
    }

    #[Test]
    public function it_can_set_description(): void
    {
        $widget = StatsOverviewWidget::make()->description('12% increase from last month');
        $data = $widget->getData();

        $this->assertEquals('12% increase from last month', $data['description']);
    }

    #[Test]
    public function it_can_set_icon(): void
    {
        $widget = StatsOverviewWidget::make()->icon('heroicon-o-users');
        $data = $widget->getData();

        $this->assertEquals('heroicon-o-users', $data['icon']);
    }

    #[Test]
    public function it_can_set_color(): void
    {
        $widget = StatsOverviewWidget::make()->color('success');
        $data = $widget->getData();

        $this->assertEquals('success', $data['color']);
    }

    #[Test]
    public function it_has_primary_color_by_default(): void
    {
        $widget = StatsOverviewWidget::make();
        $data = $widget->getData();

        $this->assertEquals('primary', $data['color']);
    }

    #[Test]
    public function it_can_set_url(): void
    {
        $widget = StatsOverviewWidget::make()->url('/users');
        $data = $widget->getData();

        $this->assertEquals('/users', $data['url']);
    }

    #[Test]
    public function it_returns_correct_data_structure(): void
    {
        $widget = StatsOverviewWidget::make()
            ->heading('Total Sales')
            ->value('$45,231')
            ->description('+20% from last month')
            ->icon('heroicon-o-currency-dollar')
            ->color('success')
            ->url('/sales');

        $data = $widget->getData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('heading', $data);
        $this->assertArrayHasKey('value', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('icon', $data);
        $this->assertArrayHasKey('color', $data);
        $this->assertArrayHasKey('url', $data);
    }

    #[Test]
    public function it_supports_method_chaining(): void
    {
        $widget = StatsOverviewWidget::make()
            ->heading('Orders')
            ->value('432')
            ->description('This week')
            ->icon('heroicon-o-shopping-cart')
            ->color('warning')
            ->url('/orders');

        $this->assertInstanceOf(StatsOverviewWidget::class, $widget);

        $data = $widget->getData();
        $this->assertEquals('Orders', $data['heading']);
        $this->assertEquals('432', $data['value']);
        $this->assertEquals('warning', $data['color']);
    }
}
