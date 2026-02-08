<?php

namespace Rhaima\VoltPanel\Tests\Unit\Pages;

use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Pages\SettingsPage;
use Rhaima\VoltPanel\Settings\Setting;
use Rhaima\VoltPanel\Tests\TestCase;

class SettingsPageTest extends TestCase
{

    protected function getTestSettingsPage(): SettingsPage
    {
        return new class extends SettingsPage {
            public static function settings(): array
            {
                return [
                    Setting::make('site_name')
                        ->type('text')
                        ->label('Site Name')
                        ->description('The name of your site')
                        ->default('My Site'),

                    Setting::make('site_enabled')
                        ->type('toggle')
                        ->label('Site Enabled')
                        ->default(true),
                ];
            }

            // Make protected method public for testing
            public function getComponentClassForType(string $type): ?string
            {
                return parent::getComponentClassForType($type);
            }
        };
    }

    #[Test]
    public function it_has_cog_icon_by_default(): void
    {
        $page = $this->getTestSettingsPage();

        $this->assertEquals('heroicon-o-cog', $page::getNavigationIcon());
    }

    #[Test]
    public function it_has_settings_group_by_default(): void
    {
        $page = $this->getTestSettingsPage();

        $this->assertEquals('Settings', $page::getNavigationGroup());
    }

    #[Test]
    public function it_can_get_settings_as_collection(): void
    {
        $page = $this->getTestSettingsPage();
        $settings = $page::getSettings();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $settings);
        $this->assertCount(2, $settings);
    }

    #[Test]
    public function it_maps_text_type_to_text_input_component(): void
    {
        $page = $this->getTestSettingsPage();
        $componentClass = $page->getComponentClassForType('text');

        $this->assertEquals(\Rhaima\VoltPanel\Forms\Components\TextInput::class, $componentClass);
    }

    #[Test]
    public function it_maps_email_type_to_text_input_component(): void
    {
        $page = $this->getTestSettingsPage();
        $componentClass = $page->getComponentClassForType('email');

        $this->assertEquals(\Rhaima\VoltPanel\Forms\Components\TextInput::class, $componentClass);
    }

    #[Test]
    public function it_maps_toggle_type_to_toggle_component(): void
    {
        $page = $this->getTestSettingsPage();
        $componentClass = $page->getComponentClassForType('toggle');

        $this->assertEquals(\Rhaima\VoltPanel\Forms\Components\Toggle::class, $componentClass);
    }

    #[Test]
    public function it_maps_select_type_to_select_component(): void
    {
        $page = $this->getTestSettingsPage();
        $componentClass = $page->getComponentClassForType('select');

        $this->assertEquals(\Rhaima\VoltPanel\Forms\Components\Select::class, $componentClass);
    }

    #[Test]
    public function it_maps_textarea_type_to_textarea_component(): void
    {
        $page = $this->getTestSettingsPage();
        $componentClass = $page->getComponentClassForType('textarea');

        $this->assertEquals(\Rhaima\VoltPanel\Forms\Components\Textarea::class, $componentClass);
    }

    #[Test]
    public function it_maps_date_type_to_date_picker_component(): void
    {
        $page = $this->getTestSettingsPage();
        $componentClass = $page->getComponentClassForType('date');

        $this->assertEquals(\Rhaima\VoltPanel\Forms\Components\DatePicker::class, $componentClass);
    }

    #[Test]
    public function it_maps_color_type_to_color_picker_component(): void
    {
        $page = $this->getTestSettingsPage();
        $componentClass = $page->getComponentClassForType('color');

        $this->assertEquals(\Rhaima\VoltPanel\Forms\Components\ColorPicker::class, $componentClass);
    }

    #[Test]
    public function it_maps_rich_type_to_rich_editor_component(): void
    {
        $page = $this->getTestSettingsPage();
        $componentClass = $page->getComponentClassForType('rich');

        $this->assertEquals(\Rhaima\VoltPanel\Forms\Components\RichEditor::class, $componentClass);
    }

    #[Test]
    public function it_returns_null_for_unknown_component_type(): void
    {
        $page = $this->getTestSettingsPage();
        $componentClass = $page->getComponentClassForType('unknown_type');

        $this->assertNull($componentClass);
    }

}
