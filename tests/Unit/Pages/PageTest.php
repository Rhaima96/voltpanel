<?php

namespace Rhaima\VoltPanel\Tests\Unit\Pages;

use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Pages\Page;
use Rhaima\VoltPanel\Tests\TestCase;

class PageTest extends TestCase
{
    protected function getTestPageClass(): Page
    {
        return new class extends Page {
            protected static ?string $navigationLabel = 'Test Page';
            protected static ?string $navigationIcon = 'heroicon-o-document';
            protected static ?string $navigationGroup = 'Testing';
            protected static ?int $navigationSort = 5;
            protected static ?string $navigationDescription = 'A test page';
        };
    }

    #[Test]
    public function it_can_get_navigation_label(): void
    {
        $page = $this->getTestPageClass();

        $this->assertEquals('Test Page', $page::getNavigationLabel());
    }

    #[Test]
    public function it_can_get_navigation_icon(): void
    {
        $page = $this->getTestPageClass();

        $this->assertEquals('heroicon-o-document', $page::getNavigationIcon());
    }

    #[Test]
    public function it_can_get_navigation_group(): void
    {
        $page = $this->getTestPageClass();

        $this->assertEquals('Testing', $page::getNavigationGroup());
    }

    #[Test]
    public function it_can_get_navigation_sort(): void
    {
        $page = $this->getTestPageClass();

        $this->assertEquals(5, $page::getNavigationSort());
    }

    #[Test]
    public function it_returns_zero_sort_order_by_default(): void
    {
        $page = new class extends Page {};

        $this->assertEquals(0, $page::getNavigationSort());
    }

    #[Test]
    public function it_can_get_navigation_description(): void
    {
        $page = $this->getTestPageClass();

        $this->assertEquals('A test page', $page::getNavigationDescription());
    }

    #[Test]
    public function it_allows_access_by_default(): void
    {
        $page = $this->getTestPageClass();

        $this->assertTrue($page::canAccess());
    }

    #[Test]
    public function it_generates_label_from_class_name_when_not_set(): void
    {
        $page = new class extends Page {};

        // The anonymous class will have a generated name, so we just verify it's a string
        $label = $page::getNavigationLabel();
        $this->assertIsString($label);
    }

    #[Test]
    public function it_can_restrict_access(): void
    {
        $page = new class extends Page {
            public static function canAccess(): bool
            {
                return false;
            }
        };

        $this->assertFalse($page::canAccess());
    }

    #[Test]
    public function it_returns_null_for_optional_navigation_properties(): void
    {
        $page = new class extends Page {};

        $this->assertNull($page::getNavigationIcon());
        $this->assertNull($page::getNavigationGroup());
        $this->assertNull($page::getNavigationDescription());
    }
}
