<?php

namespace Rhaima\VoltPanel;

use Illuminate\Support\Facades\Route;
use Rhaima\VoltPanel\Commands\InstallCommand;
use Rhaima\VoltPanel\Commands\MakePageCommand;
use Rhaima\VoltPanel\Commands\MakePanelCommand;
use Rhaima\VoltPanel\Commands\MakeResourceCommand;
use Rhaima\VoltPanel\Commands\MakeWidgetCommand;
use Rhaima\VoltPanel\Commands\SetupCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VoltPanelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('voltpanel')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommands([
                // SetupCommand::class,
                InstallCommand::class,
                MakePanelCommand::class,
                MakeResourceCommand::class,
                MakePageCommand::class,
                MakeWidgetCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('voltpanel', function ($app) {
            return new VoltPanelManager($app);
        });

        $this->app->singleton('voltpanel.settings', function ($app) {
            return new \Rhaima\VoltPanel\Settings\SettingsManager;
        });

        $this->app->singleton('voltpanel.saved_filters', function ($app) {
            return new \Rhaima\VoltPanel\Filters\SavedFilterManager;
        });

        $this->app->singleton('voltpanel.tenancy', function ($app) {
            return new \Rhaima\VoltPanel\Tenancy\TenantManager;
        });

        $this->app->singleton('voltpanel.webhooks', function ($app) {
            return new \Rhaima\VoltPanel\Webhooks\WebhookManager;
        });
    }

    public function packageBooted(): void
    {
        // Register routes
        $this->registerRoutes();

        // Publish migrations (with duplicate detection and removal)
        $this->publishMigrationsWithDuplicateHandling();

        // Detect folder naming convention and publish Vue components accordingly
        $folders = $this->detectFolderConvention();

        $this->publishes([
            __DIR__ . '/../resources/js/Pages' => resource_path("js/{$folders['pages']}/VoltPanel"),
            __DIR__ . '/../resources/js/Components' => resource_path("js/{$folders['components']}/VoltPanel"),
            __DIR__ . '/../resources/js/Layouts' => resource_path("js/{$folders['layouts']}/VoltPanel"),
            __DIR__ . '/../resources/js/Composables' => resource_path("js/{$folders['composables']}/VoltPanel"),
        ], 'voltpanel-components');

        // Publish CSS (all versions - InstallCommand handles version detection)
        // Use 'php artisan voltpanel:install' for automatic Tailwind version detection
        $this->publishes([
            __DIR__ . '/../resources/css/voltpanel-tw3.css' => resource_path('css/vendor/voltpanel/voltpanel-tw3.css'),
            __DIR__ . '/../resources/css/voltpanel-tw4.css' => resource_path('css/vendor/voltpanel/voltpanel-tw4.css'),
            __DIR__ . '/../resources/css/voltpanel.css' => resource_path('css/vendor/voltpanel/voltpanel.css'),
        ], 'voltpanel-assets');
    }

    /**
     * Detect the folder naming convention used in the project.
     * Returns lowercase folders if official Laravel starter kit is detected,
     * otherwise returns capitalized folders (Breeze convention).
     */
    protected function detectFolderConvention(): array
    {
        $resourceJsPath = resource_path('js');

        // Get actual directory listing (case-sensitive)
        $actualDirs = array_filter(
            scandir($resourceJsPath) ?: [],
            fn($item) => $item !== '.' && $item !== '..' &&
                is_dir($resourceJsPath . DIRECTORY_SEPARATOR . $item)
        );

        // Check for exact lowercase folders
        if (
            in_array('pages', $actualDirs, true) ||
            in_array('components', $actualDirs, true) ||
            in_array('layouts', $actualDirs, true)
        ) {
            return [
                'pages' => 'pages',
                'components' => 'components',
                'layouts' => 'layouts',
                'composables' => 'composables',
            ];
        }

        // Default to capitalized convention
        return [
            'pages' => 'Pages',
            'components' => 'Components',
            'layouts' => 'Layouts',
            'composables' => 'Composables',
        ];
    }

    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => config('voltpanel.path', 'admin'),
            'middleware' => array_merge(
                config('voltpanel.middleware', ['web', 'auth']),
                [\Rhaima\VoltPanel\Http\Middleware\ShareInertiaData::class]
            ),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * Publish migrations with duplicate detection and removal.
     * If a migration with the same name (ignoring timestamp) already exists
     * in the project, it will be deleted and replaced with the package version.
     */
    protected function publishMigrationsWithDuplicateHandling(): void
    {
        $packageMigrationsPath = __DIR__ . '/../database/migrations';
        $projectMigrationsPath = database_path('migrations');

        // Register the publish callback that handles duplicates
        $this->publishes([
            $packageMigrationsPath => $projectMigrationsPath,
        ], 'voltpanel-migrations');

        // Only handle duplicates when actually publishing
        if ($this->app->runningInConsole()) {
            $this->app['events']->listen('Illuminate\Console\Events\CommandStarting', function ($event) use ($packageMigrationsPath, $projectMigrationsPath) {
                if ($event->command === 'vendor:publish') {
                    $this->removeDuplicateMigrations($packageMigrationsPath, $projectMigrationsPath);
                }
            });
        }
    }

    /**
     * Remove duplicate migrations from the project that match package migrations.
     * Matches migrations by name, ignoring the timestamp prefix.
     */
    protected function removeDuplicateMigrations(string $packagePath, string $projectPath): void
    {
        if (! is_dir($packagePath) || ! is_dir($projectPath)) {
            return;
        }

        // Get all package migration names (without timestamp)
        $packageMigrations = [];
        foreach (glob($packagePath . '/*.php') as $file) {
            $name = $this->getMigrationNameWithoutTimestamp(basename($file));
            if ($name) {
                $packageMigrations[] = $name;
            }
        }

        // Check project migrations for duplicates and remove them
        foreach (glob($projectPath . '/*.php') as $file) {
            $name = $this->getMigrationNameWithoutTimestamp(basename($file));
            if ($name && in_array($name, $packageMigrations)) {
                @unlink($file);
            }
        }
    }

    /**
     * Extract the migration name without the timestamp prefix.
     * e.g., "2024_01_01_000008_add_two_factor_columns_to_users_table.php"
     * returns "add_two_factor_columns_to_users_table"
     */
    protected function getMigrationNameWithoutTimestamp(string $filename): ?string
    {
        // Remove .php extension
        $name = preg_replace('/\.php$/', '', $filename);

        // Remove timestamp prefix (YYYY_MM_DD_HHMMSS_)
        if (preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_(.+)$/', $name, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
