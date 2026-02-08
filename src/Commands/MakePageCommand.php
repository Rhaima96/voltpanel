<?php

namespace Rhaima\VoltPanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePageCommand extends Command
{
    protected $signature = 'voltpanel:page {name} {--settings : Create a settings page}';

    protected $description = 'Create a new VoltPanel page';

    public function handle(): int
    {
        $name = $this->argument('name');
        $className = Str::studly($name);
        $isSettings = $this->option('settings');

        // Ensure it ends with 'Page'
        if (!Str::endsWith($className, 'Page')) {
            $className .= 'Page';
        }

        $stub = $isSettings ? $this->getSettingsStub() : $this->getPageStub();
        $stub = str_replace('{{className}}', $className, $stub);

        // Extract label from class name
        $label = Str::of($className)
            ->beforeLast('Page')
            ->kebab()
            ->replace('-', ' ')
            ->title()
            ->toString();

        $stub = str_replace('{{label}}', $label, $stub);

        $path = app_path("VoltPanel/Pages/{$className}.php");

        if (file_exists($path)) {
            $this->error("Page {$className} already exists!");

            return self::FAILURE;
        }

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $stub);

        $pageType = $isSettings ? 'Settings page' : 'Page';
        $this->info("{$pageType} {$className} created successfully!");
        $this->info("Don't forget to register it in your Panel provider using ->pages([{$className}::class])");

        return self::SUCCESS;
    }

    protected function getPageStub(): string
    {
        return <<<'PHP'
<?php

namespace App\VoltPanel\Pages;

use Rhaima\VoltPanel\Pages\Page;

class {{className}} extends Page
{
    protected static ?string $navigationLabel = '{{label}}';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = null;
    protected static ?string $navigationDescription = null;

    public static function canAccess(): bool
    {
        return true;
    }

    // Add your custom page logic here
}
PHP;
    }

    protected function getSettingsStub(): string
    {
        return <<<'PHP'
<?php

namespace App\VoltPanel\Pages;

use Rhaima\VoltPanel\Pages\SettingsPage;
use Rhaima\VoltPanel\Settings\Setting;

class {{className}} extends SettingsPage
{
    protected static ?string $navigationLabel = '{{label}}';
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 100;
    protected static ?string $navigationDescription = 'Configure {{label}} settings';

    public static function settings(): array
    {
        return [
            Setting::make('example_setting')
                ->label('Example Setting')
                ->description('This is an example setting')
                ->type('text')
                ->default('default value')
                ->group('general'),

            Setting::make('example_toggle')
                ->label('Example Toggle')
                ->description('Enable or disable a feature')
                ->type('toggle')
                ->default(false)
                ->group('general'),

            // Add more settings here...
        ];
    }
}
PHP;
    }
}
