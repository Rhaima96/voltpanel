<?php

namespace Rhaima\VoltPanel\Pages;

use Rhaima\VoltPanel\Forms\Form;
use Rhaima\VoltPanel\Settings\Setting;
use Illuminate\Support\Collection;

abstract class SettingsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Settings';

    abstract public static function settings(): array;

    public static function getSettings(): Collection
    {
        return collect(static::settings());
    }

    public function getForm(): Form
    {
        $form = Form::make();
        $schema = [];

        foreach (static::getSettings() as $setting) {
            if (!$setting instanceof Setting) {
                continue;
            }

            $settingData = $setting->toArray();
            $componentClass = $this->getComponentClassForType($settingData['type']);

            if (!$componentClass) {
                continue;
            }

            $component = $componentClass::make($settingData['key'])
                ->label($settingData['label'] ?? str_replace('_', ' ', ucfirst($settingData['key'])))
                ->default($settingData['value']);

            if ($settingData['description']) {
                $component->helperText($settingData['description']);
            }

            $schema[] = $component;
        }

        return $form->schema($schema);
    }

    protected function getComponentClassForType(string $type): ?string
    {
        return match($type) {
            'text', 'email', 'url', 'tel' => \Rhaima\VoltPanel\Forms\Components\TextInput::class,
            'textarea' => \Rhaima\VoltPanel\Forms\Components\Textarea::class,
            'select' => \Rhaima\VoltPanel\Forms\Components\Select::class,
            'toggle', 'boolean' => \Rhaima\VoltPanel\Forms\Components\Toggle::class,
            'date', 'datetime' => \Rhaima\VoltPanel\Forms\Components\DatePicker::class,
            'color' => \Rhaima\VoltPanel\Forms\Components\ColorPicker::class,
            'checkbox' => \Rhaima\VoltPanel\Forms\Components\Checkbox::class,
            'radio' => \Rhaima\VoltPanel\Forms\Components\Radio::class,
            'file' => \Rhaima\VoltPanel\Forms\Components\FileUpload::class,
            'rich' => \Rhaima\VoltPanel\Forms\Components\RichEditor::class,
            default => null,
        };
    }

    public function save(array $data): void
    {
        foreach (static::getSettings() as $setting) {
            if (!$setting instanceof Setting) {
                continue;
            }

            $key = $setting->toArray()['key'];

            if (array_key_exists($key, $data)) {
                $setting->set($data[$key]);
            }
        }

        session()->flash('success', 'Settings saved successfully!');
    }
}
