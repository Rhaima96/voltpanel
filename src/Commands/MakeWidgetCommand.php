<?php

namespace Rhaima\VoltPanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeWidgetCommand extends Command
{
    protected $signature = 'voltpanel:widget {name} {--type=base : The type of widget (base, stats, chart, activity)}';

    protected $description = 'Create a new VoltPanel widget';

    protected array $widgetTypes = [
        'base' => 'Widget',
        'stats' => 'StatsOverviewWidget',
        'chart' => 'ChartWidget',
        'activity' => 'ActivityLogWidget',
    ];

    public function handle(): int
    {
        $name = $this->argument('name');
        $className = Str::studly($name);

        // Ensure the name ends with "Widget"
        if (! Str::endsWith($className, 'Widget')) {
            $className .= 'Widget';
        }

        $type = $this->option('type');

        if (! array_key_exists($type, $this->widgetTypes)) {
            $this->error("Invalid widget type '{$type}'. Available types: " . implode(', ', array_keys($this->widgetTypes)));

            return self::FAILURE;
        }

        $stub = $this->getStub($type);
        $stub = str_replace('{{className}}', $className, $stub);
        $stub = str_replace('{{extends}}', $this->widgetTypes[$type], $stub);

        $path = app_path("VoltPanel/Widgets/{$className}.php");

        if (file_exists($path)) {
            $this->error("Widget {$className} already exists!");

            return self::FAILURE;
        }

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $stub);

        $this->info("Widget {$className} created successfully!");
        $this->info("Type: {$this->widgetTypes[$type]}");
        $this->info("Path: {$path}");

        return self::SUCCESS;
    }

    protected function getStub(string $type): string
    {
        return match ($type) {
            'stats' => $this->getStatsWidgetStub(),
            'chart' => $this->getChartWidgetStub(),
            'activity' => $this->getActivityWidgetStub(),
            default => $this->getBaseWidgetStub(),
        };
    }

    protected function getBaseWidgetStub(): string
    {
        return <<<'PHP'
<?php

namespace App\VoltPanel\Widgets;

use Rhaima\VoltPanel\Widgets\{{extends}};

class {{className}} extends {{extends}}
{
    protected static ?string $view = null;
    protected static ?int $sort = 0;

    protected ?string $heading = null;

    public static function make(): static
    {
        return new static();
    }

    public function heading(string $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getData(): array
    {
        return [
            'heading' => $this->heading,
            // Add your widget data here
        ];
    }
}
PHP;
    }

    protected function getStatsWidgetStub(): string
    {
        return <<<'PHP'
<?php

namespace App\VoltPanel\Widgets;

use Rhaima\VoltPanel\Widgets\{{extends}};

class {{className}} extends {{extends}}
{
    protected static ?int $sort = 0;

    public static function make(): static
    {
        return new static();
    }

    public function getData(): array
    {
        return [
            'heading' => $this->heading,
            'value' => $this->value,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'url' => $this->url,
        ];
    }
}
PHP;
    }

    protected function getChartWidgetStub(): string
    {
        return <<<'PHP'
<?php

namespace App\VoltPanel\Widgets;

use Rhaima\VoltPanel\Widgets\{{extends}};

class {{className}} extends {{extends}}
{
    protected static ?int $sort = 0;

    public static function make(): static
    {
        return new static();
    }

    public function getData(): array
    {
        // Example data - customize for your needs
        return [
            'heading' => $this->heading,
            'type' => $this->type, // 'line', 'bar', 'pie', 'doughnut', etc.
            'datasets' => $this->datasets,
            'labels' => $this->labels,
            'options' => $this->options,
        ];
    }
}
PHP;
    }

    protected function getActivityWidgetStub(): string
    {
        return <<<'PHP'
<?php

namespace App\VoltPanel\Widgets;

use Rhaima\VoltPanel\Widgets\{{extends}};

class {{className}} extends {{extends}}
{
    protected static ?int $sort = 0;

    public static function make(): static
    {
        return new static();
    }

    public function getData(): array
    {
        $activityModel = config('voltpanel.activity_log.model', \Rhaima\VoltPanel\Models\Activity::class);

        $query = $activityModel::query()
            ->with(['causer', 'subject'])
            ->latest();

        if ($this->logName) {
            $query->where('log_name', $this->logName);
        }

        if ($this->subjectType) {
            $query->where('subject_type', $this->subjectType);
        }

        $activities = $query->limit($this->limit)->get();

        return [
            'activities' => $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'event' => $activity->event,
                    'causer' => $activity->causer?->name ?? 'System',
                    'subject_type' => class_basename($activity->subject_type),
                    'created_at' => $activity->created_at->diffForHumans(),
                    'properties' => $activity->properties,
                ];
            }),
            'limit' => $this->limit,
        ];
    }
}
PHP;
    }
}
