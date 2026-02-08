<?php

namespace Rhaima\VoltPanel\Tests\Unit\Widgets;

use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Tests\TestCase;
use Rhaima\VoltPanel\Widgets\ActivityLogWidget;

class ActivityLogWidgetTest extends TestCase
{
    #[Test]
    public function it_can_create_an_activity_log_widget(): void
    {
        $widget = ActivityLogWidget::make();

        $this->assertInstanceOf(ActivityLogWidget::class, $widget);
    }

    #[Test]
    public function it_can_set_custom_limit(): void
    {
        $widget = ActivityLogWidget::make()->limit(20);

        $this->assertInstanceOf(ActivityLogWidget::class, $widget);
    }

    #[Test]
    public function it_can_set_log_name_filter(): void
    {
        $widget = ActivityLogWidget::make()->logName('user_actions');

        $this->assertInstanceOf(ActivityLogWidget::class, $widget);
    }

    #[Test]
    public function it_can_set_subject_type_filter(): void
    {
        $widget = ActivityLogWidget::make()->subjectType('App\\Models\\User');

        $this->assertInstanceOf(ActivityLogWidget::class, $widget);
    }

    #[Test]
    public function it_supports_method_chaining(): void
    {
        $widget = ActivityLogWidget::make()
            ->limit(15)
            ->logName('default')
            ->subjectType('App\\Models\\Post');

        $this->assertInstanceOf(ActivityLogWidget::class, $widget);
    }
}
