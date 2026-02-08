<?php

namespace Rhaima\VoltPanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class InstallCommand extends Command
{
    protected $signature = 'voltpanel:install
                            {--force : Overwrite existing files}
                            {--no-deps : Skip installing dependencies}
                            {--composer : Install composer dependencies only}
                            {--npm : Install npm dependencies only}
                            {--typescript : Use TypeScript instead of JavaScript}
                            {--javascript : Use JavaScript instead of TypeScript}';

    protected $description = 'Install VoltPanel package (publishes config, migrations, and installs dependencies)';

    protected bool $useTypeScript = false;

    protected int $tailwindVersion = 3;

    public function handle(): int
    {
        $this->components->info('Installing VoltPanel...');
        $this->newLine();

        // Detect Tailwind version
        $this->tailwindVersion = $this->detectTailwindVersion();
        $this->components->info(
            "Detected Tailwind CSS v{$this->tailwindVersion}"
        );

        // Ask for language preference if not specified
        if (! $this->option('typescript') && ! $this->option('javascript')) {
            $choice = $this->choice(
                'Which language would you like to use?',
                ['JavaScript', 'TypeScript'],
                0
            );
            $this->useTypeScript = $choice === 'TypeScript';
        } else {
            $this->useTypeScript = $this->option('typescript');
        }

        $this->components->info(
            'Using ' . ($this->useTypeScript ? 'TypeScript' : 'JavaScript') . ' for VoltPanel components'
        );

        // Detect and inform about folder convention
        $folderConvention = $this->detectFolderConvention();
        $this->components->info(
            'Detected ' . $folderConvention['type'] . ' folder convention'
        );
        $this->newLine();

        // Step 1: Install Composer dependencies
        if (! $this->option('no-deps') && ! $this->option('npm')) {
            $this->installComposerDependencies();
        }

        // Step 2: Install npm dependencies
        if (! $this->option('no-deps') && ! $this->option('composer')) {
            $this->installNpmDependencies();
        }

        $this->newLine();

        // Step 3: Publish configuration
        $this->components->task('Publishing configuration', function () {
            $this->callSilent('vendor:publish', [
                '--tag' => 'voltpanel-config',
                '--force' => $this->option('force'),
            ]);
        });

        // Step 4: Publish migrations
        $this->components->task('Publishing migrations', function () {
            $this->callSilent('vendor:publish', [
                '--tag' => 'voltpanel-migrations',
                '--force' => $this->option('force'),
            ]);
        });

        // Step 5: Publish Vue components (required for Inertia)
        $this->components->task('Publishing Vue components', function () {
            $this->callSilent('vendor:publish', [
                '--tag' => 'voltpanel-components',
                '--force' => $this->option('force'),
            ]);
        });

        // Step 5.1: Fix import paths for capitalized folder convention (Breeze)
        $this->fixImportPathsForBreeze();

        // Step 5.5: Clean up unnecessary language files
        $this->cleanupLanguageFiles();

        // Step 6: Publish CSS assets (with Tailwind version detection)
        if ($this->confirm('Would you like to publish CSS assets?', false)) {
            $this->publishCssAssets();
        }



        $this->newLine();

        // Step 7: Run migrations
        if ($this->confirm('Would you like to run the migrations now?', true)) {
            $this->newLine();
            $this->components->info('Running migrations...');
            $this->call('migrate');
        } else {
            $this->components->warn('Remember to run migrations later: php artisan migrate');
        }

        $this->newLine();
        $this->components->info('VoltPanel installed successfully! 🎉');
        $this->newLine();

        // Display next steps
        $this->line('<fg=gray>Next steps:</>');
        $this->line('  <fg=yellow>1.</> Create a panel: <fg=cyan>php artisan voltpanel:panel AdminPanel</>');
        $this->line('  <fg=yellow>2.</> Create a resource: <fg=cyan>php artisan voltpanel:resource UserResource</>');
        $this->line('  <fg=yellow>3.</> Visit your admin panel: <fg=cyan>http://your-app.test/admin</>');
        $this->newLine();

        // Display published files info
        $this->components->info('Published files:');
        $this->line('  <fg=green>✓</> Config: <fg=gray>config/voltpanel.php</>');
        $this->line('  <fg=green>✓</> Migrations: <fg=gray>database/migrations/2024_01_01_*_voltpanel_*.php</>');
        $this->line('  <fg=green>✓</> Vue Components: <fg=gray>resources/js/Pages/VoltPanel/</>');
        $this->line('  <fg=green>✓</> Vue Components: <fg=gray>resources/js/Components/VoltPanel/</>');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Detect the installed Tailwind CSS version.
     */
    protected function detectTailwindVersion(): int
    {
        $packageJsonPath = base_path('package.json');

        if (! File::exists($packageJsonPath)) {
            $this->components->warn('package.json not found, assuming Tailwind CSS v3');

            return 3;
        }

        $packageJson = json_decode(File::get($packageJsonPath), true);

        // Check dependencies and devDependencies
        $dependencies = array_merge(
            $packageJson['dependencies'] ?? [],
            $packageJson['devDependencies'] ?? []
        );

        // Check for tailwind.config.js (v3 pattern)
        if (
            File::exists(base_path('tailwind.config.js')) ||
            File::exists(base_path('tailwind.config.ts'))
        ) {
            // Having a config file doesn't necessarily mean v3,
            // but combined with no v4 indicators, it's likely v3
            // Delete tailwind.config.js to avoid confusion and add tailwind.config.js from VoltPanel package

            File::delete(base_path('tailwind.config.js'));
            File::copy(__DIR__ . '/../../resources/config/tailwind.config.js', base_path('tailwind.config.js'));
            return 3;
        }

        // Check for @tailwindcss/vite (v4 specific)
        if (isset($dependencies['@tailwindcss/vite'])) {
            return 4;
        }

        // Check tailwindcss version
        if (isset($dependencies['tailwindcss'])) {
            $version = $dependencies['tailwindcss'];
            // Remove ^ or ~ prefix
            $version = ltrim($version, '^~>=<');

            // Extract major version
            $majorVersion = (int) explode('.', $version)[0];

            if ($majorVersion >= 4) {
                return 4;
            }
        }

        // Check for Tailwind v4 config patterns
        $appCssPath = resource_path('css/app.css');
        if (File::exists($appCssPath)) {
            $appCss = File::get($appCssPath);

            // Tailwind v4 uses @import 'tailwindcss' or @theme
            if (
                str_contains($appCss, "@import 'tailwindcss'") ||
                str_contains($appCss, '@import "tailwindcss"') ||
                str_contains($appCss, '@theme')
            ) {
                return 4;
            }
        }

        // Default to v3 for safety
        return 3;
    }

    /**
     * Publish CSS assets based on detected Tailwind version.
     */
    protected function publishCssAssets(): void
    {
        $this->components->task(
            "Publishing CSS assets for Tailwind CSS v{$this->tailwindVersion}",
            function () {
                $sourcePath = $this->tailwindVersion >= 4
                    ? __DIR__ . '/../../resources/css/voltpanel-tw4.css'
                    : __DIR__ . '/../../resources/css/voltpanel-tw3.css';

                $destinationDir = resource_path('css/vendor/voltpanel');
                $destinationPath = $destinationDir . '/voltpanel.css';

                // Ensure directory exists
                if (! File::exists($destinationDir)) {
                    File::makeDirectory($destinationDir, 0755, true);
                }

                // Copy the appropriate CSS file
                File::copy($sourcePath, $destinationPath);

                return true;
            }
        );

        $this->newLine();
        $this->components->info(
            "CSS assets published for Tailwind CSS v{$this->tailwindVersion}"
        );
        $this->line('  <fg=gray>Add to your app.css:</> <fg=cyan>@import \'./vendor/voltpanel/voltpanel.css\';</>');
        $this->newLine();
        if ($this->tailwindVersion == 4) {
            $lines = [
                '@import "./vendor/voltpanel/voltpanel.css";',
                '@import "tailwindcss";',
                '@import "tw-animate-css";',
                'the rest of your Tailwind CSS imports...',
            ];
        } else {
            $lines = [
                '@import "vendor/voltpanel/voltpanel.css";',
                '@tailwind base;',
                '@tailwind components;',
                '@tailwind utilities;',
            ];
        }


        foreach ($lines as $line) {
            $this->line("  <fg=gray>{$line}</>");
        }
    }

    protected function installComposerDependencies(): void
    {
        $this->components->info('Installing Composer dependencies...');

        $dependencies = [
            'inertiajs/inertia-laravel' => '^2.0',
            'pragmarx/google2fa' => '^8.0',
            'openspout/openspout' => '^4.0',
            'dompdf/dompdf' => '^3.0',
        ];

        foreach ($dependencies as $package => $version) {
            $this->components->task("Installing {$package}", function () use ($package, $version) {
                $result = Process::path(base_path())
                    ->timeout(300) // 5 minutes timeout for large packages
                    ->run("composer require {$package}:{$version} --no-interaction");

                return $result->successful();
            });
        }

        $this->newLine();
    }

    protected function installNpmDependencies(): void
    {
        $this->components->info('Installing npm dependencies...');

        // Detect package manager
        $packageManager = $this->detectPackageManager();

        // Base dependencies for both versions
        $dependencies = [
            '@inertiajs/vue3',
            '@tiptap/extension-color',
            '@tiptap/extension-highlight',
            '@tiptap/extension-image',
            '@tiptap/extension-link',
            '@tiptap/extension-text-align',
            '@tiptap/extension-text-style',
            '@tiptap/extension-underline',
            '@tiptap/extension-youtube',
            '@tiptap/pm',
            '@tiptap/starter-kit',
            '@tiptap/vue-3',
            '@vitejs/plugin-vue',
            'autoprefixer',
            'axios',
            'chart.js',
            'chartjs-adapter-date-fns',
            'class-variance-authority',
            'clsx',
            'date-fns',
            'laravel-vite-plugin',
            'postcss',
            'radix-vue',
            'shadcn-vue',
            'tailwind-merge',
            'tailwindcss-animate',
            'tiptap-extension-resize-image',
            'vite',
            'vue',
            'vue-chartjs',
        ];

        // Add Tailwind version-specific dependencies
        if ($this->tailwindVersion >= 4) {
            $dependencies[] = 'tailwindcss@^4.0';
            $dependencies[] = '@tailwindcss/vite';
        } else {
            $dependencies[] = 'tailwindcss@^3.4';
            $dependencies[] = '@tailwindcss/forms';
        }

        // Add TypeScript dependencies if user chose TypeScript
        if ($this->useTypeScript) {
            $dependencies = array_merge($dependencies, [
                '@types/node',
                'typescript',
                'vue-tsc',
            ]);
        }

        $command = match ($packageManager) {
            'yarn' => 'yarn add -D ' . implode(' ', $dependencies),
            'pnpm' => 'pnpm add -D ' . implode(' ', $dependencies),
            default => 'npm install --save-dev ' . implode(' ', $dependencies),
        };

        $this->components->task("Installing npm packages with {$packageManager}", function () use ($command) {
            $result = Process::path(base_path())
                ->timeout(300) // 5 minutes timeout
                ->run($command);

            return $result->successful();
        });

        $this->newLine();
    }

    protected function cleanupLanguageFiles(): void
    {
        $this->components->task('Cleaning up language files', function () {
            $folderConvention = $this->detectFolderConvention();
            $composablesFolder = $folderConvention['composables'];
            $composablesPath = resource_path("js/{$composablesFolder}/VoltPanel");

            if (! file_exists($composablesPath)) {
                return true;
            }

            // Remove TypeScript files if user chose JavaScript
            if (! $this->useTypeScript) {
                $tsFiles = glob("{$composablesPath}/*.ts");
                foreach ($tsFiles as $file) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }

                // Remove types directory
                $typesPath = resource_path('js/types');
                if (file_exists($typesPath)) {
                    $this->deleteDirectory($typesPath);
                }
            } else {
                // Remove JavaScript files if user chose TypeScript
                $jsFiles = glob("{$composablesPath}/*.js");
                foreach ($jsFiles as $file) {
                    // Don't remove if there's no .ts equivalent
                    $tsVersion = str_replace('.js', '.ts', $file);
                    if (file_exists($file) && file_exists($tsVersion)) {
                        unlink($file);
                    }
                }
            }

            return true;
        });
    }

