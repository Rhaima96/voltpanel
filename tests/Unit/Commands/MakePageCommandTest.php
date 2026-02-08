<?php

namespace Rhaima\VoltPanel\Tests\Unit\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Tests\TestCase;

class MakePageCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clean up created files
        $path = app_path('VoltPanel/Pages');
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        parent::tearDown();
    }

    #[Test]
    public function it_can_create_a_regular_page(): void
    {
        $result = Artisan::call('voltpanel:page', [
            'name' => 'TestPage'
        ]);

        $this->assertEquals(0, $result);
        $this->assertTrue(File::exists(app_path('VoltPanel/Pages/TestPage.php')));
    }

    #[Test]
    public function it_can_create_a_settings_page(): void
    {
        $result = Artisan::call('voltpanel:page', [
            'name' => 'CustomSettings',
            '--settings' => true
        ]);

        $this->assertEquals(0, $result);
        $this->assertTrue(File::exists(app_path('VoltPanel/Pages/CustomSettingsPage.php')));
    }

    #[Test]
    public function it_appends_page_suffix_if_missing(): void
    {
        Artisan::call('voltpanel:page', [
            'name' => 'Dashboard'
        ]);

        $this->assertTrue(File::exists(app_path('VoltPanel/Pages/DashboardPage.php')));
    }

    #[Test]
    public function it_fails_when_page_already_exists(): void
    {
        // Create page first time
        Artisan::call('voltpanel:page', [
            'name' => 'Duplicate'
        ]);

        // Try to create again
        $result = Artisan::call('voltpanel:page', [
            'name' => 'Duplicate'
        ]);

        $this->assertEquals(1, $result);
    }

    #[Test]
    public function it_creates_directory_if_not_exists(): void
    {
        // Ensure directory doesn't exist
        $path = app_path('VoltPanel/Pages');
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        Artisan::call('voltpanel:page', [
            'name' => 'NewPage'
        ]);

        $this->assertTrue(File::isDirectory($path));
        $this->assertTrue(File::exists(app_path('VoltPanel/Pages/NewPage.php')));
    }

    #[Test]
    public function it_generates_correct_namespace_for_regular_page(): void
    {
        Artisan::call('voltpanel:page', [
            'name' => 'RegularTest'
        ]);

        $content = File::get(app_path('VoltPanel/Pages/RegularTestPage.php'));

        $this->assertStringContainsString('namespace App\VoltPanel\Pages;', $content);
        $this->assertStringContainsString('use Rhaima\VoltPanel\Pages\Page;', $content);
        $this->assertStringContainsString('class RegularTestPage extends Page', $content);
    }

    #[Test]
    public function it_generates_correct_namespace_for_settings_page(): void
    {
        Artisan::call('voltpanel:page', [
            'name' => 'SettingsTest',
            '--settings' => true
        ]);

        $content = File::get(app_path('VoltPanel/Pages/SettingsTestPage.php'));

        $this->assertStringContainsString('namespace App\VoltPanel\Pages;', $content);
        $this->assertStringContainsString('use Rhaima\VoltPanel\Pages\SettingsPage;', $content);
        $this->assertStringContainsString('class SettingsTestPage extends SettingsPage', $content);
    }

    #[Test]
    public function it_includes_settings_method_in_settings_page(): void
    {
        Artisan::call('voltpanel:page', [
            'name' => 'AppSettings',
            '--settings' => true
        ]);

        $content = File::get(app_path('VoltPanel/Pages/AppSettingsPage.php'));

        $this->assertStringContainsString('public static function settings(): array', $content);
        $this->assertStringContainsString('Setting::make(', $content);
    }

    #[Test]
    public function it_generates_navigation_label_from_class_name(): void
    {
        Artisan::call('voltpanel:page', [
            'name' => 'UserManagement'
        ]);

        $content = File::get(app_path('VoltPanel/Pages/UserManagementPage.php'));

        $this->assertStringContainsString("'User Management'", $content);
    }
}
