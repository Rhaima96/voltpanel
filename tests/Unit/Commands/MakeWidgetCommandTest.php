<?php

namespace Rhaima\VoltPanel\Tests\Unit\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Tests\TestCase;

class MakeWidgetCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clean up created files
        $path = app_path('VoltPanel/Widgets');
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        parent::tearDown();
    }

    #[Test]
    public function it_can_create_a_base_widget(): void
    {
        $result = Artisan::call('voltpanel:widget', [
            'name' => 'TestWidget',
            '--type' => 'base'
        ]);

        $this->assertEquals(0, $result);
        $this->assertTrue(File::exists(app_path('VoltPanel/Widgets/TestWidget.php')));
    }

    #[Test]
    public function it_can_create_a_stats_widget(): void
    {
        $result = Artisan::call('voltpanel:widget', [
            'name' => 'StatsTest',
            '--type' => 'stats'
        ]);

        $this->assertEquals(0, $result);
        $this->assertTrue(File::exists(app_path('VoltPanel/Widgets/StatsTestWidget.php')));
    }

    #[Test]
    public function it_can_create_a_chart_widget(): void
    {
        $result = Artisan::call('voltpanel:widget', [
            'name' => 'ChartTest',
            '--type' => 'chart'
        ]);

        $this->assertEquals(0, $result);
        $this->assertTrue(File::exists(app_path('VoltPanel/Widgets/ChartTestWidget.php')));
    }

    #[Test]
    public function it_can_create_an_activity_widget(): void
    {
        $result = Artisan::call('voltpanel:widget', [
            'name' => 'ActivityTest',
            '--type' => 'activity'
        ]);

        $this->assertEquals(0, $result);
        $this->assertTrue(File::exists(app_path('VoltPanel/Widgets/ActivityTestWidget.php')));
    }

    #[Test]
    public function it_appends_widget_suffix_if_missing(): void
    {
        Artisan::call('voltpanel:widget', [
            'name' => 'Custom',
            '--type' => 'base'
        ]);

        $this->assertTrue(File::exists(app_path('VoltPanel/Widgets/CustomWidget.php')));
    }

    #[Test]
    public function it_fails_for_invalid_widget_type(): void
    {
        $result = Artisan::call('voltpanel:widget', [
            'name' => 'Invalid',
            '--type' => 'nonexistent'
        ]);

        $this->assertEquals(1, $result);
    }

    #[Test]
    public function it_fails_when_widget_already_exists(): void
    {
        // Create widget first time
        Artisan::call('voltpanel:widget', [
            'name' => 'Duplicate',
            '--type' => 'base'
        ]);

        // Try to create again
        $result = Artisan::call('voltpanel:widget', [
            'name' => 'Duplicate',
            '--type' => 'base'
        ]);

        $this->assertEquals(1, $result);
    }

    #[Test]
    public function it_creates_directory_if_not_exists(): void
    {
        // Ensure directory doesn't exist
        $path = app_path('VoltPanel/Widgets');
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        Artisan::call('voltpanel:widget', [
            'name' => 'NewWidget',
            '--type' => 'base'
        ]);

        $this->assertTrue(File::isDirectory($path));
        $this->assertTrue(File::exists(app_path('VoltPanel/Widgets/NewWidget.php')));
    }

    #[Test]
    public function it_generates_correct_namespace_in_widget_file(): void
    {
        Artisan::call('voltpanel:widget', [
            'name' => 'NamespaceTest',
            '--type' => 'base'
        ]);

        $content = File::get(app_path('VoltPanel/Widgets/NamespaceTestWidget.php'));

        $this->assertStringContainsString('namespace App\VoltPanel\Widgets;', $content);
        $this->assertStringContainsString('class NamespaceTestWidget', $content);
    }
}