    protected function deleteDirectory(string $dir): bool
    {
        if (! file_exists($dir)) {
            return true;
        }

        if (! is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (! $this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    protected function detectPackageManager(): string
    {
        $basePath = base_path();

        if (file_exists("{$basePath}/pnpm-lock.yaml")) {
            return 'pnpm';
        }

        if (file_exists("{$basePath}/yarn.lock")) {
            return 'yarn';
        }

        return 'npm';
    }

    /**
     * Detect the folder naming convention used in the project.
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
                'type' => 'lowercase',
                'pages' => 'pages',
                'components' => 'components',
                'layouts' => 'layouts',
                'composables' => 'composables',
            ];
        }

        // Default to capitalized convention
        return [
            'type' => 'capitalized',
            'pages' => 'Pages',
            'components' => 'Components',
            'layouts' => 'Layouts',
            'composables' => 'Composables',
        ];
    }

    /**
     * Fix import paths in published Vue files for Breeze (capitalized folders).
     */
    protected function fixImportPathsForBreeze(): void
    {
        $this->components->task('Adjusting import paths for folder convention', function () {
            $folderConvention = $this->detectFolderConvention();

            // Only fix if using capitalized convention (Breeze)
            if ($folderConvention['type'] === 'lowercase') {
                return true;
            }

            $resourceJsPath = resource_path('js');
            $foldersToFix = ['Pages', 'Components', 'Layouts'];

            foreach ($foldersToFix as $folder) {
                $vueFilesPath = $resourceJsPath . '/' . $folder . '/VoltPanel';

                if (! is_dir($vueFilesPath)) {
                    continue;
                }

                // Get all .vue files recursively
                $vueFiles = $this->getVueFilesRecursively($vueFilesPath);

                foreach ($vueFiles as $file) {
                    $content = file_get_contents($file);

                    // Replace lowercase imports with capitalized for Breeze
                    $content = str_replace('/pages/', '/Pages/', $content);
                    $content = str_replace('/components/', '/Components/', $content);
                    $content = str_replace('/layouts/', '/Layouts/', $content);
                    $content = str_replace('/composables/', '/Composables/', $content);

                    file_put_contents($file, $content);
                }
            }

            return true;
        });
    }

    /**
     * Get all .vue files recursively from a directory.
     */
    protected function getVueFilesRecursively(string $directory): array
    {
        $files = [];

        if (! is_dir($directory)) {
            return $files;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'vue') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
