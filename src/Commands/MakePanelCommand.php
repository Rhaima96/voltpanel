<?php

namespace Rhaima\VoltPanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePanelCommand extends Command
{
    protected $signature = 'voltpanel:panel {name}';

    protected $description = 'Create a new VoltPanel panel';

    public function handle(): int
    {
        $name = $this->argument('name');
        $className = Str::studly($name);

        $stub = $this->getStub();
        $stub = str_replace('{{className}}', $className, $stub);

        $path = app_path("Panels/{$className}.php");

        if (file_exists($path)) {
            $this->error("Panel {$className} already exists!");

            return self::FAILURE;
        }

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $stub);

        $this->info("Panel {$className} created successfully!");
        $this->line("Register it in your AppServiceProvider:");
        $this->line("  VoltPanel::panel('{$name}')->resources([...]);");

        return self::SUCCESS;
    }

    protected function getStub(): string
    {
        return <<<'PHP'
<?php

namespace App\Panels;

use Rhaima\VoltPanel\Panels\Panel;

class {{className}} extends Panel
{
    public function __construct()
    {
        parent::__construct('{{className}}');

        $this->path('admin')
            ->brandName('Admin Panel')
            ->resources([
                // Add your resources here
            ])
            ->pages([
                // Add your pages here
            ]);
    }
}
PHP;
    }
}
